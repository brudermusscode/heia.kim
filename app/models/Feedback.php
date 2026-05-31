<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Time\Time;
use Heiakim\Application\Cookie;
use Heiakim\Trait\HasDefaultUser;

class Feedback extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "reference_id",
    "type",
    "action",
    "message",
    "updated_at",
  ];

  /**
   * Feedback last cookie defines when the last feedback was set,
   * so we can show the card again on new updates.
   *
   * @var string
   */
  protected static $cookie = "FEEDBACK_LAST";

  /**
   * Valid types.
   *
   * @var array
   */
  private static $types = [
    "score",
    "beatmap",
    "update",
    "artist",
    "birthday_cheer",
  ];

  /**
   * Valid actions
   *
   * @var array
   */
  private static $actions = [
    "thumb_up",
    "thumb_down",
  ];

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

    /**
     * @var ?Notification
     */
    $Notification = null;

    /**
     * Type valid?
     */
    if (!in_array($params->type, self::$types))
      return $this->error();

    /**
     * Action valid?
     */
    if (!in_array($params->action, self::$actions))
      return $this->error();

    /**
     * ? BIRTHDAY CHEERS
     */
    if ($params->type === "birthday_cheer") {

      /**
       * Reference user as id set?
       */
      if (empty($params->reference_id))
        return $this->error();

      /**
       * @var ?User
       */
      $User = User::find($params->reference_id);

      /**
       * User exists?
       */
      if (!$User)
        return $this->error();

      /**
       * Is Users birthday?
       */
      if (!$User->has_birthday())
        return $this->error();

      /**
       * Feedback already exists?
       */
      if ($CurrentUser->feedback()
        ->where([
          "reference_id" => $User->id,
          "type" => "birthday_cheer",
        ])
        ->exists()
      )
        return $this->error("<strong>You have cheered for their birthday already!</strong> Thank you so much!");

      /**
       * Set the action to always be thumb_up, the user has birthday!
       */
      $params->action = "thumb_up";

      /**
       * Prepare notification.
       */
      $Notification = $User->notifications()
        ->make([
          "type" => "__feedback__/user+birthday",
          "reference_id" => $CurrentUser->id,
          "updated_at" => null,
        ]);
    }

    /**
     * ? SCORES
     */
    if ($params->type === "score") {
      $Score = Score::find($params->reference_id ?? 0);

      /**
       * Score exists?
       */
      if (!$Score)
        return $this->error();

      $notification_user_id = $Score->userid;
      $notification_type = "__feedback__/score";
    }

    /**
     * ? BEATMAPS
     */
    if ($params->type === "beatmap") {
      $Beatmap = Beatmap::find($params->reference_id);

      if (!$Beatmap)
        return $this->error();
    }

    /**
     * ? UPDATES
     */
    if ($params->type == "update") {
      Cookie::set(self::$cookie, date("Y.m.d H:i:s"), "+10 months");

      $Feedback = $params->CurrentUser->feedback()
        ->where("type", $params->type)
        ->orderByDesc("id")
        ->first();

      /**
       * Last feedback for updates is less than a day ago?
       */
      if ($Feedback && !Time::has_passed_since($Feedback->created_at, 1, true))
        return $this->success("<strong>Thank you so much!</strong> We grow with your feedback.");
    }

    /**
     * Already submitted?
     */
    $already_submitted = false;
    $Feedback = $params->CurrentUser->feedback()
      ->where("reference_id", $params->reference_id)
      ->where("type", $params->type)
      ->where("action", $params->action)
      ->first();

    /**
     * IF yes, delete Feedback.
     */
    if ($Feedback && $params->type !== "update") {
      $already_submitted = true;
      $Feedback->delete();

      /**
       * Create new Feedback
       */
    } else
      $Feedback = $CurrentUser->feedback()
        ->create([
          "reference_id" => $params->reference_id ?? null,
          "type" => $params->type ?? null,
          "action" => $params->action ?? null,
        ])->fresh();

    /**
     * Set the feedback_last cookie to the current timestamp.
     */
    if ($params->type === "update")
      Cookie::set(self::$cookie, date("Y.m.d H:i:s"), "+10 months");

    /**
     * Send a notifciation on specific types.
     */
    if (in_array($params->type, ["score"]) && !$already_submitted) {
      (new Notification)->new((object) [
        "user_id" => $notification_user_id,
        "type" => $notification_type,
        "reference_id" => $params->reference_id,
        "reference_2_id" => $params->CurrentUser_id,
      ]);
    }

    /**
     * Save notification!
     */
    $Notification?->save();

    return $this->success("<strong>Your feedback has been given!</strong>");
  }

  /**
   * @return object
   */
  public function remove()
  {
    $this->remove();

    return $this->success("<strong>Removed!</strong>");
  }

  /**
   * @return ?Artist|Beatmap|Score
   */
  public function reference()
  {
    $Reference = match ($this->type) {
      "artist" => Artist::class,
      "beatmap" => Beatmap::class,
      "score" => Score::class,
      default => null
    };

    return $this->belongsTo($Reference, "reference_id", "id");
  }
}
