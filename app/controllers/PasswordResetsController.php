<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Mail\Mail;
use Heiakim\Model\Mailing;
use Heiakim\Model\PasswordReset;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;

class PasswordResetsController extends Controller
{

  /**
   * @var array
   */
  protected $request_params = [
    "mail"
  ];

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["email"],
    );

    # CurrentUser exists, we will always use their current email address. Else we use the given email addres from parameters.
    $this->params->email = CurrentUser->exists && CurrentUser->email
      ? CurrentUser->email
      : $this->params->email;

    $msg = CurrentUser->exists
      ? "<strong>A verification mail has been sent!</strong> Unless it doesn't arrive in 30 mins, try again then."
      : "<strong>You might receive a verification mail soon!</strong> If not in the next 30 mins, try again 🙂";

    # Mail is invalid?
    if (!filter_var($this->params->email, FILTER_VALIDATE_EMAIL))
      return die(error("<strong>Invalid e-mail address!</strong>"));

    /**
     * @var User
     */
    $User = CurrentUser->exists
      ? CurrentUser
      : User::where("email", $this->params->email)->first();

    # In case there is no User with this e-mail address and no User logged in right
    # now, we can return early and tell the User that we might have sent a code.
    if (!CurrentUser->exists && !$User)
      return $this->success($msg);

    $token = Utils::random_alpha_token(32);

    PasswordReset::create([
      "user_id" => $User->id,
      "token" => $token,
      "old_password_encrypted" => $User->pw_bcrypt,
      "updated_at" => null,
    ]);

    # Prepare mail body.
    $main_url = _env("SERVER_ADDRESS");
    $mail_template = "password_reset";
    $mail_subject = "♻️ Password forgotten?";
    $mail_token = Utils::random_alpha_token(64);
    $mail_body = file_get_contents(TEMPLATE . "/mail/$mail_template.html");

    # Replace all placeholders with real values.
    # TODO: This could be outsourced for more DRY.
    $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
    $mail_body = str_replace('{username}', $User->name ?? "you", $mail_body);
    $mail_body = str_replace('{big-button-link}', "$main_url/password-reset/$token?mailing_token=$mail_token", $mail_body);
    $mail_body = str_replace('{home-link}', "$main_url", $mail_body);
    $mail_body = str_replace('{user-settings-link}', "$main_url/my/security/password", $mail_body);
    $mail_body = str_replace('{app-name}', _env("APP_NAME"), $mail_body);
    $mail_body = str_replace('{discord-link}', _env("DISCORD_INVITE"), $mail_body);
    $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
    $mail_body = str_replace('{footer-copyright}', _env("APP_NAME") . " &copy; " . date("Y") . ". All rights reserved.", $mail_body);
    $mail_body = str_replace('{unsubscribe-link}', "$main_url/my/privacy/mailing", $mail_body);

    if ((new Mail)->create(
      $this->params->email,
      $mail_subject,
      $mail_body
    ))
      Mailing::create([
        "template" => $mail_template,
        "user_id" => $User->id,
        "email" => $this->params->email,
        "subject" => $mail_subject,
        "token" => $mail_token,
        "updated_at" => null,
      ]);

    return success($msg);
  }

  /**
   * @return object
   */
  public function update()
  {

    $this->validate_params(
      strict: ["token", "password"],
      optional: ["mail"],
    );

    # Starting password resets from an existing and logged in account, we can append
    # the current email address of the User.
    if (CurrentUser->exists)
      $this->params->email = CurrentUser->email ?? $this->params->email;

    /**
     * @var ?PasswordReset
     */
    $PasswordReset = PasswordReset::with("user")
      ->where("token", $this->params->token)
      ->first();

    # Token is invalid thus no PasswordReset was found?
    if (!$PasswordReset)
      return error();

    return $PasswordReset->edit($this->params);
  }
}
