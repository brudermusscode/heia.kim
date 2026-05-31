<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Utils\Utils;
use Heiakim\Model\User;
use Heiakim\Mail\Mail;
use Heiakim\Time\Time;
use Heiakim\Trait\HasDefaultUser;

class PasswordReset extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "token",
    "old_password_encrypted",
    "updated_at",
  ];

  /**
   * @var string
   */
  public static $request_interval = "+30 minutes";

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {

    /**
     * @var string
     */
    $token = Utils::random_alpha_token(32);

    /**
     * @var ?User
     */
    $CurrentUser = $params->CurrentUser;

    if ($CurrentUser) {

      /**
       * @var ?PasswordReset
       */
      $PasswordReset =
        $CurrentUser->password_resets()
        ->latest()
        ->first();

      /**
       * Return an error, if not enough time has passed since the
       * last password reset request.
       */
      if ($PasswordReset && !Time::has_passed($PasswordReset->created_at, self::$request_interval))
        return $this->error("<strong>Your last reset request is less than " . str_replace("+", "", self::$request_interval) . " ago.</strong>");
    }

    /**
     * If user is logged, append the current user's mail to the
     * params object.
     */
    if ($CurrentUser && $CurrentUser->email && filter_var($CurrentUser->email, FILTER_VALIDATE_EMAIL))
      $params->mail = $CurrentUser->email;

    /**
     * Mail is valid?
     */
    if (!filter_var($params->mail, FILTER_VALIDATE_EMAIL))
      return $this->error("<strong>Invalid mail address!</strong>");

    /**
     * Prepare the message.
     */
    $msg = $CurrentUser
      ? "<strong>A mail with a verification link has been sent!</strong> If you don't receive an e-mail in the next 30 minutes, request a new password reset."
      : "<strong>If there is a user account connected to this mail address, we have sent out an e-mail with instructions to change the password!</strong> If you don't receive an e-mail in the next 30 minutes, request a new password reset.";

    /**
     * @var User
     */
    $User = User::where("email", $params->mail)
      ->first();

    /**
     * A user doesn't exist? Send a success message anyway to hide
     * that there could potentially exist a user with the given
     * mail 😘.
     */
    if (!$CurrentUser && !$User)
      return $this->success($msg);

    /**
     * Create it!
     */
    self::create([
      "user_id" => $CurrentUser ? $CurrentUser->id : $User->id,
      "token" => $token,
      "old_password_encrypted" => $CurrentUser?->pw_bcrypt ?? $User?->pw_bcrypt,
      "updated_at" => null,
    ]);

    /**
     * Prepare mail body.
     */
    $main_url = _env("SERVER_ADDRESS");
    $mail_template = "password_reset";
    $mail_subject = "♻️ Password forgotten?";
    $mail_token = Utils::random_alpha_token(64);
    $mail_body = file_get_contents(_root() . "/app/templates/mail/$mail_template.html");

    /**
     * Replace curly variables.
     */
    $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
    $mail_body = str_replace('{username}', $CurrentUser ? $CurrentUser->name : "you", $mail_body);
    $mail_body = str_replace('{big-button-link}', "$main_url/password-reset/$token?mailing_token=$mail_token", $mail_body);
    $mail_body = str_replace('{home-link}', "$main_url", $mail_body);
    $mail_body = str_replace('{user-settings-link}', "$main_url/my/security/password", $mail_body);
    $mail_body = str_replace('{app-name}', _env("APP_NAME"), $mail_body);
    $mail_body = str_replace('{discord-link}', _env("DISCORD_INVITE"), $mail_body);
    $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
    $mail_body = str_replace('{footer-copyright}', _env("APP_NAME") . " &copy; " . date("Y") . ". All rights reserved.", $mail_body);
    $mail_body = str_replace('{unsubscribe-link}', "$main_url/my/privacy/mailing", $mail_body);

    /**
     * Try sending the mail and on success, create a new mailing
     * for the user or just with user id 0.
     */
    if ((new Mail)->create(
      $params->mail,
      $mail_subject,
      $mail_body
    ))
      Mailing::create([
        "template" => $mail_template,
        "user_id" => $CurrentUser ? $CurrentUser->id : 0,
        "email" => $params->mail,
        "subject" => $mail_subject,
        "token" => $mail_token,
        "updated_at" => null,
      ]);

    return $this->success($msg);
  }

  /**
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {

    /**
     * @var string
     */
    $password = htmlspecialchars_decode($params->password);
    $password_length = strlen($password);

    /**
     * Password has valid length?
     */
    if ($password_length < 6 || $password_length > 36)
      return $this->error("<strong>Your password should be between 6 - 36 characters in length.</strong>");

    /**
     * @var string
     */
    $params->pw_bcrypt = User::encrypt_password($password);

    /**
     * Prepare old + new password and encrypt the new one.
     */
    $old_encrypted_password = $this->user->pw_bcrypt;

    /**
     * Insert new user change.
     */
    $this->user
      ->password_changes()
      ->create([
        "type" => "password",
        "previous_value" => $old_encrypted_password,
        "updated_value" => $params->pw_bcrypt,
        "updated_at" => null,
      ]);

    /**
     * Update user's password.
     */
    $this->user->update([
      "pw_bcrypt" => $params->pw_bcrypt,
    ]);

    /**
     * Delete password reset.
     */
    $this->delete();

    return $this->success("<strong>Your password has been reset!</strong>");
  }
}
