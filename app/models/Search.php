<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\HasDefaultUser;

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
