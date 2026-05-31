<?php

namespace Heiakim\Model\User;

use Heiakim\Justin;
use Heiakim\Application\Cookie;
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

  /**
   * @var array
   */
  public $mailings = [
    "mailing_account",
    "mailing_newsletter",
    "mailing_expiring_premium",
    "mailing_reminder",
    "mailing_birthday",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {

    /**
     * * Mailings
     */
    foreach ($this->mailings as $mailing) {
      if (isset($params->$mailing)) {
        $mailing_value = $this->ensure_numeric_bool($params->$mailing);

        /**
         * Set the real bool value for the mailing.
         */
        $this->$mailing = $mailing_value !== null ? $mailing_value : $this->$mailing;
      }
    }

    /**
     * * Public Profile
     */
    if (isset($params->is_public))
      $this->is_public = $params->is_public > 0 ? 1 : 0;

    /**
     * * Image History
     */
    if (isset($params->image_history))
      $this->image_history = $params->image_history > 0 ? 1 : 0;

    /**
     * * Policies
     */
    if (isset($params->accepts_policies)) {
      $this->accepts_policies = $params->accepts_policies > 0 ? 1 : 0;

      /**
       * Remove policies step cookie and set the consent set cookie.
       */
      Cookie::delete("POLICIES_CONSENT_STEP");
      Cookie::set("POLICIES_CONSENT", true, "+10 years");
    }

    /**
     * Update it!
     */
    $this->save();

    return $this->success("<strong>All cool!</strong>");
  }

  /**
   * @return int
   */
  public function ensure_numeric_bool(mixed $input)
  {

    /**
     * @var ?int
     */
    $filtered_input = filter_var($input, FILTER_VALIDATE_INT);

    if ($filtered_input !== null && ($filtered_input == 0 || $filtered_input == 1))
      return intval($input);
    else
      return null;
  }

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
