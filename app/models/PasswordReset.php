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
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    # Mail is invalid?
    if (!filter_var($params->email, FILTER_VALIDATE_EMAIL))
      return die(error("<strong>Invalid e-mail address!</strong>"));

    # Prepare return message as unlogged users should not know whether an email addr-
    # ess is registered or not.
    $msg = $CurrentUser
      ? "<strong>A verification mail has been sent!</strong> Unless it doesn't arrive in 30 mins, try again then."
      : "<strong>You might receive a verification mail soon!</strong> If not in the next 30 mins, try again 🙂";

    /**
     * @var User
     */
    $User = User::where("email", $params->email)->first();

    # In case there is no User with this e-mail address and no User logged in right
    # now, we can return early and tell the User that we might have sent a code.
    if (!$CurrentUser && !$User)
      return $this->success($msg);

    $token = Utils::random_alpha_token(32);

    # Create a PasswordReset!
    self::create([
      "user_id" => $CurrentUser ? $CurrentUser->id : $User->id,
      "token" => $token,
      "old_password_encrypted" => $CurrentUser?->pw_bcrypt ?? $User?->pw_bcrypt,
      "updated_at" => null,
    ]);

    # Prepare mail body.
    $main_url = _env("SERVER_ADDRESS");
    $mail_template = "password_reset";
    $mail_subject = "♻️ Password forgotten?";
    $mail_token = Utils::random_alpha_token(64);
    $mail_body = file_get_contents(ROOT . "/app/templates/mail/$mail_template.html");

    # Replace all placeholders with real values.
    # TODO: This could be outsourced for more DRY.
    $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
    $mail_body = str_replace('{username}', $CurrentUser?->name ?? "you", $mail_body);
    $mail_body = str_replace('{big-button-link}', "$main_url/password-reset/$token?mailing_token=$mail_token", $mail_body);
    $mail_body = str_replace('{home-link}', "$main_url", $mail_body);
    $mail_body = str_replace('{user-settings-link}', "$main_url/my/security/password", $mail_body);
    $mail_body = str_replace('{app-name}', _env("APP_NAME"), $mail_body);
    $mail_body = str_replace('{discord-link}', _env("DISCORD_INVITE"), $mail_body);
    $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
    $mail_body = str_replace('{footer-copyright}', _env("APP_NAME") . " &copy; " . date("Y") . ". All rights reserved.", $mail_body);
    $mail_body = str_replace('{unsubscribe-link}', "$main_url/my/privacy/mailing", $mail_body);

    # Send the Mail!
    if ((new Mail)->create(
      $params->email,
      $mail_subject,
      $mail_body
    ))

      # Create a Mailing, so we know when the User clicks on the link in the Mail.
      Mailing::create([
        "template" => $mail_template,
        "user_id" => $CurrentUser ? $CurrentUser->id : 0,
        "email" => $params->email,
        "subject" => $mail_subject,
        "token" => $mail_token,
        "updated_at" => null,
      ]);

    return $this->success($msg);
  }

  /**
   * @param object $params
   * @return object
   *
   * NOTE: Will die on error.
   */
  public function edit(object $params)
  {

    $password = $params->password;
    $old_encrypted_password = $this->user->pw_bcrypt;

    # Validate and update the User with the new password.
    $this->user->set_password_invalid($password);
    $this->user->save();

    $new_encrypted_password = $this->user->pw_bcrypt;

    # Create a new Change for this User.
    $this->user->password_changes()
      ->create([
        "type" => "password",
        "previous_value" => $old_encrypted_password,
        "updated_value" => $new_encrypted_password,
        "updated_at" => null,
      ]);

    # Delete it!
    $this->delete();

    return success("<strong>Password reset!</strong>");
  }
}
