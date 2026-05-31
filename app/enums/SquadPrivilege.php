<?php

namespace Heiakim\Enum;

enum SquadPrivilege: int
{
  case CHIEF = 1 << 20; // 1048576
  case COMMUNITY_MANAGER = 1 << 19; // 524288
  case COORDINATOR = 1 << 16; // 65536
  case CONTENT_GUARDIAN = 1 << 12; // 4096
  case ASSISTANT = 1 << 4; // 16
  case UNRESTRICTED = 1 << 2; // 4
  case MEMBER = 1 << 1; // 2

  /**
   * @return object
   */
  public function get_display()
  {
    return (object) match ($this) {
      self::CHIEF => [
        "name" => "Chief",
        "icon" => "crown",
        "tasks" => "Big Bruder",
        "full_tasks" => "<strong>The chief is the highest privileged member</strong> of the squad and can take action anywhere. They are untouchable!",
      ],
      self::COMMUNITY_MANAGER => [
        "name" => "Community Manager",
        "icon" => "person_celebrate",
        "tasks" => "Manage staff & members",
        "full_tasks" => "Community Management is responsible for a friendly being together in a squad. Together with the chief, they are the only ones to remove members and players of certain staff grade below their own.",
      ],
      self::COORDINATOR => [
        "name" => "Team Coordinator",
        "icon" => "handshake",
        "tasks" => "Invite/Accept members",
        "full_tasks" => "Team coordinators are responsible for coordinating the player base of a squad. They can <strong>accept join requests, send out invites and restrict members</strong>.",
      ],
      self::CONTENT_GUARDIAN => [
        "name" => "Content Guardian",
        "icon" => "swords",
        "tasks" => "Moderate community made content",
        "full_tasks" => "Content Guardians can <strong>remove any posts from members like threads, images or comments</strong>. They cannot remove content from Content Guardians and higher privileged members.",
      ],
      self::ASSISTANT => [
        "name" => "Assistant",
        "icon" => "support",
        "tasks" => "Helps out",
        "full_tasks" => "Any idea, what an assistant could do? Tell us on {discord-link}!",
      ],
      self::UNRESTRICTED => [
        "name" => "Member",
        "icon" => "taunt",
        "tasks" => "Can interact freely with the community",
      ],
      self::MEMBER => [
        "name" => "Restricted",
        "icon" => "front_hand",
        "tasks" => "Socially excluded",
      ],
      default => [
        "name" => "Member",
        "icon" => "taunt",
        "tasks" => "Be a part of this awesome squad",
      ],
    };
  }
}
