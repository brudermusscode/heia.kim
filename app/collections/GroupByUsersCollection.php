<?php

namespace Bruder\Heiakim\Collection;

use Bruder\Heiakim\Trait\GroupableByUsers;
use Illuminate\Database\Eloquent\Collection;

class GroupByUsersCollection extends Collection
{
  use GroupableByUsers;
}
