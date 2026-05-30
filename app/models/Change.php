<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Heiakim\Trait\HasDefaultUser;

class Change extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "type",
    "previous_value",
    "updated_value",
    "updated_at",
  ];
}
