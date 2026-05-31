<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\User;
use Heiakim\Trait\HasDefaultUser;

class Mailing extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "template",
    "user_id",
    "email",
    "subject",
    "token",
    "updated_at",
  ];
}
