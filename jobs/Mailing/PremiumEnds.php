<?php

namespace Bruder\Heiakim\Job\Mailing;

use Bruder\Job;
use Bruder\Heiakim\Model\User;
use Bruder\Utils\Utils;
use Bruder\Mail\Mail;
use Bruder\Time\Time;

class PremiumEnds extends Job
{
  /**
   * @var string
   */
  private $interval = "+7 days";

  /**
   * @return void
   */
  public function execute()
  {
    /**
     * @var object
     */
    $App = $this->App();

    /**
     * @var int
     */
    $interval = strtotime($this->interval, time());

    /**
     * @var array
     */
    $PremiumUsers = User::premium_members()
      ->where([
        ["donor_end", ">", time()],
        ["donor_end", "<=", $interval],
      ])
      ->get()
      ->toArray();

    /**
     * No premium members?
     */
    if (!count($PremiumUsers))
      return;

    $count = 0;
    $failed = 0;
    $subject = "⏰ Is it already over?";
    $template = "premium_ends";
    $chunkSize = 10;

    foreach (array_chunk($PremiumUsers, $chunkSize) as $chunk) {
      foreach ($chunk as $UserArray) {

        /**
         * @var User
         */
        $User = User::find($UserArray["id"]);

        /**
         * User has no email address set or has birthday today?
         */
        if (!$User->email || $User->has_birthday())
          continue;

        /**
         * Mailing disabled?
         */
        if (!$User->privacy->mailing_expiring_premium)
          continue;

        /**
         * @var ?Mailing
         */
        $Mailing = $User->mailings()
          ->where("template", $template)
          ->whereRaw("UNIX_TIMESTAMP(created_at) >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))")
          ->latest()
          ->first();

        /**
         * Delete all older mailings.
         */
        // if ($Mailing)
        //   $User->mailings()
        //     ->where("template", $template)
        //     ->whereNot("id", $Mailing->id)
        //     ->delete();

        /**
         * Last mailing is longer ago than the set interval?
         */
        if ($Mailing)
          continue;

        /**
         * Dependency variables.
         */
        $main_url = _env("SERVER_ADDRESS");
        $token = Utils::random_alpha_token(64);

        /**
         * Prepare mail body.
         */
        $file_path = _root() . "/app/templates/mail/$template.html";
        $mail_body = file_get_contents($file_path);

        /**
         * Replace curly variables in mail body.
         */
        $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
        $mail_body = str_replace('{username}', $User->name, $mail_body);
        $mail_body = str_replace('{premium-feature-name}', $App->premium_feature_name, $mail_body);
        $mail_body = str_replace('{premium-time-left}', Time::left($User->donor_end), $mail_body);
        $mail_body = str_replace('{big-button-link}',  "$main_url/unlock/premium?mailing_token=$token", $mail_body);
        $mail_body = str_replace('{discord-link}', _env("DISCORD_INVITE"), $mail_body);
        $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
        $mail_body = str_replace('{footer-copyright}', _env("APP_NAME") . " &copy; " . date("Y") . ". All rights reserved.", $mail_body);
        $mail_body = str_replace('{unsubscribe-link}', "$main_url/my/privacy/mailing?mailing_token=$token&unsubscribe=$template", $mail_body);

        /**
         * Send the Mail.
         */
        if (!(new Mail)->create(
          $User->email,
          $subject,
          $mail_body,
          "noreply@heia.kim",
          "heia.kim",
        )) {
          $failed++;
          continue;
        }

        /**
         * Create Mailing.
         */
        $User->mailings()
          ->create([
            "template" => $template,
            "email" => $User->email,
            "subject" => $subject,
            "token" => $token,
            "updated_at" => null,
          ]);

        $count++;
      }

      /**
       * Sleep 100ms which is 1000 microseconds.
       */
      usleep(1000);
    }

    /**
     * ! Return
     */
    if ($count || $failed)
      echo "\n" . date("d.m.Y<;>H:i:s") . "<;>Mailing `$template` sent to $count users, $failed failed<&>\n";
  }
}
