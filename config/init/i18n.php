<?php

use Bruder\I18n\I18n;
use Bruder\Application\Cookie;

/**
 * Does the locale cookie exist? If not, fallback to the default.
 */
$__CURRENT_LOCALE = I18n::$default_locale;
$__LOCALE_BEING_REQUESTED = !empty($_GET["lang"]) ? htmlspecialchars($_GET["lang"]) : null;

/**
 * Set the locale cookie, if it doesn't exist.
 */
if (!Cookie::exists("LOCALE"))
  Cookie::set(
    name: "LOCALE",
    value: $__CURRENT_LOCALE,
    time: "+2 years",
    httponly: false,
  );
else if (in_array(Cookie::get("LOCALE"), I18n::$locales))
  $__CURRENT_LOCALE = Cookie::get("LOCALE");

/**
 * Validate the locale being requested, if one is being requested.
 */
if ($__LOCALE_BEING_REQUESTED && in_array($__LOCALE_BEING_REQUESTED, I18n::$locales)) {
  $__CURRENT_LOCALE = $__LOCALE_BEING_REQUESTED;

  /**
   * Set  the cookie for the requested locale.
   */
  Cookie::set(
    name: "LOCALE",
    value: $__CURRENT_LOCALE,
    time: "+2 years",
    httponly: false,
  );
}

/**
 * Save all the translations of the file with the array inside
 * this variable. A specific translation by key is requested
 * with the _ function.
 *
 * @var I18n
 */
$__I18n = new I18n(locale: $__CURRENT_LOCALE);

/**
 * Define the function for getting a specific key of the
 * translation file.
 */
function __(string $translation_key)
{
  return $GLOBALS["__I18n"]->by_key($translation_key);
}

define("LOCALE", $__CURRENT_LOCALE);
