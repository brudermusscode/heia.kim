<?php

namespace Heiakim\Model\Squad;

use Heiakim\Justin;
use Heiakim\Model\Image;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Heiakim\Trait\HasDefaultUser;

class SquadFeedItem extends Justin
{
  use HasDefaultUser;

  /**
   * @var string
   */
  protected $table = "clan_feed_items";

  /**
   * @var array
   */
  public $fillable = [
    "user_id",
    "type",
    "reference_id",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $types = [
    // Logs created through setting updates.
    "__squad__/created",
    "__squad__/edit/name",
    "__squad__/edit/tag",
    "__squad__/edit/image+logo",
    "__squad__/edit/image+headline",
    "__squad__/edit/publicity",
    "__member__/restricted",
    "__member__/setfree",
    "__member__/promoted",
    "__member__/demoted",
    "__member__/kicked",
    "__member__/left",
    "__member__/joined",
    "__member__/chief+new",

    // Community made content.
    "__post__",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params) {}

  /**
   * @return ?User
   */
  public function affected_user()
  {
    return $this->belongsTo(User::class, "reference_id", "id");
  }

  /**
   * @return ?Image
   */
  public function image()
  {
    return $this->belongsTo(Image::class, "reference_id", "id");
  }

  /**
   * @return Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "clan_id", "id");
  }

  /**
   * @return ?SquadPost
   */
  public function post()
  {
    return $this->belongsTo(SquadPost::class, "reference_id", "id");
  }

  /**
   * @return bool
   */
  public function is_system_post()
  {
    return !str_starts_with("__post__", $this->type);
  }

  /**
   * @return bool
   */
  public function is_simple_post()
  {
    return in_array($this->type, ["__member__/left"]);
  }

  /**
   * @var string
   */
  public function display_type()
  {
    return (object) match ($this->type) {
      "__squad__/created" => [
        "append_text" => "created squad",
        "icon" => "add_circle",
      ],
      "__squad__/edit/name" => [
        "append_text" => "updated name",
        "icon" => "edit",
      ],
      "__squad__/edit/tag" => [
        "append_text" => "updated tag",
        "icon" => "edit",
      ],
      "__squad__/edit/image+logo" => [
        "append_text" => "updated logo",
        "icon" => "blur_circular",
      ],
      "__squad__/edit/image+headline" => [
        "append_text" => "updated headline",
        "icon" => "blur_circular",
      ],
      "__squad__/edit/publicity" => [
        "append_text" => "publicity updated",
        "icon" => $this->reference_id === Squad::$joinable["public"]
          ? "globe"
          : (
            $this->reference_id === Squad::$joinable["private"]
            ? "public_off"
            : "vpn_lock"
          ),
      ],
      "__member__/restricted" => [
        "append_text" => "restricted",
        "icon" => "front_hand",
      ],
      "__member__/setfree" => [
        "append_text" => "set free",
        "icon" => "shield_with_heart",
      ],
      "__member__/promoted" => [
        "append_text" => "promoted",
        "icon" => "stat_2"
      ],
      "__member__/demoted" => [
        "append_text" => "demoted",
        "icon" => "keyboard_double_arrow_down"
      ],
      "__member__/kicked" => [
        "append_text" => "kicked",
        "icon" => "sports_martial_arts"
      ],
      "__member__/left" => [
        "append_text" => "left",
        "icon" => "north_east"
      ],
      "__member__/joined" => [
        "append_text" => "joined",
        "icon" => "south_west"
      ],
      "__member__/chief+new" => [
        "append_text" => "new chief",
        "icon" => "arming_countdown"
      ],

      default => [
        "append_text" => "made something",
        "icon" => "help"
      ],
    };
  }

  /**
   * @var string
   */
  public function display_full_action()
  {
    /**
     * @var User
     */
    $TriggeredUser = $this->user;

    /**
     * @var User
     */
    $AffectedUser = $this->affected_user ?? User::guest();


    /**
     * @var bool
     */
    $is_same_user = $TriggeredUser->is($AffectedUser);

    return match ($this->type) {
      "__squad__/created" => "{triggered} created the squad",
      "__squad__/edit/image+logo" => "{triggered} updated the logo",
      "__squad__/edit/image+headline" => "{triggered} updated the headline image",
      "__squad__/edit/publicity" => "{triggered} set the squad to " . array_flip(Squad::$joinable)[$this->reference_id],
      "__member__/restricted" => "{triggered} restricted {affected}",
      "__member__/setfree" => "{triggered} set {affected} free",
      "__member__/promoted" => "{triggered} promoted {affected}",
      "__member__/demoted" => "{triggered} demoted {affected}",
      "__member__/left" => $is_same_user ? "{triggered} has left" : "{triggered} removed {affected}",
      "__member__/kicked" => "{triggered} has kicked {affected}",
      "__member__/joined" => $is_same_user ? "{triggered} has joined" : "{triggered} accepted {affected} to join",
      "__member__/chief+new" => "{triggered} left for {affected} to be the new chief",

      default => "{triggered} is cool",
    };
  }
}
