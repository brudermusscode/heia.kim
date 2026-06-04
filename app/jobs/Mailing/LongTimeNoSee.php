<?php

// L::2024-12-15 22:29:01#

namespace Heiakim\Job\Mailing;

use Heiakim\Job;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;
use Heiakim\Mail\Mail;
use Heiakim\Time\Time;

class LongTimeNoSee extends Job
{
  /**
   * @var string
   */
  private $interval = "-1 month";

  /**
   * @return void
   */
  public function execute(?string $interval = null)
  {
    if (!Time::has_passed($this->last_executed(__FILE__), $this->interval))
      return;

    $this->update_last_executed(__FILE__);

    /**
     * @var int
     */
    $interval =  strtotime($this->interval, time());

    /**
     * @var array
     */
    $InactiveUsers = User::where("latest_activity", "<=", $interval)
      ->get()
      ->toArray();

    $count = 0;
    $failed = 0;
    $subject = "👀 Long time no see";
    $template = "long_time_no_see";
    $chunkSize = 10;

    foreach (array_chunk($InactiveUsers, $chunkSize) as $chunk) {
      foreach ($chunk as $UserArray) {

        /**
         * @var User
         */
        $User = User::with("privacy")
          ->find($UserArray["id"]);

        /**
         * User has no email address set?
         */
        if (!$User->email)
          continue;

        /**
         * Mailing disabled?
         */
        if (!$User->privacy || !$User->privacy->mailing_reminder)
          continue;

        /**
         * @var ?Mailing
         */
        $Mailing = $User->mailings()
          ->where("template", $template)
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
         * Last reminder is more than a month ago?
         */
        if ($Mailing && !(strtotime($Mailing->created_at) <= $interval))
          continue;

        /**
         * Does the domain used for the e-mail exist? This will
         * prevent this service from sending out mails to
         * inexistent providers. Should solve the death lock loop
         * of sending mails again and again.
         */
        if (!$this->email_domain_exists($User->email))
          continue;

        /**
         * Dependency variables.
         */
        $main_url = _env("SERVER_ADDRESS");
        $token = Utils::random_alpha_token(64);

        /**
         * Prepare mail body.
         */
        $file_path = ROOT . "/app/templates/mail/$template.html";
        $mail_body = file_get_contents($file_path);

        /**
         * Replace curly variables in mail body.
         */
        $mail_body = str_replace('{current-date}', date("d. F Y"), $mail_body);
        $mail_body = str_replace('{app-name}', _env("APP_NAME"), $mail_body);
        $mail_body = str_replace('{username}', $User->name, $mail_body);
        $mail_body = str_replace('{big-button-link}',  "$main_url?mailing_token=$token", $mail_body);
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

  /**
   * @return ?bool
   */
  public function email_domain_exists(string $email)
  {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      return null;

    $mail_arr = explode("@", $email);
    $domain = $mail_arr[1];

    /**
     * Get DNS records.
     */
    $records = dns_get_record($domain, DNS_A | DNS_AAAA | DNS_CNAME);

    return !empty($records);
  }
}
