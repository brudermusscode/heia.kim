<?php

namespace Heiakim\Model;

use Heiakim\Application\Logger;
use Heiakim\Justin;
use Heiakim\Model\Beatmap;
use Heiakim\Model\User;
use Heiakim\Model\Thread\ThreadPostAttachment;
use Heiakim\Enum\Mod;
use Heiakim\Trait\HasDefaultUser;

class Score extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  public static $statuses = [
    0, // failed
    1, // played but not submitted
    2, // submitted
  ];

  /**
   * @return bool
   */
  public function remove()
  {
    // TODO: Delete replays.

    try {

      $this->comments()
        ->delete();

      $this->squad_post_comments()
        ->delete();

      $this->reactions()
        ->delete();

      $this->feedback()
        ->delete();

      $this->thread_post_attachments()
        ->delete();

      $this->delete();

      return true;
    } catch (\Exception $e) {

      /**
       * Log & return.
       */
      Logger::to_file($e);
      return false;
    }
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "userid", "id");
  }

  /**
   * @return Beatmap
   */
  public function beatmap()
  {
    return $this->belongsTo(Beatmap::class, "map_md5", "md5");
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,, FEEDBACK & REACTIONS ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return ?Feedback
   */
  public function feedback()
  {
    return $this->hasMany(Feedback::class, "reference_id", "id")
      ->where("type", "score");
  }

  /**
   * @param int $id
   * @return ?Feedback
   */
  public function has_received_feedback_from(int $id)
  {
    return $this->feedback()
      ->where("user_id", $id)
      ->first();
  }

  /**
   * @return ?Reaction
   */
  public function reactions()
  {
    return $this->hasMany(Reaction::class, "reference_id", "id");
  }

  /**
   * @return ?Comment
   */
  public function comments()
  {
    return $this->hasMany(Comment::class, "reference_id", "id")
      ->where("type", "score");
  }

  /**
   * @return ?ThreadPostAttachment
   */
  public function thread_post_attachments()
  {
    return $this->hasMany(ThreadPostAttachment::class, "reference_id")
      ->where("type", "score");
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,, SQUADS ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return ?Comment
   */
  public function squad_post_comments()
  {
    return $this->hasMany(Comment::class, "reference_id", "id")
      ->where("type", "squad+score");
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,, DISPLAY ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return array All mods as two char value in array
   */
  public function mods()
  {
    return self::turn_mods_into_array($this->mods);
  }

  /**
   * @param int $mods
   * @return array
   */
  public static function turn_mods_into_array(int $mods)
  {
    $modsApplied = [];

    foreach (Mod::cases() as $mod) {
      if (($mods & $mod->value) !== 0) {
        $modsApplied[] = (object) [
          "Mod" => $mod,
          "full" => $mod->get_display()["full"],
          "short" => $mod->get_display()["short"],
        ];
      }
    }

    return $modsApplied;
  }

  /**
   * @return string
   */
  public function grade()
  {
    return $this->turn_grade_to_text();
  }

  /**
   * Turns the grade from a number into a text value for
   * user friendly displaying on the website
   *
   * @return string The mod string
   */
  public function turn_grade_to_text()
  {
    $grade = $this->data()->grade ?? "";

    return $grade === 'X' ||  $grade === 'XH'
      ? 'SS'
      : ($grade === 'SH'
        ? 'S'
        :  $grade
      );
  }

  /**
   * Turn abbreviation of a mod into their full name.
   *
   * @param string $mod
   * @return string
   */
  public static function mod_text(string $mod)
  {
    return match ($mod) {
      "NM" => "No mod",
      "HD" => "Hidden",
      "DT" => "DoubleTime",
      "NC" => "Nightcore",
      "HR" => "HardRock",
      "FL" => "Flashlight",
      "RX" => "Relax",
      "AP" => "Autopilot",
      "V2" => "Score V2",
      "NF" => "No Fail",
      "MR" => "Mirroring",
      "PF" => "Perfect",
      "TD" => "Touch Device",
      default => "Unknown mod",
    };
  }

  /**
   * Converts X, XH and other grade texts to their real being
   *
   * @param string $grade
   * @return string
   */
  public function grade_text(string $grade)
  {
    return $grade === 'X' || $grade === 'XH' ? 'SS' : ($grade === 'SH' ? 'S' : $grade);
  }

  /**
   * @return bool
   */
  public function is_submitted()
  {
    return $this->status >= 2;
  }
}
