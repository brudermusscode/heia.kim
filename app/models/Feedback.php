<?php

namespace Heiakim\Model;

use Heiakim\Justin;
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
   * Valid types to set.
   */
  private static array $types = [
    "score",
    "beatmap",
    "update",
    "artist",
    "birthday_cheer",
  ];

  /**
   * Valid actions to pass.
   */
  private static array $actions = [
    "thumb_up",
    "thumb_down",
  ];

  /**
   * @param object $params
   * @return static
   *
   * NOTE: Will die on error.
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = CurrentUser;

    /**
     * @var ?Notification
     */
    $Notification = null;

    # Validate types.
    if (!in_array($params->type, self::$types))
      return die(error());

    # Validate actions.
    if (!in_array($params->action, self::$actions))
      return die(error());

    # ? Birthday Cheers
    if ($params->type === "birthday_cheer") {

      if (empty($params->reference_id))
        return die(error());

      /**
       * @var ?User
       */
      $User = User::findOrReturn($params->reference_id);

      if (!$User->has_birthday())
        return die(error());

      if ($CurrentUser->feedback()
        ->where([
          "reference_id" => $User->id,
          "type" => "birthday_cheer",
        ])
        ->exists()
      )
        return die(error(
          "<strong>You have cheered for their birthday already!</strong> Thank you so much!"
        ));

      $params->action = "thumb_up";

      # Prepare a notification.
      $Notification = (new Notification)->new([
        "user_id" => $User->id,
        "type" => "__feedback__/user+birthday",
        "reference_id" => $CurrentUser->id,
      ]);
    }

    # ? Scores
    if ($params->type === "score") {
      $Score = Score::findOrReturn($params->reference_id ?? 0);

      $Notification = (new Notification)->new([
        "user_id" => $Score->userid,
        "type" => "__feedback__/score",
        "reference_id" => $params->reference_id,
        "reference_2_id" => $params->CurrentUser_id,
      ]);
    }

    # ? Beatmaps
    if ($params->type === "beatmap") {
      $Beatmap = Beatmap::findOrReturn($params->reference_id);
    }

    # As some feedback will notify Users, let's check if the feedback has already been
    # given to prevent spam notification 🙂
    $already_submitted = false;
    $Feedback = $CurrentUser
      ->feedback()
      ->where("reference_id", $params->reference_id)
      ->where("type", $params->type)
      ->where("action", $params->action)
      ->first();

    if ($Feedback) {
      $already_submitted = true;
      $Feedback->delete();
    } else
      $Feedback = $CurrentUser->feedback()
        ->create([
          "reference_id" => $params->reference_id ?? null,
          "type" => $params->type ?? null,
          "action" => $params->action ?? null,
        ]);

    $Notification?->save();

    return $Feedback;
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
