<?php

use Heiakim\Application\Application;
use Heiakim\I18n\I18n;
use Heiakim\Application\Cookie;

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
 * Gets the translation for a given key. Will return the text as is from the key if no
 * value was found.
 *
 * @param string $translation_key
 * @return string
 */
function __(string $translation_key)
{
  $text = htmlspecialchars_decode($GLOBALS["__I18n"]->by_key($translation_key));
  $text = Application::replace_braced_variables($text);

  return $text;
}

define("LOCALE", $__CURRENT_LOCALE);
