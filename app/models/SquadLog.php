<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquadLog extends Justin
{

  protected $fillable = [
    "user_id",
    "affected_user_id",
    "reference_id",
    "type",
  ];

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return BelongsTo<User>
   */
  public function affected_user()
  {
    return $this->belongsTo(User::class, "affected_user_id");
  }

  /**
   * @return BelongsTo<Image>
   */
  public function image()
  {
    return $this->belongsTo(Image::class, "reference_id");
  }

  /**
   * @var string
   */
  public function display_type()
  {
    return (object) match ($this->type) {
      "create" => [
        "append_text" => "created squad",
        "icon" => "add_circle",
      ],
      "update:name" => [
        "append_text" => "updated name",
        "icon" => "edit",
      ],
      "update:tag" => [
        "append_text" => "updated tag",
        "icon" => "edit",
      ],
      "update:logo" => [
        "append_text" => "updated logo",
        "icon" => "blur_circular",
      ],
      "update:headline" => [
        "append_text" => "updated headline",
        "icon" => "blur_circular",
      ],
      "update:joinable" => [
        "append_text" => "publicity updated",
        "icon" => $this->reference_id === Squad::$joinable_map["public"]
          ? "globe"
          : (
            $this->reference_id === Squad::$joinable_map["private"]
            ? "public_off"
            : "vpn_lock"
          ),
      ],
      "update:modes" => [
        "append_text" => "updated modes",
        "icon" => "chess",
      ],
      "member:restrict" => [
        "append_text" => "restricted",
        "icon" => "front_hand",
      ],
      "member:setfree" => [
        "append_text" => "set free",
        "icon" => "shield_with_heart",
      ],
      "member:promote" => [
        "append_text" => "promoted",
        "icon" => "stat_2"
      ],
      "member:demote" => [
        "append_text" => "demoted",
        "icon" => "keyboard_double_arrow_down"
      ],
      "member:kick" => [
        "append_text" => "kicked",
        "icon" => "sports_martial_arts"
      ],
      "member:leave" => [
        "append_text" => "left",
        "icon" => "north_east"
      ],
      "member:join" => [
        "append_text" => "joined",
        "icon" => "south_west"
      ],
      "update:chief" => [
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

    $is_same_user = $TriggeredUser->is($AffectedUser);

    return match ($this->type) {
      "create" => "{triggered} created the squad",
      "update:logo" => "{triggered} updated the logo",
      "update:headline" => "{triggered} updated the headline image",
      "update:joinable" => "{triggered} set the squad to " . array_flip(Squad::$joinable_map)[$this->reference_id],
      "update:modes" => "{triggered} updated the modes",
      "member:restrict" => "{triggered} restricted {affected}",
      "member:setfree" => "{triggered} set {affected} free",
      "member:promote" => "{triggered} promoted {affected}",
      "member:demoted" => "{triggered} demoted {affected}",
      "member:leave" => $is_same_user ? "{triggered} has left" : "{triggered} removed {affected}",
      "member:kick" => "{triggered} has kicked {affected}",
      "member:join" => $is_same_user ? "{triggered} has joined" : "{triggered} accepted {affected} to join",
      "update:chief" => "{triggered} left for {affected} to be the new chief",

      default => "{triggered} is cool",
    };
  }
}
