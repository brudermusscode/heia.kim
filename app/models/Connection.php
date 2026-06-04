<?php

/**
 * This class represents any connection to a vendor.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\IsConnectionProvider;

class Connection extends Justin
{
  use IsConnectionProvider;
}
