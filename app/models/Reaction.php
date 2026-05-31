<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\Reaction\ReactionPackageEmoji;
use Heiakim\Trait\HasDefaultUser;

class Reaction extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "reference_id",
    "reaction",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $types = [
    "score",
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
     * Emoji exists?
     */
    if (!ReactionPackageEmoji::where("reaction", $params->reaction)->first())
      return $this->error();

    /**
     * Type valid?
     */
    if (!in_array($params->type, self::$types))
      return $this->error();

    /**
     * Reaction already exists?
     */
    $Reaction = $CurrentUser->reactions()
      ->where("type", $params->type)
      ->where("reaction", $params->reaction)
      ->where("reference_id", $params->reference_id)
      ->first();

    /**
     * @var bool
     */
    $got_deleted = false;

    if ($Reaction) {
      $got_deleted = true;
      $Reaction->delete();
    } else {
      $Reaction = $CurrentUser->reactions()
        ->create([
          "type" => $params->type,
          "reaction" => $params->reaction,
          "reference_id" => $params->reference_id,
          "updated_at" => null,
        ]);
    }

    /**
     * Return no data but success, when the reaction has been deleted.
     */
    if ($got_deleted)
      return $this->success();

    /**
     * Get fresh Reaction data.
     */
    $Reaction = $Reaction->fresh();

    /**
     * Begin output buffer.
     */
    ob_start();
    $CurrentUser;

    include _root() . "/app/templates/reaction/_reaction.php";

    return $this->success(data: ob_get_clean());
  }

  /**
   * @return ReactionPackageEmoji
   */
  public function emoji()
  {
    return $this->belongsTo(ReactionPackageEmoji::class, "reaction", "reaction");
  }

  /**
   * @return Score|null
   */
  public function reference()
  {
    $Reference = match ($this->type) {
      "score" => Score::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_id", "id");
  }
}
