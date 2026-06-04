<?php

namespace Heiakim\Registry;

abstract class ApiRegistry
{

  /**
   * Mapping for classes that provide basic functionality to interact
   * with a provider's API in a non user-grant specific fashion.
   */
  public static array $map = [
    "osu!" => \Heiakim\Model\ApiOsu::class,
  ];
}
