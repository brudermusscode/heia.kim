<?php

namespace Bruder\Heiakim\Authorization;

interface PermissionGuardInterface
{
  /**
   * @param string $interaction
   * @param string $section
   * @param mixed $privilege
   * @return bool
   */
  public static function can(string $interaction, string $section, $privilege);
}
