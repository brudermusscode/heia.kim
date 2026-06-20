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
