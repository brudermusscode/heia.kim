<?php

use Heiakim\Application\Cookie;

/**
 * Set the default timezone.
 */
date_default_timezone_set('Europe/Berlin');

/**
 * Set a bunch of cookies if not yet set.
 */
foreach (["DARKMODE", "ANIMATIONS", "SOUNDS"] as $cookie_name) :
  if (Cookie::exists($cookie_name))
    continue;

  Cookie::set(
    name: $cookie_name,
    value: 1,
    time: "+2 year",
    httponly: false,
  );
endforeach;
