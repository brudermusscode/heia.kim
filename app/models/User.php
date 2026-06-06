<?php

namespace Heiakim\Model;

use Locale;
use Heiakim\Justin;
use Heiakim\Geo\Geo;
use Heiakim\Http\Request;
use Heiakim\API\Bancho;
use Heiakim\APIGateway\Count\BeatmapsGateway;
use Heiakim\APIGateway\Score\ScoresGateway;
use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Database\Redis;
use Heiakim\Database\Manager as DBM;
use Heiakim\Enum\Privilege;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Validate\Validate;
use Heiakim\Validate\Image as ValidateImage;
use Heiakim\Model\Session;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Image;
use Heiakim\Model\Search;
use Heiakim\Model\Change;
use Heiakim\Model\Score;
use Heiakim\Model\User\UserPin;
use Heiakim\Model\User\UserSettings;
use Heiakim\Model\User\UserSettingsPrivacy;
use Heiakim\Model\Connect\Connect;
use Heiakim\Model\Connect\ConnectDiscord;
use Heiakim\Model\Connect\ConnectGoogle;
use Heiakim\Model\Connect\ConnectOsu;
use Heiakim\Model\Manager\ManagerAuthentication;
use Heiakim\Model\Manager\ManagerLog;
use Heiakim\Model\Manager\ManagerSession;
use Heiakim\Model\Manager\ManagerUser;
use Heiakim\Model\Osu\OsuFavorite;
use Heiakim\Model\Osu\OsuIngameLogin;
use Heiakim\Model\Osu\OsuRating;
use Heiakim\Model\Beatmap\BeatmapRequest;
use Heiakim\Model\Relationship;
use Heiakim\Model\Restriction\Restriction;
use Heiakim\Model\Restriction\RestrictionAppeal;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Model\Squad\SquadPostPollAnswer;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Model\Stat\StatDevelopment;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Model\Thread\Thread;
use Heiakim\Model\Thread\ThreadPost;
use Heiakim\Mail\Mail;
use Heiakim\Utils\Utils;
use Heiakim\Time\Time;
use Heiakim\Utils\Arr;
use Heiakim\Utils\Str;
use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Justin
{

  /**
   * @var array
   */
  protected $fillable = [
    "id",
    "name",
    "safe_name",
    "email",
    "priv",
    "pw_bcrypt",
    "country",
    "silence_end",
    "donor_end",
    "creation_time",
    "latest_activity",
    "clan_id",
    "preferred_mode",
    "play_style",
    "custom_badge_name",
    "custom_badge_icon",
    "userpage_content",
    "api_key",
    "remote_address",
    "frozen_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected $attributes = [
    "id" => 0,
    "priv" => 0,
    "name" => "Lovely",
    "safe_name" => "lovely",
    "country" => "xx",
    "email" => "lol@lol.lol",
    "clan_id" => 0,
  ];

  public static array $disallowed_names = ["mumei no"];

  public static array $name_length = [1, 16];

  public static string $name_regex = '/^[a-zA-Z0-9_\-\[\] ]+$/';

  public static string $password_regex = '/^(?=.{6,32}$)[\w\d\-_$,.:;#+?=!&%§{}><\'"\[\]\/]+$/u';

  /**
   * @param object $params
   * @return object
   *
   * NOTE: Will die on error.
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $User = self::make();

    # ? Name + Safe Name
    $User->set_name_invalid($params->name);

    # ? E-Mail
    $User->set_mail_invalid($params->email);

    # ? Password
    # Need to decode special chars as the Controller will automatically
    # encode everything.
    $password = htmlspecialchars_decode($params->password);
    $User->set_password_invalid($password);

    $ip = Request::get_remote_address();
    $time = time();

    # Set the rest.
    $User->remote_address = $ip;
    $User->country = Geo::country_code($ip);
    $User->creation_time = $time;
    $User->latest_activity = $time;

    # Begin the database transaction! Nothing to be left behind.
    $User->db_transaction();

    try {

      # Save it!
      $User->save();

      # Create UserSettings.
      $User->settings()->create([
        "birthday" => null,
        "is_legit" => $params->is_legit,
      ]);

      # Create UserPrivacySettings
      $User->privacy()->create([
        "accepts_policies" => 1,
        "is_public" => 1,
        "image_history" => 1,
      ]);

      # Create Profile.
      $User->create_default_profile();

      # Create Stats for each gamemode 0-7(8)
      for ($i = 0; $i <= 7; $i++) {
        if ($i == 7) {
          $i = 8;
        }

        $User->stats()->create([
          "mode" => $i,
        ]);
      }

      # TODO: Upload a picture. Filename has to be the user id.

      $User->db_commit();

      return $User;
    } catch (\Exception $e) {

      Logger::to_file($e);
      $User->db_rollback();

      return die(error($e->getMessage()));
    }
  }

  /**
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {
    /**
     * @var ?Change
     */
    $Change = null;

    /**
     * * Password
     */
    if (isset($params->password, $params->current_password)) {
      $current_password = htmlspecialchars_decode($params->current_password);
      $User = User::verify_login($this->name, $current_password);

      /**
       * Password is wrong?
       */
      if (!$User)
        return $this->error("<strong>Your credentials seem to be wrong!</strong>");

      /**
       * Current set password is matching?
       */
      if ($params->current_password == $params->password)
        return $this->error("<strong>This is your current password.</strong> Choose another one!");

      /**
       * @var string
       */
      $password = htmlspecialchars_decode($params->password);

      /**
       * Check all previous passwords.
       */
      foreach ($this->password_changes()->get() as $Change) {
        if (
          self::decrypt_password($password, $Change->previous_value)
        ) {
          return $this->error(
            "<strong>You have used this password before.</strong> Please choose another one."
          );
        }
      }

      /**
       * Password is of valid length?
       */
      if (Str::length($password, 6, 36)) {
        return $this->error(
          "<strong>Your password should be between 6 - 36 characters long.</strong>"
        );
      }

      /**
       * Encrypt password and add it to the params object.
       */
      $this->pw_bcrypt = self::encrypt_password($password);

      /**
       * Precreate a password change entry.
       *
       * @var Change
       */
      $Change = $this->changes()->make([
        "type" => "password",
        "previous_value" => $this->pw_bcrypt,
        "updated_value" => $params->pw_bcrypt,
        "updated_at" => null,
      ]);
    }

    /**
     * * Mail
     */
    if (isset($params->email)) {
      /**
       * Mail is invalid?
       */
      if (!filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
        return $this->error("<strong>Your mail is invalid</strong> 😆");
      }

      /**
       * @var ?User
       */
      $UserWithMail = self::where("email", $params->email)->first();

      if ($UserWithMail) {
        return $this->error(
          "<strong>This e-mail address is in use already.</strong>"
        );
      }

      /**
       * Set the new mail.
       */
      $this->email = $params->email;
    }

    /**
     * ? Name
     */
    if (isset($params->name)) {
      /**
       * Has name changes left?
       */
      if ($this->settings->name_changes_left < 1) {
        return request_error(
          "<strong>No name changes left!</strong> " .
            $this->dd["UNLOCK_MORE_WITH_PREMIUM"]
        );
      }

      /**
       * Is current name?
       */
      if ($this->name === $params->name) {
        return request_error("<strong>This is your name!</strong>");
      }

      /**
       * Validate the new name.
       */
      $this->validate_name($params->name, include_former_names: true);

      /**
       * Check if the user has had this name before. If not, we
       * need to check further for other users that could have had
       * this name.
       *
       * @var ?Change
       */
      $Change = $this->name_changes()
        ->where("previous_value", $params->name)
        ->first();

      if (!$Change) {
        /**
         * @var ?Change
         */
        $Change = Change::where("type", "name")
          ->where("previous_value", $params->name)
          ->whereNot("user_id", $this->id)
          ->first();

        /**
         * If a name change exists, this name is a former name of
         * another player and thus is not available for the
         * current user.
         */
        if ($Change) {
          return $this->error(
            "<strong>This name is not available!</strong> Please choose another one."
          );
        }
      }

      /**
       * Set name & safe name.
       */
      $this->safe_name = self::create_safe_name($params->name);
      $this->name = $params->name;

      /**
       * @var ?Change
       */
      $Change = $this->name_changes()->make([
        "type" => "name",
        "previous_value" => $this->name,
        "updated_value" => $params->name,
      ]);

      /**
       * Update settings and remove one name change.
       */
      $this->settings->name_changes_left =
        $this->settings->name_changes_left - 1;

      $return_msg = "<strong>Hello, $params->name!</strong>";
    }

    /**
     * ? Preferred Gamemode
     */
    if (isset($params->mode, $params->mod)) {
      $this->preferred_mode = Gamemode::get_gumode_as_int(
        $params->mode,
        $params->mod
      );

      unset($params->mode, $params->mod);
    }

    /**
     * Begin database transaction.
     */
    $this->db_transaction();
    try {
      /**
       * Save & commit!
       */
      $this->save();
      $this->settings->save();
      $Change?->save();
      $this->db_commit();

      return request_success(
        $return_msg ??
          "<strong>Your information has been saved!</strong>"
      );
    } catch (\Exception $e) {
      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return request_error();
    }
  }

  /**
   * @param object $params
   * @return bool
   */
  public function remove(object $params)
  {
    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser ?? $this;

    /**
     * @var ?SquadUser
     */
    $SquadUser = $CurrentUser->squad_user;

    /**
     * User is a squad owner?
     * ! Error
     */
    if ($SquadUser && $SquadUser->is_owner()) {
      return $this->error(
        "<strong>Please transfer the ownership of your squad, before you delete your account.</strong>"
      );
    }

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {
      // ? Authentications
      $CurrentUser->authentications()->delete();

      // ? Changes
      $CurrentUser->changes()->delete();

      // ? Squad
      $CurrentUser->squad_requests()?->delete();
      $CurrentUser->squad_feed_item()?->delete();
      $SquadUser?->delete();

      // ? Client hashes
      $CurrentUser->client_hashes()->delete();

      // ? Connect credentials
      $CurrentUser->connections()->delete();

      // ? Favourites
      $CurrentUser->osu_favorites()->delete();

      // ? Feedback
      $CurrentUser->feedback()->delete();

      /**
       * This will only delete images of type __user__. Squad
       * images will still be available.
       *
       * ? Images
       */
      $CurrentUser->images()->delete();

      // ? Ingame logins
      $CurrentUser->osu_ingame_logins()->delete();

      // ? Mailings
      $CurrentUser->mailings()->delete();

      // ? Manager
      $CurrentUser->manager_authentications()->delete();

      $CurrentUser->manager_logs()->delete();

      $CurrentUser->manager_sessions()->delete();

      $CurrentUser->manager_user()->delete();

      // ? Beatmap Requests
      $CurrentUser->beatmap_requests()->delete();

      // ? Notifications
      $CurrentUser->notifications()->delete();

      // ? Orders
      $Orders = $CurrentUser->orders()->get();

      foreach ($Orders as $Order) {
        /**
         * @var Order $Order
         */

        $Order->paypal()->delete();
      }

      // ? Password Resets
      $CurrentUser->password_resets()->delete();

      // ? Profile
      $CurrentUser->profile()->delete();

      // ? Ratings
      $CurrentUser->osu_ratings()->delete();

      // ? Reactions
      $CurrentUser->reactions()->delete();

      // ? Relationships
      $Relationships = Relationship::whereRaw("user1 = ? OR user2 = ?", [
        $this->id,
        $this->id,
      ])->get();

      foreach ($Relationships as $Relationship) {
        $Relationship->delete();
      }

      // ? Reports
      $CurrentUser->reports()->delete();

      // ? Restrictions
      $CurrentUser->restrictions()->delete();

      $CurrentUser->appeals()->delete();

      // ? Scores
      $Scores = $CurrentUser->scores();

      foreach ($Scores->get() as $Score) {
        /**
         * @var Score $Score
         */

        $Score->comments()->delete();

        $Score->reactions()->delete();

        $Score->thread_post_attachments()->delete();
      }

      $Scores->delete();

      // ? Searches
      $CurrentUser->searches()->delete();

      // ? Sessions
      $CurrentUser->sessions()->delete();

      // ? Stats
      $CurrentUser->stats()->delete();

      // ? Stat Developments
      $CurrentUser->stat_development()->delete();

      // ? Threads
      foreach ($CurrentUser->threads() as $Thread) {
        $Posts = $Thread->posts();

        /**
         * Delete all attachments.
         */
        foreach ($Posts->get() as $Post) {
          $Post->attachments()->delete();
        }

        /**
         * Delete Posts & finally the Thread.
         */
        $Posts->delete();
        $Thread->delete();
      }

      // ? Achievements
      $CurrentUser->achievements()->delete();

      // ? Pins
      $CurrentUser->pins()->delete();

      // ? Settings
      $CurrentUser->settings()->delete();

      $CurrentUser->privacy()->delete();

      $CurrentUser->premium()->delete();

      /**
       * ! DELETE THE USER OMG !
       */
      $CurrentUser->delete();

      $this->db_commit();

      /**
       * ? Success
       */
      return $this->success("<strong>See you l8er boi.</strong>");
    } catch (\Exception $e) {
      /**
       * Log & Rollback 🤔.
       */
      Logger::to_file($e);
      $this->db_rollback();

      /**
       * ! Error
       */
      return $this->error(
        "<strong>What happened?</strong> Something is wrong, definetely."
      );
    }
  }

  /**
   * Complete validation, serialization and setting of a given
   * e-mail address for this instance.
   *
   * @param string $email
   * @return void
   *
   * NOTE: Will die on error.
   */
  public function set_mail_invalid(string $email)
  {

    # Trim the email string first.
    $email = trim($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return die(error("<strong>Mail invalid!</strong>"));

    if (self::where("email", $email)->exists())
      return die(error("<strong>You can't use this E-Mail brother!</strong>"));

    $this->email = $email;
  }

  /**
   * Complete validation of name as it is used in the ingame client.
   *
   * @param string $name The name to validate
   * @param bool $include_former_names
   * @return void|string
   *
   * NOTE: Will die on error.
   */
  public function set_name_invalid(string $name, bool $include_former_names = true)
  {

    # Trim the name string first.
    $name = trim($name);

    $min = self::$name_length[0];
    $max = self::$name_length[1];
    $errors = [
      "disallowed" => "<strong>Name is not allowed.</strong>",
      "out-of-range" => "<strong>Name should be between $min and $max characters.</strong>",
      "invalid" => "<strong>Name can contain letters, numbers, dashes and underscores.</strong>",
      "taken" => "<strong>Name not available!</strong> Choose another one.",
    ];

    # Disallowed?
    if (in_array($name, self::$disallowed_names))
      return die(error($errors["disallowed"]));

    # Length invalid?
    if (!Validate::string_length($min, $max, $name))
      return die(error($errors["out-of-range"]));

    # Has invalid characters?
    if (!Validate::string_matches(self::$name_regex, $name))
      return die(error($errors["invalid"]));

    # In use?
    if (
      self::where(function ($q) use ($name) {
        $q->where("name", $name)->orWhere("safe_name", $name);
      })->exists()
    )
      return die(error($errors["taken"]));

    # Former name of someone else?
    if (
      $include_former_names &&
      Change::where("type", "name")
      ->where("previous_value", $name)
      ->whereNot("user_id", $this->id)
      ->first()
    )
      return die(error($errors["taken"]));

    # Set the name + safe_name for this instance!
    $this->name = $name;
    $this->safe_name = self::safe_name($name);
  }

  /**
   * @param string $name
   * @return string
   */
  public static function safe_name(string $name)
  {

    /**
     * Replace all whitespace and dashes with underscores
     *
     * + at the end of the regex Matches many in a row and will
     *   replace them with just one underscore. --- would become _
     *   then.
     */
    $regex_pattern = "/[\s\[\]\-]+/";
    $safe_name = preg_replace($regex_pattern, "_", $name);
    $counter = 1;

    # Append a number after the users safe_name, as it should be unique.
    while (User::where("safe_name", $safe_name)->exists()) {
      $safe_name = $safe_name . "_" . $counter;
      $counter++;
    }

    return $safe_name;
  }

  /**
   * @param string $password
   * @return void|string
   *
   * NOTE: May die on error.
   */
  public function set_password_invalid(string $password, bool $die_on_error = true)
  {

    # Set some validation if wanted. I think the user can decide
    # for themselves, if their password should be secure or not.
    # I don't see it as my task to force them 🙂

    $this->pw_bcrypt = self::encrypt_password($password);
  }

  /**
   * @return Profile
   */
  public function create_default_profile()
  {
    return $this->profile()->create([
      "sections_visibility" => Arr::to_json(
        Profile::$sections_visibility
      ),
      "tabs_visibility" => Arr::to_json(Profile::$tabs_visibility),
    ]);
  }

  /**
   * @return User
   */
  public static function guest()
  {
    /**
     * Create a new instance of this object.
     * @var User
     */
    $Instance = self::findOrNew(0);

    /**
     * @var UserSettings
     */
    $Instance->settings = new UserSettings();

    /**
     * @var UserSettingsPrivacy
     */
    $Instance->privacy = new UserSettingsPrivacy();

    return $Instance;
  }

  /**
   * @param Image|Comment $Content
   * @return bool
   */
  public function owns(Image|Comment $Content)
  {
    return $Content->user && $Content->user()->is($this);
  }

  /**
   * Whether or not the user is excluded from interacting with
   * the community in any way, buying Premium for themselves or
   * others, posting comments and so on.
   *
   * @return bool
   */
  public function is_socially_excluded()
  {
    return $this->is_restricted() ||
      $this->frozen_at ||
      !$this->has_accepted_privacy_policies();
  }

    // ? >>>>>>>>>>>>>>>>> AUTHENTICATIONS >>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Authentication
   */
  public function authentications()
  {
    return $this->hasMany(Authentication::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> INGAME >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?ClientHash
   */
  public function client_hashes()
  {
    return $this->hasMany(ClientHash::class, "userid", "id");
  }

  /**
   * @return ?OsuFavorite
   */
  public function osu_favorites()
  {
    return $this->hasMany(OsuFavorite::class, "userid", "id");
  }

  /**
   * @return ?OsuIngameLogin
   */
  public function osu_ingame_logins()
  {
    return $this->hasMany(OsuIngameLogin::class, "userid", "id");
  }

  /**
   * @return ?OsuRating
   */
  public function osu_ratings()
  {
    return $this->hasMany(OsuRating::class, "userid", "id");
  }

  /**
   * @return ?Achievement
   */
  public function achievements()
  {
    return $this->hasMany(Achievement::class, "userid", "id");
  }

    // ? >>>>>>>>>>>>>>>>>>>>> MANAGER >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?ManagerAuthentication
   */
  public function manager_authentications()
  {
    return $this->hasMany(ManagerAuthentication::class);
  }

  /**
   * @return ?ManagerLog
   */
  public function manager_logs()
  {
    return $this->hasMany(ManagerLog::class);
  }

  /**
   * @return ?ManagerSession
   */
  public function manager_sessions()
  {
    return $this->hasMany(ManagerSession::class);
  }

  /**
   * @return ?ManagerUser
   */
  public function manager_user()
  {
    return $this->hasOne(ManagerUser::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> THREADS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Thread
   */
  public function threads()
  {
    return $this->hasMany(Thread::class);
  }

  /**
   * @return ?ThreadPost
   */
  public function thread_posts()
  {
    return $this->hasMany(ThreadPost::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> SSO >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return bool
   */
  public function signed_up_through_sso()
  {
    return $this->google || $this->discord || $this->osu;
  }

  /**
   * @return bool
   */
  public function has_recommended_settings()
  {
    return !filter_var($this->email, FILTER_VALIDATE_EMAIL);
  }

  /**
   * @return HasMany<Connection>
   */
  public function connections()
  {
    return $this->hasMany(Connection::class);
  }

  /**
   * @return HasOne<ConnectionDiscord>
   */
  public function discord()
  {
    return $this->hasOne(ConnectionDiscord::class)
      ->where("provider", "discord");
  }

  /**
   * @return HasOne<ConnectionGoogle>
   */
  public function google()
  {
    return $this->hasOne(ConnectionGoogle::class)
      ->where("provider", "google");
  }

  /**
   * @return HasOne<ConnectionOsu>
   */
  public function osu()
  {
    return $this->hasOne(ConnectionOsu::class)
      ->where("provider", "osu!");
  }

    // ? >>>>>>>>>>>>>>>>>>> RESTRICTION SYSTEM >>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Restriction
   */
  public function restrictions()
  {
    return $this->hasMany(Restriction::class);
  }

  /**
   * @return bool
   */
  public function is_restricted()
  {
    return !$this->has_privileges_of(Privilege::UNRESTRICTED);
  }

  /**
   * @return ?Restriction
   */
  public function current_restriction()
  {
    return $this->restrictions()
      ->whereRaw("created_at >= ?", $this->frozen_at)
      ->first();
  }

  /**
   * @param object $params
   * @return bool
   */
  public function restrict(object $params)
  {
    /**
     * Remove cached data.
     */
    $this->remove_cached_data();

    /**
     * Remove unrestricted privileges. This will also update the
     * frozen state.
     */
    $this->remove_privileges(Privilege::UNRESTRICTED);

    /**
     * @var ?string
     */
    $reason = !empty($params->reason) ? $params->reason : null;

    /**
     * @var Restriction
     */
    $Restriction = $this->restrictions()
      ->create([
        "reason" => $reason,
        "updated_at" => null,
      ])
      ->fresh();

    /**
     * @var Notification
     */
    $this->notifications()->create([
      "type" => "__system__/restriction",
      "reference_id" => null,
      "reference_2_id" => $Restriction->id,
      "updated_at" => null,
    ]);

    /**
     * Send mail if the user has one set.
     */
    if ($this->email && $this->privacy && $this->privacy->mailing_account) {
      $app_url = _env("SERVER_ADDRESS");

      $mail_token = Utils::random_alpha_token(64);
      $mail_subject = "⚠️ You have been restricted";
      $mail_template = "restricted";
      $mail_template_path =
        ROOT . "/app/templates/mail/$mail_template.html";
      $mail_body = file_get_contents($mail_template_path);
      $mail_body = str_replace(
        "{current-date}",
        date("d. F Y"),
        $mail_body
      );
      $mail_body = str_replace("{username}", $this->name, $mail_body);
      $mail_body = str_replace(
        "{restriction-link}",
        "$app_url/my/game/restriction?mailing_token=$mail_token",
        $mail_body
      );
      $mail_body = str_replace(
        "{discord-link}",
        _env("DISCORD_INVITE"),
        $mail_body
      );
      $mail_body = str_replace(
        "{youtube-link}",
        _env("YOUTUBE_INVITE"),
        $mail_body
      );
      $mail_body = str_replace(
        "{footer-copyright}",
        _env("APP_NAME") .
          " &copy; " .
          date("Y") .
          ". All rights reserved.",
        $mail_body
      );
      $mail_body = str_replace(
        "{unsubscribe-link}",
        "$app_url/my/privacy/mailing",
        $mail_body
      );

      if ((new Mail())->create($this->email, $mail_subject, $mail_body)) {
        $this->mailings()->create([
          "template" => $mail_template,
          "email" => $this->email,
          "subject" => $mail_subject,
          "token" => $mail_token,
          "updated_at" => null,
        ]);
      }
    }

    return true;
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function appeals()
  {
    return $this->hasMany(RestrictionAppeal::class);
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function appeal_being_reviewed()
  {
    /**
     * User is not frozen nor restricted?
     */
    if (!$this->frozen_at || !$this->is_restricted()) {
      return null;
    }

    return $this->appeals()
      ->whereRaw("created_at > ?", $this->frozen_at)
      ->where("status", "AWAITING_PROCESSING")
      ->first();
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function current_appeal()
  {
    /**
     * User is not restricted nor frozen?
     */
    if (!$this->frozen_at && !$this->is_restricted()) {
      return null;
    }

    return $this->appeals()
      ->whereRaw("created_at > ?", $this->frozen_at)
      ->whereIn("status", ["AWAITING_PROCESSING", "REDO_REQUESTED"])
      ->latest()
      ->first();
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function current_appeal_declined()
  {
    /**
     * User is not restricted nor frozen?
     */
    if (!$this->frozen_at && !$this->is_restricted()) {
      return null;
    }

    return $this->appeals()
      ->whereRaw("updated_at > ? & status = 'DECLINED'", $this->frozen_at)
      ->latest()
      ->first();
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function current_appeal_after_restriction_waiting_period()
  {
    /**
     * User is currently restricted and there is an appeal waiting
     * for being processed by a staff member?
     */
    if ($this->is_restricted() && $this->current_appeal()) {
      return $this->current_appeal();
    }

    return null;
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function declined_live_play()
  {
    /**
     * User is frozen?
     */
    if (!$this->frozen_at) {
      return null;
    }

    return $this->appeals()
      ->whereRaw("created_at > ?", $this->frozen_at)
      ->where("status", "DECLINED")
      ->whereNull("is_appeal")
      ->first();
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function declined_appeal()
  {
    /**
     * User is frozen?
     */
    if (!$this->frozen_at) {
      return null;
    }

    return $this->appeals()
      ->whereRaw("created_at > ?", $this->frozen_at)
      ->where("status", "DECLINED")
      ->whereNotNull("is_appeal")
      ->first();
  }

  /**
   * @return ?string Time left as a human readable string or null.
   */
  public function appeal_locked()
  {
    /**
     * User is not restricted?
     */
    if (!$this->is_restricted()) {
      return false;
    }

    /**
     * @var int
     */
    $restrictions_count = $this->restrictions()->count();

    /**
     * @var string
     */
    $interval = match ($restrictions_count) {
      1 => "+1 second",
      2 => "+5 seconds",
      default => "+1 second",
    };

    /**
     * @var Restriction
     */
    $Restriction = $this->restrictions()->latest()->first();

    /**
     * @var DateTime
     */
    $appeal_ready_time = (new DateTime($Restriction->created_at))->modify(
      $interval
    );

    /**
     * @var ?string
     */
    $time_to_appeal_left = Time::left(
      $appeal_ready_time->format("Y-m-d H:i:s"),
      exact_hours: false,
      full: false
    );

    return $time_to_appeal_left;
  }

    // ? >>>>>>>>>>>>>>>>>>>>> REQUESTS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?BeatmapRequest
   */
  public function beatmap_requests()
  {
    return $this->hasMany(BeatmapRequest::class, "player_id", "id");
  }

    // ? >>>>>>>>>>>>>>>>>>>>> DISPLAY >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return string
   */
  public function headline_cover(int $gumode = 0, bool $big_cover = false)
  {
    $big_cover ??= true;

    /**
     * User has set a custom headline?
     */
    if ($this->premium && $this->premium->headline) {
      $beatmap_set_id = $this->premium->headline;
    } else {
      $Score = $this->scores()
        ->whereHas("beatmap", function ($q) {
          $q->whereIn("status", [2]);
        })
        ->where("status", 2)
        ->where("mode", $gumode)
        ->orderByDesc("pp")
        ->first();
    }

    return include ROOT . "/app/templates/helper/beatmaps/_cover.php";
  }

  /**
   * @return string
   */
  public function link()
  {
    return "/u/" . $this->id;
  }

  /**
   * @var string
   */
  public function settings_link()
  {
    return "/my/overview";
  }

  /**
   * @var string
   */
  public function squad_settings_link()
  {
    return "/manage/squad";
  }

  /**
   * @return include
   */
  public function image(bool $gdpr = true)
  {
    $user_image_id = $this->id;
    $gdpr ??= true;
    $deleted = $this->deleted ?? null;

    include ROOT . "/app/templates/helper/users/_image.php";
  }

    // ? >>>>>>>>>>>>>>>>>>>>> BIRTHDAY >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return bool
   */
  public function has_birthday()
  {
    $birthday = $this->settings?->birthday;
    return $birthday
      ? date("m-d", strtotime($birthday)) === date("m-d", time())
      : false;
  }

  /**
   * @return ?Feedback
   */
  public function birthday_cheers(string $year)
  {
    return Feedback::where([
      "reference_id" => $this->id,
      "type" => "birthday_cheer",
      ["created_at", "LIKE", "%$year%"],
    ])
      ->orderBy("created_at", "DESC")
      ->get();
  }

  /**
   * @return bool
   */
  public function cheered_for_birthday(User $User, string $year)
  {
    return Feedback::where([
      "user_id" => $this->id,
      "reference_id" => $User->id,
      "type" => "birthday_cheer",
      ["created_at", "LIKE", "%$year%"],
    ])->exists();
  }

    // ? >>>>>>>>>>>>>>>>>>>>> ORDERS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Order
   */
  public function orders()
  {
    return $this->hasMany(Order::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> RELATIONS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Search
   */
  public function searches()
  {
    return $this->hasMany(Search::class);
  }

  /**
   * @return ?Comments
   */
  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> PINS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?UserPin
   */
  public function pins()
  {
    return $this->hasMany(UserPin::class);
  }

  /**
   * @param Score|Beatmap $Reference
   * @return ?Score|Beatmap
   */
  public function has_pinned(Score|Beatmap $Reference)
  {
    /**
     * @var int
     */
    $id = $Reference->id;

    if ($Reference instanceof Score) {
      $Reference = $this->pins()->where("type", "score");
    } elseif ($Reference instanceof Beatmap) {
      $Reference = $this->pins()->where("type", "beatmap");
    }

    return $Reference->where("reference_id", $id)->first();
  }

    // ? >>>>>>>>>>>>>>>>>>>>> REPORTS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Report
   */
  public function reports()
  {
    return $this->hasMany(Report::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> CHANGES >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Change
   */
  public function changes()
  {
    return $this->hasMany(Change::class);
  }

  /**
   * @return ?Change
   */
  public function name_changes()
  {
    return $this->hasMany(Change::class)->where("type", "name");
  }

  /**
   * @return ?Change
   */
  public function password_changes()
  {
    return $this->hasMany(Change::class)->where("type", "password");
  }

  /**
   * @return ?PasswordReset
   */
  public function password_resets()
  {
    return $this->hasMany(PasswordReset::class);
  }

  /**
   * @return ?Image
   */
  public function images()
  {
    return $this->hasMany(Image::class)->where(
      "type",
      "LIKE",
      "%__user__%"
    );
  }

  /**
   * Verifies login credentials with either name or e-mail.
   *
   * @param string $login
   * @param string $password
   * @return ?User
   *
   * NOTE: Might die on error.
   */
  public static function verify_login(string $login, string $password, bool $die = false)
  {

    /**
     * @var User
     */
    $User = self::where(function ($q) use ($login) {
      $q->where("name", $login)->orWhere("email", $login);
    })->first();

    # No user found?
    if (!$User)
      return $die ? die(error("<strong>No! 🙂‍↔️</strong>")) : null;

    # Password doesn't decrypt hash?
    if (!self::decrypt_password($password, $User->pw_bcrypt))
      return $die ? die(error("<strong>No! 🙂‍↔️</strong>")) : null;

    return $User;
  }

    // ? >>>>>>>>>>>>>>>>>>>>> PRIVILEGES >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Privilege[]
   */
  public function privileges()
  {
    $privileges = [];

    /**
     * User is not yet verified and just has privileges of 0.
     */
    if ($this->priv == 0) {
      return [
        (object) [
          "privilege" => Privilege::UNVERIFIED,
          "bits" => Privilege::UNVERIFIED->value,
          "name" => Privilege::UNVERIFIED->get_display()->name,
          "icon" => Privilege::UNVERIFIED->get_display()->icon,
        ],
      ];
    }

    foreach (Privilege::cases() as $bits => $Privilege) {
      $display = $Privilege->get_display();

      /**
       * Any other case.
       */
      if (($this->priv & $Privilege->value) !== 0) {
        $privileges[] = (object) [
          "privilege" => $Privilege,
          "bits" => $bits,
          "name" => $display->name,
          "icon" => $display->icon,
        ];
      }
    }

    return $privileges ? $privileges : null;
  }

  /**
   * @var Privilege $Privileges
   * @return object
   */
  public function add_privileges(Privilege ...$Privileges)
  {
    /**
     * @var int
     */
    $updated_privs = $this->priv;

    foreach ($Privileges as $Privilege) {
      /**
       * User has privileges already?
       */
      if ($this->has_privileges_of($Privilege)) {
        continue;
      }

      /**
       * Add it up!
       */
      $updated_privs += $Privilege->value;
    }

    /**
     * Update it, if it's not the same as before!
     */
    if ($this->priv !== $updated_privs) {
      $this->update([
        "priv" => $updated_privs,
      ]);
    }

    return $this->success("<strong>Privileges updated!</strong>");
  }

  /**
   * @var Privilege $Privileges
   * @return object
   */
  public function remove_privileges(Privilege ...$Privileges)
  {
    /**
     * @var int
     */
    $updated_privs = $this->priv;

    foreach ($Privileges as $Privilege) {
      if ($Privilege == Privilege::UNRESTRICTED) {
        if (!$this->has_privileges_of($Privilege)) {
          /**
           * User has privileges already?
           */
          continue;
        }
      }

      /**
       * Add it up!
       */
      $updated_privs -= $Privilege->value;
    }

    /**
     * Update it, if it's not the same as before!
     */
    if ($this->priv !== $updated_privs) {
      $this->update([
        "priv" => $updated_privs,
      ]);
    }

    return $this->success("<strong>Privileges updated!</strong>");
  }

  /**
   * @var Privilege $Privilege
   * @return bool
   */
  public function has_privileges_of(Privilege $Privilege)
  {
    return ($this->priv & $Privilege->value) !== 0;
  }

  /**
   * @var Privilege $Privilege
   * @return bool
   */
  public function missing_privileges_of(Privilege $Privilege)
  {
    return ($this->priv & $Privilege->value) === 0;
  }

  /**
   * Allows certain roles and users to interact with
   * everything.
   *
   * @return bool
   */
  public function is_super_user()
  {
    return // $this->has_privileges_of(Privilege::COMMUNITY_MANAGER)
      // ||
      in_array($this->id, [3]);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> COUNTRY >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return string The country string.
   */
  public function country_string()
  {
    return $this->data()->country === "xx"
      ? "Unknown country"
      : Locale::getDisplayRegion(
        "-" . strtoupper($this->data()->country),
        "en"
      );
  }

  /**
   * @return Country
   */
  public function country()
  {
    return $this->belongsTo(Country::class, "country", "abbreviation");
  }

  /**
   * @return include /app/templates/helper/_image_country.php
   */
  public function country_icon()
  {
    $country_abbreviation = $this->country;
    return include ROOT . "/app/templates/helper/_image_country.php";
  }

    // ? >>>>>>>>>>>>>>>>>>>>> RANKING >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * Fetches all rankings of this user from flobal to country and
   * possible gamemodes
   *
   * @param int $gumode The gamemode (optional)
   * @return object Either all rankings or if gumode is set, just
   *    the ranking as object for this specific mode
   */
  public function get_rankings(int $gumode)
  {
    $Redis = Redis::connect();
    $return = (object) [];
    $country = $this->data()->country;

    /**
     * Build redis key.
     */
    $global_rank = $Redis->zrevrank(
      "bancho:leaderboard:$gumode",
      $this->id
    );
    $country_rank = $Redis->zrevrank(
      "bancho:leaderboard:$gumode:$country",
      $this->id
    );

    $return->global = $global_rank !== null ? $global_rank + 1 : null;
    $return->country = $country_rank !== null ? $country_rank + 1 : null;
    $return->development = $this->get_rank_development($gumode);

    return $return;
  }

  /**
   * @param int
   * @return object
   */
  public function get_all_rankings()
  {
    $Redis = Redis::connect();
    $return = [];
    $country = $this->data()->country;

    foreach (Gamemode::$modes as $key => $gumode) {
      $global_rank = $Redis->zrevrank(
        "bancho:leaderboard:$gumode",
        $this->id
      );
      $country_rank = $Redis->zrevrank(
        "bancho:leaderboard:$gumode:$country",
        $this->id
      );

      $return[$key] = [
        "mode" => $gumode,
        "global" => $global_rank !== null ? $global_rank + 1 : null,
        "country" => $country_rank !== null ? $country_rank + 1 : null,
        "development" => $this->get_rank_development($gumode),
      ];
    }

    return $return;
  }

  /**
   * Evaluates if the user has raised or dropped in ranks and
   * gives the exact count.
   *
   * @param int $gumode The mode as gumode.
   * @return object An object with the status whether dropped or
   *    increased (or stagnated) and the amount in ranks.
   */
  public function get_rank_development(int $gumode)
  {
    $Redis = Redis::connect();
    $country = $this->country;

    /**
     * Global backup
     */
    $redis_key = "heiakim:leaderboard:development";
    $old_rank_global = $Redis->zrevrank("$redis_key:$gumode", $this->id);
    $old_rank_score_global = $Redis->zrevrank(
      "$redis_key:$gumode:rscore",
      $this->id
    );

    $old_rank_country = $Redis->zrevrank(
      "$redis_key:$gumode:$country",
      $this->id
    );
    $old_rank_score_country = $Redis->zrevrank(
      "$redis_key:$gumode:$country:rscore",
      $this->id
    );

    /**
     * Current rankings
     */
    $redis_key = "bancho:leaderboard";
    $current_rank_global = $Redis->zrevrank(
      "$redis_key:$gumode",
      $this->id
    );
    $current_rank_score_global = $Redis->zrevrank(
      "$redis_key:$gumode:rscore",
      $this->id
    );

    $current_rank_country = $Redis->zrevrank(
      "$redis_key:$gumode:$country",
      $this->id
    );
    $current_rank_score_country = $Redis->zrevrank(
      "$redis_key:$gumode:$country:rscore",
      $this->id
    );

    /**
     * Compare rankings
     */
    return [
      "global" => [
        "performance" =>
        $current_rank_global !== null
          ? $current_rank_global - $old_rank_global
          : null,
        "score" =>
        $current_rank_score_global !== null
          ? $current_rank_score_global - $old_rank_score_global
          : null,
      ],
      "country" => [
        "performance" =>
        $current_rank_country !== null
          ? $current_rank_country - $old_rank_country
          : null,
        "score" =>
        $current_rank_score_country !== null
          ? $current_rank_score_country - $old_rank_score_country
          : null,
      ],
    ];
  }

  /**
   * @return Profile
   */
  public function create_profile()
  {
    return $this->profile()->create([
      "sections_visibility" => Arr::to_json(
        Profile::$sections_visibility
      ),
    ]);
  }

  /**
   * @return Profile
   */
  public function profile()
  {
    return $this->hasOne(Profile::class);
  }

  /**
   * @return object
   */
  public function decoded_profile()
  {
    $Profile = $this->profile;
    $sections = json_decode($Profile->sections_visibility ?? "{}", true);
    $tabs = json_decode($Profile->tabs_visibility ?? "{}", true);

    $profile = [];
    $profile["sections_visibility"] = $sections;
    $profile["tabs_visibility"] = $tabs;

    return (object) $profile;
  }

  /**
   * @return Session
   */
  public function sessions()
  {
    return $this->hasMany(Session::class);
  }

  /**
   * @return Stat
   */
  public function stats()
  {
    return $this->hasMany(Stat::class, "id", "id");
  }

  /**
   * @return StatDevelopment
   */
  public function stat_development()
  {
    return $this->hasMany(StatDevelopment::class);
  }

  /**
   * @return User\UserSettings
   */
  public function settings()
  {
    return $this->hasOne(User\UserSettings::class);
  }

  /**
   * @return User\UserSettingsPrivacy
   */
  public function privacy()
  {
    return $this->hasOne(User\UserSettingsPrivacy::class);
  }

  /**
   * @return bool Whether or not
   */
  public function has_accepted_privacy_policies()
  {
    return $this->privacy->accepts_policies;
  }

  /**
   * Getter for user's status on bancho.
   */
  public function get_bancho_game_status()
  {
    $Bancho = (new Bancho())->request("v2/players/$this->id/status");

    if (!$Bancho || $Bancho->status == "error" || !$Bancho->status) {
      return null;
    }

    $action_string = match ($Bancho->data->action) {
      0, 1 => "Idle",
      2 => "Playing",
      3 => "Modding",
      4 => "Editing",
      6 => "Watching",
      default => "Idle",
    };

    if ($Bancho->data->action == 6) {
      $info_text = $Bancho->data->info_text;
      $preg = preg_match("/^(.*? play)/", $info_text, $matches);
      $action_string .= " " . $matches[0];
    }

    return Arr::objectify([
      "online" => true,
      "login_time" => $Bancho->data->login_time,
      "action" => $action_string,
      "action_id" => $Bancho->data->action,
      "string" => $Bancho->data->info_text,
      "mode" => $Bancho->data->mode,
      "mods" => $Bancho->data->mods,
      "beatmap_id" => $Bancho->data->beatmap_id,
    ]);
  }

    // ? >>>>>>>>>>>>>>>>>>> AUTHORIZATION >>>>>>>>>>>>>>>>>>>>>

  /**
   * @param Image $Content
   * @param bool $die_on_error
   * @return bool
   */
  public function authorize_content_touch(
    Image $Content,
    $die_on_error = true
  ) {
    $authorized = $this->is_super_user() || $this->is($Content->user);

    return $die_on_error
      ? (!$authorized
        ? die(request_error("!NO_PERMISSIONS"))
        : true)
      : (!$authorized
        ? false
        : true);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> PREMIUM >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return User\UserSettingsPremium
   */
  public function premium()
  {
    return $this->hasOne(User\UserSettingsPremium::class);
  }

  /**
   * @return bool Whether or not
   */
  public function is_premium()
  {
    return $this->has_privileges_of(Privilege::SUPPORTER);
  }

  /**
   * @return void
   */
  public function give_premium(string $datetime)
  {
    $current_premium_time = max($this->donor_end, time());
    $future_end_time = strtotime($datetime, $current_premium_time);

    /**
     * Update the user.
     */
    $this->update([
      "donor_end" => $future_end_time,
    ]);

    /**
     * Create premium settings.
     */
    if (!$this->premium) {
      $this->premium()->create();
    }

    /**
     * Add supporter privileges to the user.
     */
    $this->add_privileges(Privilege::SUPPORTER);

    return;
  }

  /**
   * Returns the name with or without premium accessoires.
   *
   * @return string
   */
  public function name()
  {
    if (!$this->is_premium()) {
      return $this->name;
    } else {
      $premium_name_style = $this->premium?->premium_name_style;

      if ($premium_name_style) {
        return <<<TEXT
    <span class="is-premium-name premium-txt-$premium_name_style">$this->name</span>
TEXT;
      } else {
        return $this->name;
      }
    }
  }

  // ? >>>>>>>>>>>>>>>>> NOTIFICATIONS >>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return HasMany<Notification>
   */
  public function notifications()
  {
    return $this->hasMany(Notification::class);
  }

  /**
   * @return void
   */
  public function touch_notifications()
  {
    return $this->settings->update([
      "checked_notifications_at" => CURRENT_TIMESTAMP,
    ]);
  }

  /**
   * @return int
   */
  public function unread_notifications_count()
  {

    $last_checked = $this->settings->checked_notifications_at ?? "2000-01-01 01:01:01";

    return $this->notifications()
      ->where("created_at", ">", $last_checked)
      ->count();
  }

  // ? >>>>>>>>>>>>>>>>>>>>> SQUADS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "clan_id", "id");
  }

  /**
   * @return ?bool
   */
  public function is_squad_chief()
  {
    return $this->squad_user?->is_owner();
  }

  /**
   * @return ?bool
   */
  public function is_squad_community_manager()
  {
    return $this->squad_user?->has_privileges_of(
      SquadPrivilege::COMMUNITY_MANAGER
    );
  }

  /**
   * @param string $type
   * @param string $section
   * @return ?bool
   */
  public function sqcan(string $type, string $section, ?Squad $in = null)
  {
    return $this->squad_user?->can($type, $section, $in);
  }

  /**
   * Gets the SquadUser object for the current user, which holds
   * parameters just as privileges.
   *
   * @return Squad\SquadUser
   */
  public function squad_user()
  {
    return $this->hasOne(Squad\SquadUser::class);
  }

  /**
   * @return ?Squad
   */
  public function has_squad()
  {
    return $this->clan_id;
  }

  /**
   * Users can just join a new squad if they are free of a current
   * squad, are not restricted in any way and if the squad is
   * completly public.
   *
   * @param Squad $Squad
   * @return bool
   */
  public function sqcan_join(Squad $Squad)
  {
    return $Squad->is_public() &&
      !$this->is_socially_excluded() &&
      !$this->has_squad();
  }

  /**
   * Checks if the current user can request to join a certain Squad.
   *
   * @param Squad $Squad
   * @return bool
   */
  public function sqcan_request(Squad $Squad)
  {
    return $Squad->is_requestable() &&
      !$this->is_socially_excluded() &&
      !$this->has_squad() &&
      !$this->squad_requests()->where("clan_id", $Squad->id)->count();
  }

  /**
   * @param Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content
   * @return bool
   */
  public function sqcan_touch(
    Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content
  ) {
    return $this->squad_user?->can_touch($Content);
  }

  /**
   * @param Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content
   * @param bool $die_on_error
   * @return bool
   */
  public function sqauthorize_content_touch(
    Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content,
    $die_on_error = true
  ) {
    return $this->squad_user?->authorize_content_touch(
      $Content,
      $die_on_error
    );
  }

  /**
   * @param SquadFeedItem|SquadPost $Content
   * @return bool
   */
  public function sqcan_interact_with(SquadFeedItem|SquadPost $Content)
  {
    return $this->squad_user?->can_interact_with($Content);
  }

  /**
   * @param SquadFeedItem|SquadPost $Content
   * @param bool $die_on_error
   * @return bool
   */
  public function sqauthorize_content_interaction(
    SquadFeedItem|SquadPost $Content,
    $die_on_error = true
  ) {
    return $this->squad_user?->authorize_content_interaction(
      $Content,
      $die_on_error
    );
  }

  /**
   * Checks if the SquadUser of this User can interact with a
   * certain Squad.
   *
   * @param Squad $Squad
   * @return ?bool
   */
  public function sqcan_take_action_in(Squad $Squad)
  {
    return $this->squad_user?->can_take_action_in($Squad);
  }

  /**
   * Includes requests for joining and invites.
   *
   * @return ?Squad\SquadRequest
   */
  public function squad_requests()
  {
    return $this->hasMany(Squad\SquadRequest::class, "user_id")->orWhere(
      "reference_id",
      $this->id
    );
  }

  /**
   * @param User $User
   * @return bool
   */
  public function sqcan_invite(User $User)
  {
    return $this->squad &&
      !$User->has_squad() &&
      $this->sqcan("coordinate", "users") &&
      $User->privacy?->receive_invites &&
      !$User->has_invite_from($this->squad) &&
      !$User->is($this);
  }

  public function available_action_for(Squad $Squad)
  {
    return match (!0) {
      $this->squad?->is($Squad) => "is_member",
      $this->has_invite_from($Squad) => "invite_pending",
      $this->requested_to_join($Squad) => "request_pending",
      $this->sqcan_join($Squad) => "can_join",
      $this->sqcan_request($Squad) => "can_request",
      !$this->exists => "login",
      default => null,
    };
  }

  /**
   * @return ?Squad\SquadRequest
   */
  public function has_invite_from(?Squad $Squad)
  {
    return $Squad?->invites()->where("reference_id", $this->id)->first();
  }

  /**
   * @return ?Squad\SquadRequest
   */
  public function requested_to_join(Squad $Squad)
  {
    return SquadRequest::where("user_id", $this->id)
      ->where("type", "join")
      ->where("clan_id", $Squad->id)
      ->first();
  }

  /**
   * @return ?SquadRequest
   */
  public function has_active_squad_request_for(Squad $Squad)
  {
    return $this->squad_requests()
      ->where("clan_id", $Squad->id)
      ->whereNull("done_at")
      ->first();
  }

  /**
   * @return ?Squad\SquadFeedItem
   */
  public function squad_feed_item()
  {
    return $this->hasMany(Squad\SquadFeedItem::class);
  }

  /**
   * @return ?Squad\SquadPost
   */
  public function squad_post()
  {
    return $this->hasMany(Squad\SquadPost::class);
  }

  /**
   * @return ?Squad\SquadPostVote
   */
  public function squad_post_votes()
  {
    return $this->hasMany(Squad\SquadPostVote::class);
  }

  /**
   * @return ?Squad\SquadPostComment
   */
  public function squad_post_comments()
  {
    return $this->hasMany(Squad\SquadPostComment::class);
  }

  /**
   * Dynamically checks for a given content to have a vote on it.
   * It evaluates the Content's source Model and checks
   * specifically for it.
   *
   * @return ?SquadPostVote
   */
  public function has_voted_for(SquadPost $Content)
  {
    return
      /**
       * ? SquadPost
       */
      $Content instanceof SquadPost
      ? $this->squad_post_votes()
      ->where("post_id", $Content->id)
      ->whereNull("deleted_at")
      ->first()
      :
      /**
       * ? Add more as more will be added.
       */
      null;
  }

  /**
   * @return ?SquadPost
   */
  public function has_answered_poll(SquadPost $Content)
  {
    return $Content
      ->poll_answers()
      ->where("user_id", $this->id)
      ->whereNull("deleted_at")
      ->first();
  }

    // ? >>>>>>>>>>>>>>>>> FOLLOWER SYSTEM >>>>>>>>>>>>>>>>>>>>>
    // * When a user starts following another user, the user making
    // * the action will be user1!

  /**
   * @return ?Relationship
   */
  public function followers()
  {
    return $this->belongsToMany(
      User::class,
      "relationships",
      "user2",
      "user1"
    )
      ->withPivot("type", "updated_at")
      ->wherePivot("type", "friend");
  }

  /**
   * @return ?Relationship
   */
  public function followings()
  {
    return $this->belongsToMany(
      User::class,
      "relationships",
      "user1",
      "user2"
    )
      ->withPivot("type", "updated_at")
      ->wherePivot("type", "friend");
  }

  /**
   * @param User $User
   * @return bool
   */
  public function follows(User $User)
  {
    return $this->followings->contains($User);
  }

  /**
   * @param User $User
   * @return bool
   */
  public function is_followed_by(User $User)
  {
    return $this->followers->contains($User);
  }

  /**
   * @param User $User The User watching the profile.
   * @return string
   */
  public function follow_action_display(User $User)
  {
    /**
     * @var bool
     */
    $viewing_self = $this->id === $User->id;

    /**
     * Viewing own profile?
     */
    if ($viewing_self) {
      return "manage";
    }

    /**
     * User watching the profile is not following, but the user
     * being watched does follow the one watching. Special case,
     * show follow back!
     */
    if ($this->follows($User) && !$User->follows($this)) {
      return "refollow";
    }

    /**
     * User watching the profile is not following.
     */
    if (!$User->follows($this)) {
      return "follow";
    }

    /**
     * User watching the profile is following.
     */
    if ($User->follows($this)) {
      return "unfollow";
    }
  }

    // ? >>>>>>>>>>>>>>>>>>>>> FEEDBACK >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return ?Feedback
   */
  public function feedback()
  {
    return $this->hasMany(Feedback::class);
  }

  /**
   * Check if a given user has given feedback to this score.
   *
   * @param int $user_id The user id.
   * @return boolean True of false.
   */
  public function has_given_feedback_for(string $type, int $reference_id)
  {
    return $this->db->select(
      "
      SELECT count(id) counter
      FROM feedback
      WHERE type = ?
      AND user_id = ?
      AND reference_id = ?
      LIMIT 1
      ",
      [$type, $this->id, $reference_id]
    )->counter;
  }

  /**
   * @return ?Reaction
   */
  public function reactions()
  {
    return $this->hasMany(Reaction::class);
  }

    // ? >>>>>>>>>>>>>>>>>>>>> BATMAPS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return Feedback
   */
  public function favorite_beatmaps()
  {
    return $this->feedback()->where("type", "beatmap");
  }

    // ? >>>>>>>>>>>>>>>>>>>>> ARTISTS >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return Feedback
   */
  public function favorite_artists()
  {
    return $this->feedback()->where("type", "artist");
  }

    // ? >>>>>>>>>>>>>>>>>>>>> SCORES >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return Score
   */
  public function scores()
  {
    return $this->hasMany(Score::class, "userid", "id");
  }

  /**
   * @return ?Score
   */
  public function first_place_scores(
    ?int $gumode = null,
    string $order = "pp",
    string $sort = "DESC",
    int $limit = 10,
    int $offset = 0,
    bool $from_api = false
  ) {
    /**
     * @var ?Score
     */
    $Scores = $this->scores()

      /**
       * Only public columns.
       */
      ->when($from_api, function ($q) {
        $q->select(ScoresGateway::$public_columns);
      })

      /**
       * Filter by gumode.
       */
      ->when($gumode !== null, function ($q) use ($gumode) {
        $q->where("scores.mode", $gumode);
      })

      /**
       * Only loved and ranked scores.
       */
      ->whereHas("beatmap", function ($q) {
        $q->whereIn("maps.status", [2, 5]);
      })

      /**
       * Filter out scores that are not first.
       */
      ->join(
        $this->getConnection()->raw(
          "(SELECT map_md5, MAX(pp) as max_pp FROM scores GROUP BY map_md5) as max_scores"
        ),
        "scores.map_md5",
        "=",
        "max_scores.map_md5"
      )
      ->where(
        "scores.pp",
        "=",
        $this->getConnection()->raw("max_scores.max_pp")
      )

      ->whereIn("scores.status", [2])
      ->orderBy($order, $sort)
      ->limit($limit)
      ->offset($offset)
      ->get();

    /**
     * @var ?Score
     */
    // $Scores = $UserScores->filter(function ($score) {
    //   $BestScore = Score::select(["userid"])
    //     ->where('map_md5', $score->map_md5)
    //     ->whereIn('status', [2])
    //     ->whereNull("deleted_at")
    //     ->orderBy('pp', 'DESC')
    //     ->limit(1)
    //     ->first();

    //   if ($BestScore && $BestScore->userid == $this->id)
    //     return true;

    //   return false;
    // });

    return $Scores;
  }

  // ? >>>>>>>>>>>>>>>>>>>>> MAILING >>>>>>>>>>>>>>>>>>>>>>>>>

  public function mailings()
  {
    return $this->hasMany(Mailing::class);
  }

    // ? >>>>>>>>>>>>>>>>>> CACHED DATA >>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return object
   */
  public function refresh_cached_data()
  {
    /**
     * @var \Predis\Client
     */
    $redis = $this->redis();

    /**
     * @var Country
     */
    $Country = $this->country()->first();
    $country = $Country->abbreviation;

    /**
     * Set new redis keys for leaderboard.
     */
    $bancho_key = Leaderboard::$redis_keys["bancho"];
    $heiakim_key = Leaderboard::$redis_keys["heiakim"];

    foreach (Gamemode::$modes as $mode) {
      $Stat = $this->stats()->where("mode", $mode)->first();

      if (!$Stat) {
        $Stat = $this->stats()->create([
          "mode" => $mode,
        ]);

        $Stat = $Stat->fresh();
      }

      /**
       * Bancho
       */
      $redis->zadd("$bancho_key:$mode", $Stat->pp, $this->id);
      $redis->zadd("$bancho_key:$mode:rscore", $Stat->rscore, $this->id);
      $redis->zadd("$bancho_key:$mode:tscore", $Stat->tscore, $this->id);

      /**
       * Bancho country
       */
      $redis->zadd("$bancho_key:$mode:$country", $Stat->pp, $this->id);
      $redis->zadd(
        "$bancho_key:$mode:$country:rscore",
        $Stat->rscore,
        $this->id
      );
      $redis->zadd(
        "$bancho_key:$mode:$country:tscore",
        $Stat->tscore,
        $this->id
      );

      /**
       * heia.kim
       */
      $redis->zadd("$heiakim_key:$mode", $Stat->pp, $this->id);
      $redis->zadd("$heiakim_key:$mode:rscore", $Stat->rscore, $this->id);
      $redis->zadd("$heiakim_key:$mode:tscore", $Stat->tscore, $this->id);

      /**
       * Bancho country
       */
      $redis->zadd("$heiakim_key:$mode:$country", $Stat->pp, $this->id);
      $redis->zadd(
        "$heiakim_key:$mode:$country:rscore",
        $Stat->rscore,
        $this->id
      );
      $redis->zadd(
        "$heiakim_key:$mode:$country:tscore",
        $Stat->tscore,
        $this->id
      );
    }
  }

  /**
   * @return object
   */
  public function remove_cached_data()
  {
    /**
     * @var \Predis\Client
     */
    $redis = $this->redis();

    /**
     * @var Country
     */
    $Country = $this->country()->first();
    $country = $Country->abbreviation;

    /**
     * Set new redis keys for leaderboard.
     */
    $bancho_key = Leaderboard::$redis_keys["bancho"];
    $heiakim_key = Leaderboard::$redis_keys["heiakim"];

    foreach (Gamemode::$modes as $mode) {
      /**
       * Bancho
       */
      $redis->zrem("$bancho_key:$mode", $this->id);
      $redis->zrem("$bancho_key:$mode:rscore", $this->id);
      $redis->zrem("$bancho_key:$mode:tscore", $this->id);

      /**
       * Bancho country
       */
      $redis->zrem("$bancho_key:$mode:$country", $this->id);
      $redis->zrem("$bancho_key:$mode:$country:rscore", $this->id);
      $redis->zrem("$bancho_key:$mode:$country:tscore", $this->id);

      /**
       * heia.kim
       */
      $redis->zrem("$heiakim_key:$mode", $this->id);
      $redis->zrem("$heiakim_key:$mode:rscore", $this->id);
      $redis->zrem("$heiakim_key:$mode:tscore", $this->id);

      /**
       * heia.kim country
       */
      $redis->zrem("$heiakim_key:$mode:$country", $this->id);
      $redis->zrem("$heiakim_key:$mode:$country:rscore", $this->id);
      $redis->zrem("$heiakim_key:$mode:$country:tscore", $this->id);
    }
  }

  /**
   * @return object
   */
  public function wipe()
  {
    /**
     * Remove cached data.
     */
    $this->remove_cached_data();

    /**
     * Begin a new database transaction.
     */
    $this->db_transaction();

    try {
      /**
       * Update stats.
       */
      $this->stats()->update([
        "tscore" => 0,
        "rscore" => 0,
        "pp" => 0,
        "plays" => 0,
        "playtime" => 0,
        "max_combo" => 0,
        "total_hits" => 0,
        "replay_views" => 0,
        "xh_count" => 0,
        "x_count" => 0,
        "sh_count" => 0,
        "s_count" => 0,
        "a_count" => 0,
        "acc" => 0.0,
      ]);

      /**
       * Remove all stat developments.
       */
      $this->stat_development()->delete();

      /**
       * Remove pins
       */
      $this->pins()->delete();

      /**
       * Delete all scores or roll everything back if one failed.
       */
      foreach ($this->scores as $Score) {
        /**
         * @var Score $Score
         */

        if (!$Score->remove()) {
          $this->db_rollback();
          return $this->error(
            "<strong>Error while removing your scores.</strong> " .
              $this->dd["TRY_OR_STAFF"]
          );
        }
      }

      /**
       * Update settings.
       */
      if (!$this->is_super_user()) {
        $this->settings()->update([
          "account_wipes_left" =>
          (int) $this->settings->account_wipes_left - 1,
          "account_wiped_at" => date("Y-m-d H:i:s", time()),
        ]);
      }

      /**
       * Commit all changes.
       */
      $this->db_commit();

      return $this->success(
        "<strong>Wiped!</strong> Good luck on your new path, my friend."
      );
    } catch (\Exception $e) {
      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error($e);
    }
  }

  /**
   * Fetches the modes of the user having set scores in in the
   * order descending from most played to less played.
   *
   * @return ?Score
   */
  public function favorite_modes()
  {
    return $this->scores()
      ->select("mode")
      ->selectRaw("COUNT(*) as score_count")
      ->groupBy("mode")
      ->orderByDesc("score_count")
      ->get();
  }

  /**
   * @return Score[]
   */
  public function top_scores_of_all_gumodes(bool $in_api = false)
  {
    $return = [];

    foreach (Gamemode::$modes as $key => $gumode) {
      $return[$key] = $this->scores()
        ->when($in_api, function ($q) {
          $q->select(ScoresGateway::$public_columns);
        })
        ->with("beatmap")
        ->whereHas("beatmap", function ($q) use ($in_api) {
          $q->when($in_api, function ($q) {
            $q->select(BeatmapsGateway::$public_columns);
          })->where("status", 2);
        })
        ->where("mode", $gumode)
        ->where("status", 2)
        ->orderBy("pp", "DESC")
        ->limit(1)
        ->first();
    }

    return $return;
  }

  /**
   * Fetches the most played beatmaps counted by played scores
   * connected to beatmaps.
   *
   * @param int $gumode The gulag mode.
   * @param int $limit The fetch limit.
   * @return object of Beatmaps or null.
   */
  public function most_played_beatmaps(int $gumode = 0, int $limit = 20)
  {
    $B = Beatmap::select("maps.*")
      ->join("scores", "scores.map_md5", "=", "maps.md5")
      ->where("scores.mode", $gumode)
      ->where("scores.userid", $this->id)
      ->groupBy("scores.map_md5")
      ->orderByRaw("COUNT(scores.id) DESC")
      ->limit($limit)
      ->get();

    return $B;
  }

  /**
   * Users can do some changes to their settings for just a set
   * amount. This function checks, if the User has available a
   * "change" to the given type.
   *
   * @param string $of
   * @return ?int
   */
  public function changes_left(string $of)
  {

    $which = match ($of) {
      "name" => "name_changes_left",
      "birthday" => "birthday_changes_left",
      "wipe" => "account_wipes_left",
      default => null,
    };

    return $which ? $this->settings->$which : null;
  }

  /**
   * Checks a mails availability
   *
   * @param string $mail The mail to check for
   * @return bool Whether or not it was successful
   */
  public static function mail_in_use(string $mail)
  {
    return User::where("email", $mail)->first();
  }

  /**
   * Complete validation of mail including checks for:
   * chars, availability
   * @return bool
   */
  public function mail_usable(string $mail)
  {
    $mail = Request::escape_params(["email" => $mail]);

    if (!Validate::mail($mail->email)) {
      return false;
    }

    if (!self::mail_in_use($mail->email)) {
      return false;
    }

    return true;
  }

  /**
   * @param string $password
   * @return string
   */
  public static function encrypt_password(string $password)
  {
    $pw_md5 = md5($password);
    $options = [
      "cost" => 12,
    ];

    return password_hash($pw_md5, PASSWORD_BCRYPT, $options);
  }

  /**
   * @param string $password
   * @param string $hash
   * @return bool
   */
  public static function decrypt_password(
    string $password,
    string $hash,
    bool $md5 = true
  ) {

    $md5_password = $md5 ? md5($password) : $password;
    return password_verify($md5_password, $hash);
  }

  /**
   * @return bool
   */
  public function is_verified()
  {
    return $this->data()->priv > 2;
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,, VALIDATION ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return bool
   */
  public static function mail_has_valid_format(string $mail)
  {
    return filter_var($mail, FILTER_VALIDATE_EMAIL) !== false;
  }

  /**
   * Returns all the users being premium members.
   *
   * @return ?User
   */
  public static function premium_members()
  {
    return self::whereRaw("priv & ? != 0", [Privilege::SUPPORTER->value]);
  }
}
