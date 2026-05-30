<?php

namespace Bruder\Heiakim\Model;

use Bruder\Application\Application;
use Bruder\Justin;
use Bruder\Heiakim\Model\User;
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
   * @return User
   */
  public function user()
  {
    return $this->hasMany(User::class, "abbreviation", "country");
  }

  /**
   * @return string
   */
  public function display()
  {
    return $this->abbreviation === 'xx' ? self::__("Unknown country")
      : ($this->abbreviation == "hk" ? "Hong Kong" : Locale::getDisplayRegion("-" . strtoupper($this->abbreviation), LOCALE));
  }

  public function icon()
  {
    $country_abbreviation = $this->abbreviation;
    return include _root() . "/app/templates/helper/_image_country.php";
  }
}
