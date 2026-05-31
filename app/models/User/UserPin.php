<?php

namespace Heiakim\Model\User;

use Heiakim\Justin;
use Heiakim\Model\User;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;

class UserPin extends Justin
{

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "type",
    "reference_id",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected static $types = [
    "score",
    "beatmap",
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
     * Type is valid?
     */
    if (!in_array($params->type, self::$types))
      return $this->error();

    /**
     * Pin exists already?
     */
    $Pin =
      $CurrentUser
      ->pins()
      ->where("type", $params->type)
      ->where("reference_id", $params->reference_id)
      ->first();

    /**
     * Pin does exist alreadys?
     */
    if ($Pin)
      return $this->error("<strong>This is pinned already!</strong>");

    /**
     * Create it!
     */
    $Pin =
      $CurrentUser
      ->pins()
      ->create([
        "type" => $params->type,
        "reference_id" => $params->reference_id,
        "updated_at" => null,
      ]);

    /**
     * Begin the ouput buffer.
     */
    ob_start();

    if ($params->type == "score") {
      $Score = $Pin->reference;
      $CurrentUser;

      include_once _root() . "/app/templates/score/_score.php";
    }

    return $this->success(
      "<strong>Pinned!</strong>",
      data: ob_get_clean()
    );
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return Beatmap|Score
   */
  public function reference()
  {
    return match ($this->type) {
      "score" => $this->belongsTo(Score::class),
      "beatmap" => $this->belongsTo(Beatmap::class),
      default => null,
    };
  }
}
