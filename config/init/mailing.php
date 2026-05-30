<?php

use Bruder\Heiakim\Model\Mailing;
use Bruder\Heiakim\Model\User\UserSettingsPrivacy;

/**
 * Update mailing if token is set.
 *
 */
if (!empty($_GET["mailing_token"])) {
  /**
   * @var ?Mailing
   */
  $Mailing = Mailing::where("token", $_GET["mailing_token"])
    ->first();

  /**
   * Only touch if not already touched. This can be used to
   * determine the engagement.
   */
  if ($Mailing) {
    if (!$Mailing->updated_at)
      $Mailing->touch();

    /**
     * @var ?string
     */
    $__unsubscribe_template = filter_input(INPUT_GET, "unsubscribe", FILTER_SANITIZE_SPECIAL_CHARS);

    /**
     * Unsubscribe link has been clicked?
     */
    if ($__unsubscribe_template && $Mailing->user) {
      /**
       * @var UserSettingsPrivacy
       */
      $Privacy = UserSettingsPrivacy::where("user_id", $Mailing->user_id)
        ->first();

      /**
       * @var ?string
       */
      $mailing_column = $Privacy->mailing_template_belongs_to_mailing($__unsubscribe_template);

      /**
       * If the mailing template belongs to an actual mailing of
       * the user's privacy settings, disable mailings of this
       * template for the given user.
       */
      if ($Privacy && $mailing_column) {
        $Privacy->update([
          $mailing_column => 0,
        ]);

        /**
         * Define a constant to use on other pages to show a
         * success message for the user. Please no cri bois.
         */
        define("VALID_MAILING_UNSUBSCRIBE", true);
      }
    }
  }
}
