<?php

namespace Heiakim\Model\User;

use Heiakim\Justin;
use Heiakim\Model\User;

class UserSettingsPrivacy extends Justin
{

  /**
   * @var string
   */
  protected $table = "user_settings_privacy";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "accepts_policies",
    "is_public",
    "can_interact",
    "image_history",
    "mailing_newsletter",
    "mailing_expiring_premium",
    "mailing_reminder",
    "mailing_birthday",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected $attributes = [
    "id" => 0,
    "user_id" => 0,
    "accepts_policies" => 0,
    "is_public" => 0,
    "can_interact" => 0,
    "image_history" => 0,
    "mailing_newsletter" => 0,
    "mailing_expiring_premium" => 0,
    "mailing_reminder" => 0,
    "mailing_birthday" => 0,
    "updated_at" => null,
  ];

  public static array $mailings = [
    "mailing_account",
    "mailing_newsletter",
    "mailing_expiring_premium",
    "mailing_reminder",
    "mailing_birthday",
  ];

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @param string $template
   * @return ?string
   */
  public function mailing_template_belongs_to_mailing(string $template)
  {
    return match ($template) {
      /** Add more */
      "premium_ends" => $this->mailings[2],
      "long_time_no_see" => $this->mailings[3],
      "birthday_wishes" => $this->mailings[4],
      default => null,
    };
  }
}
