<?php

namespace Heiakim\Model;

use Locale;
use Heiakim\Justin;
use Heiakim\Geo\Geo;
use Heiakim\Http\Request;
use Heiakim\API\Bancho;
use Heiakim\APIGateway\Count\BeatmapsGateway;
use Heiakim\APIGateway\Score\ScoresGateway;
use Heiakim\Application\Logger;
use Heiakim\Enum\Privilege;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Validate\Validate;
use Heiakim\Model\Session;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Image;
use Heiakim\Model\Search;
use Heiakim\Model\Change;
use Heiakim\Model\Score;
use Heiakim\Model\User\UserPin;
use Heiakim\Model\User\UserSettings;
use Heiakim\Model\User\UserSettingsPrivacy;
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
use DateTime;
use Heiakim\Model\Squad\SquadPostVote;
use Heiakim\Model\User\UserSettingsPremium;
use Heiakim\Registry\RedisRegistry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

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
   * @return string
   */
  public function edit(object $params)
  {

    $Change = null;

    $this->db_transaction();

    try {

      # ? Password
      if (isset($params->password, $params->current_password)) {
        $current_password = htmlspecialchars_decode($params->current_password);
        $current_pw_bcrypt = $this->pw_bcrypt;
        $User = User::verify_login($this->name, $current_password);

        if (!$User)
          die(error("<strong>Your credentials seem to be wrong!</strong>"));

        if ($params->current_password === $params->password)
          die(error("<strong>This is your current password.</strong> Choose another one!"));

        # Need to decode special chars as the Controller will automatically encode it.
        $password = htmlspecialchars_decode($params->password);

        $this->set_password_invalid($password);

        /**
         * @var Change
         */
        $Change = $this->changes()
          ->make([
            "type" => "password",
            "previous_value" => $this->pw_bcrypt,
            "updated_value" => $current_pw_bcrypt,
            "updated_at" => null,
          ]);
      }

      # ? Mail
      if (isset($params->email))
        $this->set_mail_invalid($params->email);

      # ? Name
      if (isset($params->name)) {

        # No name changes left?
        if ($this->settings->name_changes_left < 1)
          die(error(
            "<strong>No name changes left!</strong> " .
              $this->dd["UNLOCK_MORE_WITH_PREMIUM"]
          ));

        $this->set_name_invalid($params->name);

        /**
         * @var Change
         */
        $Change = $this->name_changes()
          ->make([
            "type" => "name",
            "previous_value" => $this->name,
            "updated_value" => $params->name,
          ]);

        # Remove one name change.
        $this->settings->decrement("name_changes_left");
      }

      # ? Preferred Gamemode
      if (isset($params->mode, $params->mod)) {
        $this->preferred_mode = Gamemode::find_gumode(
          $params->mode,
          $params->mod,
          array: false
        );
        unset($params->mode, $params->mod);
      }

      # Save anything & commit!
      $this->save();
      $this->settings->save();
      $Change?->save();
      $this->db_commit();

      return $this;
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      die(error());
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

    # User is a squad owner?
    if ($SquadUser && $SquadUser->is_owner()) {
      return error(
        "<strong>Please transfer the ownership of your squad, before you delete your account.</strong>"
      );
    }

    # Begin new database transaction.
    $this->db_transaction();

    try {

      # ? Authentications
      $CurrentUser->authentications()->delete();

      # ? Changes
      $CurrentUser->changes()->delete();

      # ? Squad
      $CurrentUser->squad_requests()?->delete();
      $CurrentUser->squad_feed_item()?->delete();
      $SquadUser?->delete();

      # ? Client hashes
      $CurrentUser->client_hashes()->delete();

      # ? Connect credentials
      $CurrentUser->connections()->delete();

      # ? Favourites
      $CurrentUser->osu_favorites()->delete();

      # ? Feedback
      $CurrentUser->feedback()->delete();

      # ? Images
      # This will only delete images of type __user__. Squad images will still be a-
      # vailable.
      $CurrentUser->images()->delete();

      # ? Ingame logins
      $CurrentUser->osu_ingame_logins()->delete();

      # ? Mailings
      $CurrentUser->mailings()->delete();

      # ? Manager
      $CurrentUser->manager_authentications()->delete();
      $CurrentUser->manager_logs()->delete();
      $CurrentUser->manager_sessions()->delete();
      $CurrentUser->manager_user()->delete();

      # ? Beatmap Requests
      $CurrentUser->beatmap_requests()->delete();

      # ? Notifications
      $CurrentUser->notifications()->delete();

      # ? Orders
      $CurrentUser->orders->each(function (Order $Order) {
        $Order->paypal()->delete();
        $Order->delete();
      });

      # ? Password Resets
      $CurrentUser->password_resets()->delete();

      # ? Profile
      $CurrentUser->profile()->delete();

      # ? Ratings
      $CurrentUser->osu_ratings()->delete();

      # ? Reactions
      $CurrentUser->reactions()->delete();

      # ? Relationships
      Relationship::whereRaw("user1 = ? OR user2 = ?", [$this->id, $this->id])
        ->get()
        ?->each(fn(Relationship $R) => $R->delete());

      # ? Reports
      $CurrentUser->reports()->delete();

      # ? Restrictions
      $CurrentUser->restrictions()->delete();

      # ? Appeals
      $CurrentUser->appeals()->delete();

      # ? Scores
      $CurrentUser->scores->each(function (Score $Score) {
        $Score->comments()->delete();
        $Score->reactions()->delete();
        $Score->thread_post_attachments()->delete();
        $Score->delete();
      });

      # ? Searches
      $CurrentUser->searches()->delete();

      # ? Sessions
      $CurrentUser->sessions()->delete();

      # ? Stats
      $CurrentUser->stats()->delete();

      # ? Stat Developments
      $CurrentUser->stat_development()->delete();

      # ? Threads
      $CurrentUser->threads->each(function ($Thread) {
        $Thread->posts->each(function ($Post) {
          $Post->attachments->each->delete();
          $Post->delete();
        });
        $Thread->delete();
      });

      # ? Achievements
      $CurrentUser->achievements()->delete();

      # ? Pins
      $CurrentUser->pins()->delete();

      # ? Settings
      $CurrentUser->settings()->delete();
      $CurrentUser->privacy()->delete();
      $CurrentUser->premium()->delete();

      # ! DELETE THE USER OMG
      $CurrentUser->delete();

      # Commit!
      $this->db_commit();

      return success("<strong>See you l8er boi.</strong>");
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return error(
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

    $email = trim($email);

    # Email is current one?
    if ($email === $this->email)
      return die(error("This is your mail already 🥹"));

    # Mail is of invalid format?
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return die(error("Mail invalid!"));

    # Mail exists on another User?
    if (self::where("email", $email)->exists())
      return die(error("You can't use this E-Mail brother!"));

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

    # Set some validation if wanted. I think the user can decide for themselves, if
    # their password should be secure or not. I don't see it as my task to force them
    # 🙂

    $this->pw_bcrypt = self::encrypt_password($password);
  }

  /**
   * @return Profile
   */
  public function create_default_profile()
  {
    return $this->profile()
      ->create([
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
    return $Content->user?->is($this) ?? false;
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

  // ------------------------------------------------
  // Authentications --------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Authentication>
   */
  public function authentications()
  {
    return $this->hasMany(Authentication::class);
  }

  // ------------------------------------------------
  // Ingame -----------------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<ClientHash>
   */
  public function client_hashes()
  {
    return $this->hasMany(ClientHash::class, "userid", "id");
  }

  /**
   * @return HasMany<OsuFavorite>
   */
  public function osu_favorites()
  {
    return $this->hasMany(OsuFavorite::class, "userid", "id");
  }

  /**
   * @return HasMany<OsuIngameLogin>
   */
  public function osu_ingame_logins()
  {
    return $this->hasMany(OsuIngameLogin::class, "userid", "id");
  }

  /**
   * @return HasMany<OsuRating>
   */
  public function osu_ratings()
  {
    return $this->hasMany(OsuRating::class, "userid", "id");
  }

  /**
   * @return HasMany<Achievement>
   */
  public function achievements()
  {
    return $this->hasMany(Achievement::class, "userid", "id");
  }

  // ------------------------------------------------
  // Manager ----------------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<ManagerAuthentication>
   */
  public function manager_authentications()
  {
    return $this->hasMany(ManagerAuthentication::class);
  }

  /**
   * @return HasMany<ManagerLog>
   */
  public function manager_logs()
  {
    return $this->hasMany(ManagerLog::class);
  }

  /**
   * @return HasMany<ManagerSession>
   */
  public function manager_sessions()
  {
    return $this->hasMany(ManagerSession::class);
  }

  /**
   * @return HasOne<ManagerUser>
   */
  public function manager_user()
  {
    return $this->hasOne(ManagerUser::class);
  }

  // ------------------------------------------------
  // Threads ----------------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Thread>
   */
  public function threads()
  {
    return $this->hasMany(Thread::class);
  }

  /**
   * @return HasMany<ThreadPost>
   */
  public function thread_posts()
  {
    return $this->hasMany(ThreadPost::class);
  }

  // ------------------------------------------------
  // SSO --------------------------------------------
  // ------------------------------------------------

  /**
   * @return bool
   */
  public function signed_up_through_sso()
  {
    return $this->github || $this->discord || $this->osu;
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
   * @return HasOne<ConnectionOsu>
   */
  public function osu()
  {
    return $this->hasOne(ConnectionOsu::class)
      ->where("provider", "osu!");
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
   * @return HasOne<ConnectionGithub>
   */
  public function github()
  {
    return $this->hasOne(ConnectionGithub::class)
      ->where("provider", "github");
  }

  // ------------------------------------------------
  // Restrictions -----------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Restriction>
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
   * @return void
   */
  public function restrict(object $params)
  {

    # Remove redis cached data.
    $this->remove_cached_data();

    # Remove unrestricted privileges. This will also update the frozen state.
    $this->remove_privileges(Privilege::UNRESTRICTED);

    $reason = !empty($params->reason) ? $params->reason : null;

    /**
     * @var Restriction
     */
    $Restriction = $this->restrictions()
      ->create([
        "reason" => $reason,
        "updated_at" => null,
      ]);

    /**
     * @var Notification
     */
    $this->notifications()
      ->create([
        "type" => "__system__/restriction",
        "reference_id" => null,
        "reference_2_id" => $Restriction->id,
        "updated_at" => null,
      ]);

    # Send mail, if the user has one set.
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

      # Send it & create a new Mailing.
      if ((new Mail())->create($this->email, $mail_subject, $mail_body)) {
        $this->mailings()
          ->create([
            "template" => $mail_template,
            "email" => $this->email,
            "subject" => $mail_subject,
            "token" => $mail_token,
            "updated_at" => null,
          ]);
      }
    }
  }

  /**
   * @return HasMany<RestrictionAppeal>
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
    # User is not frozen nor restricted?
    if (!$this->frozen_at || !$this->is_restricted())
      return null;

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
    # User is not restricted nor frozen?
    if (!$this->frozen_at && !$this->is_restricted())
      return null;

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

    # User is not restricted nor frozen?
    if (!$this->frozen_at && !$this->is_restricted())
      return null;

    return $this->appeals()
      ->whereRaw("updated_at > ? & status = 'DECLINED'", $this->frozen_at)
      ->latest()
      ->first();
  }

  /**
   * User is currently restricted and there is an appeal waiting for being processed
   * by a staff member
   *
   * @return ?RestrictionAppeal
   */
  public function current_appeal_after_restriction_waiting_period()
  {
    return $this->is_restricted() && $this->current_appeal()
      ? $this->current_appeal()
      : null;
  }

  /**
   * @return ?RestrictionAppeal
   */
  public function declined_live_play()
  {

    # User is frozen?
    if (!$this->frozen_at)
      return null;

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

    # User is frozen?
    if (!$this->frozen_at)
      return null;

    return $this->appeals()
      ->whereRaw("created_at > ?", $this->frozen_at)
      ->where("status", "DECLINED")
      ->whereNotNull("is_appeal")
      ->first();
  }

  /**
   * Gets the time left for the lockage of a new appeal as a human readable string.
   *
   * @return ?string
   */
  public function appeal_locked_for()
  {

    # User is not restricted?
    if (!$this->is_restricted())
      return null;

    $restrictions_count = $this->restrictions()->count();
    $interval = match ($restrictions_count) {
      1 => "+1 second",
      2 => "+5 seconds",
      default => "+1 second",
    };

    /**
     * @var Restriction
     */
    $Restriction = $this->restrictions()
      ->latest()
      ->first();

    /**
     * @var DateTime
     */
    $appeal_ready_time = (new DateTime($Restriction->created_at))->modify(
      $interval
    );

    return Time::left(
      $appeal_ready_time->format("Y-m-d H:i:s"),
      exact_hours: false,
      full: false
    );
  }

  // ------------------------------------------------
  // Requests ---------------------------------------
  // ------------------------------------------------

  /**
   * @return ?BeatmapRequest
   */
  public function beatmap_requests()
  {
    return $this->hasMany(BeatmapRequest::class, "player_id", "id");
  }

  // ------------------------------------------------
  // Display ----------------------------------------
  // Template related things ------------------------
  // ------------------------------------------------

  /**
   * @param int $gumode
   * @param bool $big_cover
   * @return void
   *
   * NOTE: Includes /helper/beatmaps/_cover.php
   */
  public function headline_cover(int $gumode = 0, bool $big_cover = false)
  {

    $big_cover ??= true;

    # User has set a custom headline through premium settings?
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
   * @param bool $gdpr
   * @return void
   *
   * NOTE: Includes /helper/user/_image.php
   */
  public function image(bool $gdpr = true)
  {
    $user_image_id = $this->id;
    $gdpr ??= true;
    $deleted = $this->deleted ?? null;

    include ROOT . "/app/templates/helper/users/_image.php";
  }

  // ------------------------------------------------
  // Birthday ---------------------------------------
  // ------------------------------------------------

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
   * @param string $year
   * @return Collection<Feedback>
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
   * Checks if a Feedback from this User to another one exists from a specific year.
   *
   * @param User $User
   * @param string $year
   * @return bool
   */
  public function cheered_for_birthday(User $User, string $year)
  {
    return Feedback::where([
      "user_id" => $this->id,
      "reference_id" => $User->id,
      "type" => "birthday_cheer",
      ["created_at", "LIKE", "%$year%"],
    ])
      ->exists();
  }

  /**
   * @return HasMany<Order>
   */
  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  /**
   * @return HasMany<Order>
   */
  public function gifted_orders()
  {
    return $this->hasMany(Order::class, "user_2_id", "id");
  }

  /**
   * @return HasMany<Search>
   */
  public function searches()
  {
    return $this->hasMany(Search::class);
  }

  /**
   * @return HasMany<Comment>
   */
  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  /**
   * @return HasMany<UserPin>
   */
  public function pins()
  {
    return $this->hasMany(UserPin::class);
  }

  /**
   * Checks if a given instance of a class has been pinned by the User.
   *
   * @param Score|Beatmap $Reference
   * @return Score|Beatmap|null
   */
  public function has_pinned(Score|Beatmap $Reference)
  {
    return $this->pins()
      ->where([
        "type" => strtolower(class_basename($Reference)),
        "reference_id" => $Reference->id
      ])
      ->first();
  }

  /**
   * @return HasMany<Report>
   */
  public function reports()
  {
    return $this->hasMany(Report::class);
  }

  /**
   * @return HasMany<Change>
   */
  public function changes()
  {
    return $this->hasMany(Change::class);
  }

  /**
   * @return HasMany<Change>
   */
  public function name_changes()
  {
    return $this->hasMany(Change::class)
      ->where("type", "name");
  }

  /**
   * @return HasMany<Change>
   */
  public function password_changes()
  {
    return $this->hasMany(Change::class)
      ->where("type", "password");
  }

  /**
   * @return HasMany<PasswordReset>
   */
  public function password_resets()
  {
    return $this->hasMany(PasswordReset::class);
  }

  /**
   * @return HasMany<Image>
   */
  public function images()
  {
    return $this->hasMany(Image::class)
      ->where("type", "LIKE", "%__user__%");
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
  public static function verify_login(
    string $login,
    string $password,
    bool $die = false
  ) {

    /**
     * @var User
     */
    $User = self::where(function ($q) use ($login) {
      $q->where("name", $login)->orWhere("email", $login);
    })
      ->first();

    # No user found?
    if (!$User)
      return $die ? die(error("<strong>No! 🙂‍↔️</strong>")) : null;

    # Password doesn't decrypt hash?
    if (!self::decrypt_password($password, $User->pw_bcrypt))
      return $die ? die(error("<strong>No! 🙂‍↔️</strong>")) : null;

    return $User;
  }

  /**
   * @return ?Privilege[]
   */
  public function privileges()
  {

    $privileges = [];

    # User is not yet verified (priv === 0).
    if ((int) $this->priv === 0) {
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

      # Any other case.
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
   * @param Privilege $Privileges
   * @return object
   */
  public function add_privileges(Privilege ...$Privileges)
  {

    $updated_privs = $this->priv;

    foreach ($Privileges as $Privilege) {

      # User has privileges already?
      if ($this->has_privileges_of($Privilege))
        continue;

      # Add it up!
      $updated_privs += $Privilege->value;
    }

    # Update it!
    $this->update(["priv" => $updated_privs]);

    return success("<strong>Privileges updated!</strong>");
  }

  /**
   * @param Privilege $Privileges
   * @return object
   */
  public function remove_privileges(Privilege ...$Privileges)
  {

    $updated_privs = $this->priv;

    foreach ($Privileges as $Privilege) {

      # Continue, if the User is not UNRESTRICTED.
      if ($Privilege === Privilege::UNRESTRICTED) {
        if (!$this->has_privileges_of($Privilege))
          continue;
      }

      # Substract it!
      $updated_privs -= $Privilege->value;
    }

    # Update it
    $this->update(["priv" => $updated_privs]);

    return success("<strong>Privileges updated!</strong>");
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
   * @param Privilege $Privilege
   * @return bool
   */
  public function missing_privileges_of(Privilege $Privilege)
  {
    return ($this->priv & $Privilege->value) === 0;
  }

  /**
   * Allows certain roles and users to interact with everything.
   *
   * @return bool
   */
  public function is_super_user()
  {
    return in_array($this->id, [3]);
  }

  // ------------------------------------------------
  // Country ----------------------------------------
  // ------------------------------------------------

  /**
   * @return string
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
   * @return BelongsTo<Country>
   */
  public function country()
  {
    return $this->belongsTo(Country::class, "country", "abbreviation");
  }

  /**
   * @return void
   *
   * NOTE: includes /app/templates/helper/_image_country.php
   */
  public function country_icon()
  {
    $country_abbreviation = $this->country;
    return include ROOT . "/app/templates/helper/_image_country.php";
  }

  // ------------------------------------------------
  // Rankings ---------------------------------------
  // ------------------------------------------------

  /**
   * Fetches all rankings of this user from flobal to country and possible gamemodes.
   *
   * @param int $gumode
   * @return object
   */
  public function get_rankings(int $gumode)
  {

    /**
     * @var \Redis
     */
    $Redis = $this->redis();
    $return = (object) [];
    $country = $this->data()->country;
    $redis_base_key = RedisRegistry::$leaderboard_keys["players"];

    # Build redis keys.
    $global_rank = $Redis->zrevrank(
      "$redis_base_key:$gumode",
      $this->id
    );

    $country_rank = $Redis->zrevrank(
      "$redis_base_key:$gumode:$country",
      $this->id
    );

    $return->global = $global_rank !== false ? $global_rank + 1 : null;
    $return->country = $country_rank !== false ? $country_rank + 1 : null;
    $return->development = $this->get_rank_development($gumode);

    return $return;
  }

  /**
   * @return object<object>
   */
  public function get_all_rankings()
  {

    /**
     * @var \Redis
     */
    $Redis = $this->redis();
    $return = (object) [];
    $country = $this->data()->country;
    $redis_base_key = RedisRegistry::$leaderboard_keys["players"];

    foreach (Gamemode::$modes as $key => $gumode) {
      $global_rank = $Redis->zrevrank(
        "$redis_base_key:$gumode",
        $this->id
      );

      $country_rank = $Redis->zrevrank(
        "$redis_base_key:$gumode:$country",
        $this->id
      );

      $return->$key = (object) [
        "mode" => $gumode,
        "global" => $global_rank !== null ? $global_rank + 1 : null,
        "country" => $country_rank !== null ? $country_rank + 1 : null,
        "development" => $this->get_rank_development($gumode),
      ];
    }

    return $return;
  }

  /**
   * Evaluates if the user has raised or dropped in ranks and gives the exact count.
   *
   * @param int $gumode
   * @return object<object>
   */
  public function get_rank_development(int $gumode)
  {

    /**
     * @var \Redis
     */
    $Redis = $this->redis();
    $country = $this->country;

    # Global backup.
    $redis_key = RedisRegistry::$leaderboard_keys["players-climb"];
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

    # Current rankings.
    $redis_key = RedisRegistry::$leaderboard_keys["players"];
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

    return (object) [
      "global" => (object) [
        "performance" =>
        $current_rank_global !== null
          ? $current_rank_global - $old_rank_global
          : null,
        "score" =>
        $current_rank_score_global !== null
          ? $current_rank_score_global - $old_rank_score_global
          : null,
      ],
      "country" => (object) [
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
    return $this->profile()
      ->create([
        "sections_visibility" => json_encode(Profile::$sections_visibility),
      ]);
  }

  /**
   * @return HasOne<Profile>
   */
  public function profile()
  {
    return $this->hasOne(Profile::class);
  }

  /**
   * Profile parts are saved as JSON encoded strings. This function will decode them
   * and return objects.
   *
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
   * @return HasMany<Session>
   */
  public function sessions()
  {
    return $this->hasMany(Session::class);
  }

  /**
   * @return HasMany<Stat>
   */
  public function stats()
  {
    return $this->hasMany(Stat::class, "id", "id");
  }

  /**
   * @return HasMany<StatDevelopment>
   */
  public function stat_development()
  {
    return $this->hasMany(StatDevelopment::class);
  }

  /**
   * @return HasOne<UserSettings>
   */
  public function settings()
  {
    return $this->hasOne(UserSettings::class);
  }

  /**
   * @return HasOne<UserSettingsPrivacy>
   */
  public function privacy()
  {
    return $this->hasOne(UserSettingsPrivacy::class);
  }

  /**
   * @return bool
   */
  public function has_accepted_privacy_policies()
  {
    return $this->privacy?->accepts_policies;
  }

  /**
   * Getter for user's status on bancho.
   * TODO: Rename this as it's not bancho, but our own system. Bancho is osu!
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

  // ------------------------------------------------
  // Authorization ----------------------------------
  // ------------------------------------------------

  /**
   * @param Image $Content
   * @param bool $die_on_error
   * @return bool
   *
   * NOTE: Will die on error.
   */
  public function authorize_content_touch(
    Image $Content,
    $die_on_error = true
  ) {

    $authorized = $this->is_super_user() || $this->is($Content->user);

    return $die_on_error
      ? (!$authorized ? die(error("!NO_PERMISSIONS")) : true)
      : (!$authorized ? false : true);
  }

  // ------------------------------------------------
  // Premium ----------------------------------------
  // ------------------------------------------------

  /**
   * @return HasOne<UserSettingsPremium>
   */
  public function premium()
  {
    return $this->hasOne(UserSettingsPremium::class);
  }

  /**
   * @return bool
   */
  public function is_premium()
  {
    return $this->has_privileges_of(Privilege::SUPPORTER);
  }

  /**
   * @param string $datetime
   * @return void
   */
  public function give_premium(string $datetime)
  {

    $current_premium_time = max($this->donor_end, time());
    $future_end_time = strtotime($datetime, $current_premium_time);

    # Update it.
    $this->update([
      "donor_end" => $future_end_time,
    ]);

    # Create UserSettingsPremium.
    if (!$this->premium)
      $this->premium()->create();

    # Add supporter privileges.
    $this->add_privileges(Privilege::SUPPORTER);
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

  // ------------------------------------------------
  // Notifications ----------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Notification>
   */
  public function notifications()
  {
    return $this->hasMany(Notification::class);
  }

  /**
   * @return bool
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

    $last_checked = $this->settings->checked_notifications_at
      ?? "2000-01-01 01:01:01";

    return $this->notifications()
      ->where("created_at", ">", $last_checked)
      ->count();
  }

  // ------------------------------------------------
  // Squads -----------------------------------------
  // ------------------------------------------------

  /**
   * @return BelongsTo<Squad>
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
   * @param ?Squad $in
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
   * @return HasOne<SquadUser>
   */
  public function squad_user()
  {
    return $this->hasOne(SquadUser::class);
  }

  /**
   * Returns just the clan_id.
   *
   * @return int
   */
  public function has_squad()
  {
    return $this->clan_id;
  }

  /**
   * Users can just join a new squad if they are free of a current squad, are not re-
   * stricted in any way and if the squad is completly public.
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
   * @return HasMany<SquadRequest>
   */
  public function squad_requests()
  {
    return $this->hasMany(SquadRequest::class, "user_id")
      ->orWhere("reference_id", $this->id);
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

  /**
   * @param Squad $Squad
   * @return ?string
   */
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
   * @param Squad $Squad
   * @return ?SquadRequest
   */
  public function has_invite_from(?Squad $Squad)
  {
    return $Squad?->invites()
      ->where("reference_id", $this->id)
      ->first();
  }

  /**
   * @param Squad $Squad
   * @return ?SquadRequest
   */
  public function requested_to_join(Squad $Squad)
  {
    return SquadRequest::where([
      "user_id" => $this->id,
      "type" => "join",
      "clan_id" => $Squad->id,
    ])
      ->first();
  }

  /**
   * @param Squad $Squad
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
   * @return HasMany<SquadFeedItem>
   */
  public function squad_feed_item()
  {
    return $this->hasMany(SquadFeedItem::class);
  }

  /**
   * @return HasMany<SquadPost>
   */
  public function squad_post()
  {
    return $this->hasMany(SquadPost::class);
  }

  /**
   * @return HasMany<SquadPostVote>
   */
  public function squad_post_votes()
  {
    return $this->hasMany(SquadPostVote::class);
  }

  /**
   * @return HasMany<SquadPostComment>
   */
  public function squad_post_comments()
  {
    return $this->hasMany(SquadPostComment::class);
  }

  /**
   * @param SquadPost $Content
   * @return ?SquadPostVote
   */
  public function has_voted_for(SquadPost $Content)
  {
    return $this->squad_post_votes()
      ->where("post_id", $Content->id)
      ->whereNull("deleted_at")
      ->first();
  }

  /**
   * @param SquadPost $Content
   * @return ?SquadPostPollAnswer
   */
  public function has_answered_poll(SquadPost $Content)
  {
    return $Content
      ->poll_answers()
      ->where("user_id", $this->id)
      ->whereNull("deleted_at")
      ->first();
  }

  // ------------------------------------------------
  // Relationships ----------------------------------
  // User making a request is user1 -----------------
  // ------------------------------------------------

  /**
   * @return BelongsToMany<Relationship>
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
   * @return BelongsToMany<Relationship>
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
   * A string to determine which action to show for the User viewing a Profile.
   *
   * @param User $User
   * @return string
   */
  public function follow_action_display(User $User)
  {
    /**
     * @var bool
     */
    $viewing_self = $this->id === $User->id;

    # Viewing own profile?
    if ($viewing_self) {
      return "manage";
    }

    # User watching the profile is not following, but the user being watched does fo-
    # llow the one watching. Special case, show follow back!
    if ($this->follows($User) && !$User->follows($this)) {
      return "refollow";
    }

    # User watching the profile is not following.
    if (!$User->follows($this)) {
      return "follow";
    }

    # User watching the profile is following.
    if ($User->follows($this)) {
      return "unfollow";
    }
  }

  // ------------------------------------------------
  // Feedback & Reactions ---------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Feedback>
   */
  public function feedback()
  {
    return $this->hasMany(Feedback::class);
  }

  /**
   * @return HasMany<Feedback>
   */
  public function favorite_beatmaps()
  {
    return $this->feedback()
      ->where("type", "beatmap");
  }

  /**
   * @return HasMany<Feedback>
   */
  public function favorite_artists()
  {
    return $this->feedback()
      ->where("type", "artist");
  }

  /**
   * @return HasMany<Reaction>
   */
  public function reactions()
  {
    return $this->hasMany(Reaction::class);
  }

  /**
   * @param string $type
   * @param int $reference_id
   * @return bool
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

  // ------------------------------------------------
  // Scores -----------------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Score>
   */
  public function scores()
  {
    return $this->hasMany(Score::class, "userid", "id");
  }

  /**
   * @return Collection<Score>
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
     * @var Collection<Score>
     */
    $Scores = $this->scores()

      # API: Only public columns.
      ->when($from_api, function ($q) {
        $q->select(ScoresGateway::$public_columns);
      })

      # Filter by gumode.
      ->when($gumode !== null, function ($q) use ($gumode) {
        $q->where("scores.mode", $gumode);
      })

      # Only loved & ranked beatmaps.
      ->whereHas("beatmap", function ($q) {
        $q->whereIn("maps.status", [2, 5]);
      })

      # Filter out all scores that are not the #1 for this User.
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

      # Only submitted scores.
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

  // ------------------------------------------------
  // Mailing ----------------------------------------
  // ------------------------------------------------

  /**
   * @return HasMany<Mailing>
   */
  public function mailings()
  {
    return $this->hasMany(Mailing::class);
  }

  // ------------------------------------------------
  // Redis Cache ------------------------------------
  // ------------------------------------------------

  /**
   * @return void
   */
  public function refresh_cached_data()
  {

    /**
     * @var \Redis
     */
    $redis = $this->redis();

    /**
     * @var Country
     */
    $Country = $this->country()->first();
    $country = $Country->abbreviation;
    $bancho_key = RedisRegistry::$leaderboard_keys["osu!"];
    $heiakim_key = RedisRegistry::$leaderboard_keys["players"];

    foreach (Gamemode::$modes as $mode) {
      $Stat = $this->stats()->where("mode", $mode)->first();

      if (!$Stat) {
        $Stat = $this->stats()->create([
          "mode" => $mode,
        ]);

        $Stat = $Stat->fresh();
      }

      # Bancho.
      $redis->zadd("$bancho_key:$mode", $Stat->pp, $this->id);
      $redis->zadd("$bancho_key:$mode:rscore", $Stat->rscore, $this->id);
      $redis->zadd("$bancho_key:$mode:tscore", $Stat->tscore, $this->id);

      # Bancho country.
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

      # heia.kim.
      $redis->zadd("$heiakim_key:$mode", $Stat->pp, $this->id);
      $redis->zadd("$heiakim_key:$mode:rscore", $Stat->rscore, $this->id);
      $redis->zadd("$heiakim_key:$mode:tscore", $Stat->tscore, $this->id);

      # heia.kim country.
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
   * @return void
   */
  public function remove_cached_data()
  {

    /**
     * @var \Redis
     */
    $redis = $this->redis();

    /**
     * @var Country
     */
    $Country = $this->country()->first();
    $country = $Country->abbreviation;
    $bancho_key = RedisRegistry::$leaderboard_keys["osu!"];
    $heiakim_key = RedisRegistry::$leaderboard_keys["players"];

    foreach (Gamemode::$modes as $mode) {

      # Bancho.
      $redis->zrem("$bancho_key:$mode", $this->id);
      $redis->zrem("$bancho_key:$mode:rscore", $this->id);
      $redis->zrem("$bancho_key:$mode:tscore", $this->id);

      # Bancho country.
      $redis->zrem("$bancho_key:$mode:$country", $this->id);
      $redis->zrem("$bancho_key:$mode:$country:rscore", $this->id);
      $redis->zrem("$bancho_key:$mode:$country:tscore", $this->id);

      # heia.kim.
      $redis->zrem("$heiakim_key:$mode", $this->id);
      $redis->zrem("$heiakim_key:$mode:rscore", $this->id);
      $redis->zrem("$heiakim_key:$mode:tscore", $this->id);

      # heia.kim country.
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

    # Begin a database transaction! Nothing to leave behind!
    $this->db_transaction();

    try {

      $this->remove_cached_data();
      $this->stats()->update(new Stat()->getAttributes());
      $this->stat_development()->delete();
      $this->pins()->delete();
      $this->scores()->each(fn(Score $Score) => $Score->remove());

      # For any user that is not super, substract 1 account wipe left.
      if (!$this->is_super_user()) {
        $this->settings()->update([
          "account_wipes_left" =>
          (int) $this->settings->account_wipes_left - 1,
          "account_wiped_at" => date("Y-m-d H:i:s", time()),
        ]);
      }

      # Commit!
      $this->db_commit();

      return success("<strong>Wiped!</strong> Good luck on your new path, friend.");
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return error($e->getMessage());
    }
  }

  /**
   * Fetches the modes of the user having set scores in in the order descending from
   * most played to less played.
   *
   * @return Collection<Score>
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
   * Gets the most played beatmaps counted by related scores connected to them.
   *
   * @param int $gumode
   * @param int $limit
   * @return Collection<Beatmap>
   */
  public function most_played_beatmaps(int $gumode = 0, int $limit = 20)
  {
    return Beatmap::select("maps.*")
      ->join("scores", "scores.map_md5", "=", "maps.md5")
      ->where("scores.mode", $gumode)
      ->where("scores.userid", $this->id)
      ->groupBy("scores.map_md5")
      ->orderByRaw("COUNT(scores.id) DESC")
      ->limit($limit)
      ->get();
  }

  /**
   * Users can do some changes to their settings for just a set amount. This function
   * checks, if the User has available a "change" to the given type.
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
   * @param bool $md5
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

  // ------------------------------------------------
  // ------------------------------------------------
  // ------------------------------------------------

  /**
   * Returns all the users being premium members.
   *
   * @return ?User
   */
  public static function premium_members()
  {
    return static::whereRaw("priv & ? != 0", [Privilege::SUPPORTER->value])
      ->get();
  }
}
