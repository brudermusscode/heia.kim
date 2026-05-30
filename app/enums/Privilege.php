<?php

namespace Bruder\Heiakim\Enum;

enum Privilege: int
{
  case DEVELOPER = 1 << 15;
  case ADMINISTRATOR = 1 << 14;
  case COMMUNITY_MANAGER = 1 << 13;
  case MODERATOR = 1 << 12;
  case NOMINATOR = 1 << 11;
  case TOURNEY_MANAGER = 1 << 10;
  case ALUMNI = 1 << 7;
  case PREMIUM = 1 << 5;
  case SUPPORTER = 1 << 4;
  case VERIFIED = 1 << 2;
  case MEMBER = 1 << 1;
  case UNRESTRICTED = 1 << 0;
  case UNVERIFIED = 0;

  /**
   * Get the privilege as a string with an icon.
   *
   * @return object The privilege as a string with an icon.
   */
  public function get_display()
  {
    return (object) match ($this) {
      Privilege::DEVELOPER => [
        "name"  => "Developer",
        "icon" => "php",
      ],
      Privilege::ADMINISTRATOR => [
        "name"  => "Administrator",
        "icon" => "tune",
      ],
      Privilege::COMMUNITY_MANAGER => [
        "name"  => "Community Manager",
        "icon" => "supervised_user_circle",
      ],
      Privilege::MODERATOR => [
        "name"  => "Moderator",
        "icon" => "wb_twilight",
      ],
      Privilege::NOMINATOR => [
        "name"  => "Nominator",
        "icon" => "auto_mode",
      ],
      Privilege::TOURNEY_MANAGER => [
        "name"  => "Tourney Manager",
        "icon" => "local_activity",
      ],
      Privilege::ALUMNI => [
        "name"  => "Alumni",
        "icon" => "redeem",
      ],
      Privilege::PREMIUM,
      Privilege::SUPPORTER => [
        "name"  => "Premium+",
        "icon" => "workspace_premium",
      ],
      Privilege::VERIFIED => [
        "name"  => "Verified",
        "icon" => "verified",
      ],
      Privilege::MEMBER => [
        "name"  => "Member",
        "icon" => "egg",
      ],
      Privilege::UNRESTRICTED => [
        "name"  => "Unrestricted",
        "icon" => "egg",
      ],
      Privilege::UNVERIFIED => [
        "name"  => "Unverified",
        "icon" => "visibility_lock",
      ],
    };
  }

  /**
   * Evaluates the Privileges from a given int.
   *
   * @param int $bits
   * @return ?object
   */
  public static function by_bits(int $bits)
  {
    $privileges = [];

    foreach (Privilege::cases() as $privilegeName => $privilegeFlag) {
      $display = $privilegeFlag->get_display();

      if (($bits & $privilegeFlag->value) !== 0) {
        $privileges[] = (object) [
          "privilege" => $privilegeFlag,
          "bits" => $privilegeName,
          "name" => $display->name,
          "icon" => $display->icon,
        ];
      }
    }

    return $privileges ? $privileges : null;
  }
}
