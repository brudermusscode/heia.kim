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

  public static array $types = [
    "user:create",
    "user:delete",
    "user:update:email",
    "user:wipe",
    "squad:user:delete",
  ];

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

  /**
   * @param object $params
   * @return void
   *
   * NOTE: Will die on error.
   */
  public static function validate_params(object $params)
  {

    if (!$params->type || !in_array($params->type, Authentication::$types))
      die(error("What happened? 😂"));

    if ($params->type === "user:update:email") {
      if (empty($params->value))
        die(error("Bro, where mail?"));

      if (!filter_var($params->value, FILTER_VALIDATE_EMAIL))
        die(error("Invalid mail 😟"));
    }
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
