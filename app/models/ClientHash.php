<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;

class ClientHash extends Justin
{
  public function user()
  {
    return $this->belongsTo(User::class, "id", "userid");
  }
}
