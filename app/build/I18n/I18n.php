<?php

namespace Heiakim\I18n;

class I18n
{

  /**
   * @var array
   */
  public $translations = [];

  /**
   * @var string
   */
  public static $default_locale = "en";

  /**
   * @var array
   */
  public static $locales = [
    "en",
    "de",
    "pl",
  ];

  /**
   * @param string $locale
   */
  public function __construct(string $locale = "en")
  {
    $this->translations = $this->get_translations_by_locale($locale);
  }

  /**
   * @param string $locale
   * @return array
   */
  private function get_translations_by_locale(string $locale)
  {
    $originals_file_path = ROOT . "/locales/original.php";
    $file_path = ROOT . "/locales/$locale/t.php";

    return include file_exists($file_path) ? $file_path : $originals_file_path;
  }

  /**
   * Returns the translation by a given key or falls back to an
   * empty string.
   *
   * @param string $translation_key
   * @return string
   */
  public function by_key(string $translation_key)
  {
    return $this->translations[$translation_key] ?? htmlspecialchars($translation_key);
  }
}
