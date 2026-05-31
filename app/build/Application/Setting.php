<?php

namespace Heiakim\Application;

use Heiakim\Justin;

class Setting extends Justin
{
  /**
   * @var string
   */
  public $table = "web_settings";

  /**
   * @var array
   */
  protected $fillable = [
    "ranked_updated_at",
  ];
}
