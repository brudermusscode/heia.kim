<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Heiakim\Trait\HasDefaultUser;

class Search extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  public $fillable = [
    "type",
    "search",
    "updated_at",
  ];
}
