<?php

namespace Heiakim\Model;

use Heiakim\Justin;

class ClientHash extends Justin
{
  public function user()
  {
    return $this->belongsTo(User::class, "id", "userid");
  }
}
