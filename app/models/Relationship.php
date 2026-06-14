<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Model\User;

class Relationship extends Justin
{

  /**
   * @return User
   */
  public function user_sent()
  {
    return $this->belongsTo(User::class, 'user1');
  }

  /**
   * @return User
   */
  public function user_received()
  {
    return $this->belongsTo(User::class, 'user2');
  }
}
