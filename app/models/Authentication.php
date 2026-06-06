<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Application\Logger;
use Heiakim\Http\Request;
use Heiakim\Utils\Utils;
use Heiakim\Mail\Mail;
use Heiakim\Trait\HasDefaultUser;
use Heiakim\Model\User;
use Heiakim\Validate\Validate;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Authentication extends Justin
{
  use SoftDeletes;
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "email",
    "type",
    "token",
    "code",
    "value",
    "remote_address",
  ];

  protected static array $types = [
    "user:create",
    "user:delete",
    "user:update:email",
    "user:wipe",
    "squad:user:delete",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser ?? null;

    /**
     * @var string
     */
    $token = Utils::random_alpha_token(32);

    /**
     * @var string
     */
    $code = Utils::random_numeric_token(6);

    /**
     * Type is set?
     */
    if (!$params->type || !in_array($params->type, static::$types))
      return request_error("<strong>What happened kurwa?</strong> 😂");

    /**
     * New user creation is handled differently.
     */
    if ($params->type === "user:create")
      return $this->create_user($params);

    /**
     * User needs to be logged in any case except the user creation.
     */
    if (!$CurrentUser)
      return request_error("!NOT_LOGGED");

    /**
     * If the value is set but there is nothing in it, return an error.
     */
    if (isset($params->value) && !trim($params->value))
      return request_error(match ($params->type) {
        static::$types[2] => "<strong>Put a valid mail bro</strong> 🤪",
        default => "<strong>Nothing to authenticate!</strong> Put something in! 🤭"
      });

    /**
     * Creating a user by now uses a different procedure, because
     * no user requires to be logged in.
     * @var ?Authentication
     */
    $Authentication =
      $CurrentUser
      ->authentications()
      ->where("type", $params->type)
      ->whereNull("deleted_at")
      ->first();

    /**
     * Create a new authentication for the current user.
     */
    $Authentication
      ? $Authentication->update([
        "email" => $CurrentUser->email,
        "type" => $params->type,
        "token" => $token,
        "code" => $code,
        "value" => $params->value ?? null,
        "remote_address" => Request::get_remote_address(),
      ])
      : $Authentication = $params->CurrentUser
      ->authentications()
      ->create([
        "email" => $CurrentUser->email,
        "type" => $params->type,
        "token" => $token,
        "code" => $code,
        "value" => $params->value ?? null,
        "remote_address" => Request::get_remote_address(),
      ]);

    /**
     * Send a mail.
     */
    return !$Authentication->send_mail(
      $code,
      $CurrentUser->email,
      $CurrentUser->name,
    )
      ? $this->error("<strong>We had trouble sending a mail.</strong> Try again!")
      : $this->success("<strong>A code has been sent to your e-mail address.</strong> Be sure to check your spam folder, too!");
  }

  /**
   * @return HasOne<Connection>
   */
  public function connection()
  {
    return $this->hasOne(Connection::class, "authentication_token", "token");
  }

  /**
   * @param object $params
   * @return object
   */
  public function create_user(object $params)
  {

    /**
     * @var string
     */
    $token = Utils::random_alpha_token(32);

    /**
     * Mail invalid?
     */
    if (!Validate::mail($params->email))
      return $this->error("<strong>Friend, this mail is not valid 🫣</strong>");

    /**
     * Mail already in use?
     */
    if (User::where("email", $params->email)->first())
      return $this->error("<strong>This e-mail can't be used!</strong> Select another one.");

    /**
     * @var ?Authentication
     */
    $Authentication =
      self::where("email", $params->email)
      ->whereNull("deleted_at")
      ->first();

    /**
     * Begin a transaction & try catch block.
     */
    $this->db_transaction();
    try {

      /**
       * Check, if an authentication with type a
       */
      !$Authentication
        ? $Authentication = self::create([
          "email" => $params->email,
          "type" => $params->type,
          "token" => $token,
          "remote_address" => Request::get_remote_address(),
        ])
        : $Authentication->update([
          "token" => $token,
          "remote_address" => Request::get_remote_address(),
        ]);

      /**
       * Prepare mail body.
       */
      $mail_token = Utils::random_alpha_token(64);
      $mail_template = "signed_up";
      $mail_subject = $Authentication->display_mail_subject();
      $mail_body = file_get_contents(ROOT . "/app/templates/mail/$mail_template.html");

      /**
       * Replace curly variables.
       */
      $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
      $mail_body = str_replace('{home-url}', HOME_URL, $mail_body);
      $mail_body = str_replace('{app-name}', APP_NAME, $mail_body);
      $mail_body = str_replace('{big-button-link}', HOME_URL . "/begin/" . trim($token) . "?mailing_token=$mail_token", $mail_body);
      $mail_body = str_replace('{discord-link}', ENV->DISCORD_INVITE, $mail_body);
      $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
      $mail_body = str_replace('{footer-copyright}', APP_NAME . " &copy; " . date("Y") . ". All rights reserved.", $mail_body);
      $mail_body = str_replace('{unsubscribe-link}', HOME_URL . "/my/privacy/mailing", $mail_body);

      /**
       * Abfahrt!
       */
      if (!(new Mail)->create(
        $params->email,
        $mail_subject,
        $mail_body
      ))
        return $this->error("<strong>An error occured while sending an authentication mail.</strong>");

      /**
       * Create new Mailing.
       */
      Mailing::create([
        "template" => $mail_template,
        "user_id" => 0,
        "email" => $params->email,
        "subject" => $mail_subject,
        "token" => $mail_token,
        "updated_at" => null,
      ]);

      /**
       * Commit this cutie.
       */
      $this->db_commit();

      return $this->success("<strong>A verification has been sent to &raquo;$params->email&laquo;!</strong> Be sure to also look up your spam folder.");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error();
    }
  }

  /**
   * @param string $code
   * @param string $email
   * @return bool
   */
  public function send_mail(string $code, string $email, string $username)
  {

    /**
     * Prepare mail body.
     */
    $main_url = _env("SERVER_ADDRESS");
    $mail_template = "email_authentication";
    $mail_subject = $this->display_mail_subject();
    $mail_token = Utils::random_alpha_token(64);
    $mail_body = file_get_contents(ROOT . "/app/templates/mail/$mail_template.html");

    /**
     * Replace curly variables.
     */
    $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
    $mail_body = str_replace('{username}', $username, $mail_body);
    $mail_body = str_replace('{code}', $code, $mail_body);
    $mail_body = str_replace('{user-settings-link}', "$main_url/my/security/password?mailing_token=$mail_token", $mail_body);
    $mail_body = str_replace('{app-name}', _env("APP_NAME"), $mail_body);
    $mail_body = str_replace('{discord-link}', _env("DISCORD_INVITE"), $mail_body);
    $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
    $mail_body = str_replace('{footer-copyright}', _env("APP_NAME") . " &copy; " . date("Y") . ". All rights reserved.", $mail_body);
    $mail_body = str_replace('{unsubscribe-link}', "$main_url/my/privacy/mailing", $mail_body);

    /**
     * Send it!
     */
    return (new Mail)->create(
      $email,
      $mail_subject,
      $mail_body
    );
  }


  // ? >>>>>>>>>>>>>>>>>>>>> DISPLAY >>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return string
   */
  public function display_type()
  {
    return match ($this->type) {
      static::$types[0] => "Start Journey",
      static::$types[1] => "Account Deletion",
      static::$types[2] => "Change E-Mail Address",
      static::$types[3] => "Restart Journey",
      static::$types[4] => "Leave Squad",
      default => "Authenticate"
    };
  }

  /**
   * @return string
   */
  public function display_mail_subject()
  {
    return match ($this->type) {
      static::$types[0] => "❤️‍🔥 Wanna join " . APP_NAME . "? Here is your code!",
      static::$types[1] => "🥹 Wanna say goodbye? Here is your code…",
      static::$types[2] => "🤗 Wanna change your mail? Here is your code…",
      static::$types[3] => "🥵 Spicy restart of your journey? Here is your code!",
      static::$types[4] => "🫥 Wanna leave your squad behind? Here is your code…",
      default => "♻️ Your code is: " . $this->code,
    };
  }
}
