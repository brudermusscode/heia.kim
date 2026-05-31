<?php

namespace Heiakim\Trait;

use Heiakim\I18n\I18n;
use Heiakim\Application\Cookie;

trait Translation
{
  /**
   * Obtain translations from the I18n class and make it globally
   * accessible for all model.
   *
   * @return string
   */
  public static function __(string $key)
  {
    $locale = Cookie::get("LOCALE") ?? I18n::$default_locale;
    $I18N = new I18n($locale);

    return $I18N->by_key($key) ?? "";
  }
}
