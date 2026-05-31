<?php

namespace Heiakim\Authorization;

use Heiakim\Exception\AuthorizationException;
use Heiakim\Enum\SquadPrivilege;

class SquadGuard implements PermissionGuardInterface
{
  /**
   * Baba people - Can do all.
   *
   * @var SquadPrivilege[]
   */
  const GROUP_MANAGE_SQUAD = [
    SquadPrivilege::CHIEF,
  ];

  /**
   * These can promote users and take any other setting possible.
   *
   * @var SquadPrivilege[]
   */
  const GROUP_MANAGE_USERS = [
    SquadPrivilege::CHIEF,
    SquadPrivilege::COMMUNITY_MANAGER,
  ];

  /**
   * These can invite new users to join, process any incoming join
   * requests and remove users from the squad.
   *
   * @var SquadPrivilege[]
   */
  const GROUP_COORDINATE_USERS = [
    SquadPrivilege::CHIEF,
    SquadPrivilege::COMMUNITY_MANAGER,
    SquadPrivilege::COORDINATOR,
  ];

  /**
   * These can edit and delete content posted, as well as create
   * untouchable content like closed threads.
   *
   * @var SquadPrivilege[]
   */
  const GROUP_MANAGE_CONTENT = [
    SquadPrivilege::CHIEF,
    SquadPrivilege::COMMUNITY_MANAGER,
    SquadPrivilege::CONTENT_GUARDIAN,
  ];

  /**
   * @param string $interaction
   * @param string $section
   * @param SquadPrivilege $privilege
   * @return bool
   */
  public static function can(string $interaction, string $section, $privilege)
  {
    /**
     * @var string
     */
    $default_return = self::return_with_error("Invalid section '$section' for interaction '$interaction'");

    return match ($interaction) {
      "manage" => match ($section) {
        "squad" => in_array($privilege, self::GROUP_MANAGE_SQUAD),
        "users" => in_array($privilege, self::GROUP_MANAGE_USERS),
        "content" => in_array($privilege, self::GROUP_MANAGE_CONTENT),
        default => $default_return,
      },

      "coordinate" => match ($section) {
        "users" => in_array($privilege, self::GROUP_COORDINATE_USERS),
        default => $default_return,
      },

      /**
       * Log new exception for illegal interaction.
       */
      default => self::return_with_error("Invalid interaction '$interaction'"),
    };
  }

  /**
   * @param string $message
   * @return false
   */
  private static function return_with_error(string $message)
  {
    new AuthorizationException($message);
    return false;
  }
}
