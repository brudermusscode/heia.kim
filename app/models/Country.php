<?php

namespace Heiakim\Model;

use Heiakim\Application\Application;
use Heiakim\Justin;
use Heiakim\Model\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Locale;

class Country extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "abbreviation",
    "updated_at",
  ];

  /**
   * @return HasMany<User>
   */
  public function users()
  {
    return $this->hasMany(User::class, "country", "abbreviation");
  }

  /**
   * @return string
   */
  public function display()
  {
    return $this->abbreviation === 'xx' ? self::__("Unknown country")
      : ($this->abbreviation == "hk" ? "Hong Kong" : Locale::getDisplayRegion("-" . strtoupper($this->abbreviation), LOCALE));
  }

  /**
   * @return void
   *
   * NOTE: Includes /helper/_image_country.php
   */
  public function icon()
  {
    $country_abbreviation = $this->abbreviation;
    return include ROOT . "/app/templates/helper/_image_country.php";
  }
}
