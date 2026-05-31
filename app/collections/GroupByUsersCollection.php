<?php

namespace Heiakim\Collection;

use Heiakim\Trait\GroupableByUsers;
use Illuminate\Database\Eloquent\Collection;

class GroupByUsersCollection extends Collection
{
  use GroupableByUsers;
}
