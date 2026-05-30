<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Trait\HasDefaultUser;

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
