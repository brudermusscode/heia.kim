<?php

namespace Heiakim\Job\Mailing;

use Heiakim\Job;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;
use Heiakim\Mail\Mail;
use Illuminate\Support\Carbon;

class BirthdayWishes extends Job
{
  /**
   * @return void
   */
  public function execute()
  {
    /**
     * @var object
     */
    $AppSettings = $this->App();

    /**
     * @var Carbon
     */
    $todayDate = Carbon::today();

    /**
     * @var array
     */
    $BirthdayUsers = User::whereHas("settings", function ($q) {
      $q->whereNotNull("birthday");
    })
      ->get()

      /**
       * Filter to only the ones that have birthday today.
       */
      ->filter(function ($user) use ($todayDate) {
        return Carbon::createFromFormat('Y-m-d', $user->settings->birthday)->format('m-d') === $todayDate->format('m-d');
      })
      ->toArray();

    $count = 0;
    $failed = 0;
    $subject = "🥳 It's your birthday!";
    $template = "birthday_wishes";
    $chunkSize = 10;

    foreach (array_chunk($BirthdayUsers, $chunkSize) as $chunk) {
      foreach ($chunk as $UserArray) {

        /**
         * @var User
         */
        $User = User::find($UserArray["id"]);

        /**
         * User has no email address set?
         */
        if (!$User->email)
          continue;

        /**
         * Mailing disabled?
         */
        if (!$User->privacy->mailing_birthday)
          continue;

        /**
         * @var ?Mailing
         */
        $Mailing = $User->mailings()
          ->where("template", $template)
          ->whereYear("created_at", Carbon::now()->year)
          ->latest()
          ->first();

        /**
         * This years mailing exists?
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
        $mail_body = str_replace('{premium-feature-name}', $AppSettings->premium_feature_name, $mail_body);
        $mail_body = str_replace('{discord-link}', _env("DISCORD_INVITE"), $mail_body);
        $mail_body = str_replace('{youtube-link}', "https://www.youtube.com/@heia.kimosu", $mail_body);
        $mail_body = str_replace('{profile-link}',  "$main_url/u/$User->id?mailing_token=$token", $mail_body);
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

        /**
         * Give the user premium!
         */
        $User->give_premium("+1 week");

        /**
         * Update premium settings to birthday font.
         */
        $User->premium()
          ->update([
            "headline" => 1964641,
            "premium_name_style" => "OMG-MORE-COLORS",
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
