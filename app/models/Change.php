<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\HasDefaultUser;

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
