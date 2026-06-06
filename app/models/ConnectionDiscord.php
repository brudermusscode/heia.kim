<?php

/**
 * This class represents a connection to an existing Discord account.
 */

namespace Heiakim\Model;

use Heiakim\Model\Vendor\Discord;
use Heiakim\Trait\IsConnectionProvider;
use Heiakim\Utils\Utils;

class ConnectionDiscord extends Connection
{
  use IsConnectionProvider;

  /**
   * @see https://osu.ppy.sh
   */
  protected const string PROVIDER = "discord";

  /**
   * Generates the link to the vendor's API where the user has to authorize their ac-
   * count.
   *
   * @return string
   * @see https://osu.ppy.sh/docs/#authorization-code-grant
   */
  public function generate_link()
  {

    $api = $this->api();
    $callback = $this->credentials["callback"][current_env()]["connect"];
    $return = $api["user-auth"]["endpoint"]
      . "?client_id=" . $this->credentials["client_id"]
      . "&response_type=" . $api["user-auth"]["response_type"]
      . "&redirect_uri=" . $callback
      . "&state=" . Utils::random_alpha_token(124)
      . "&scope=" . implode(" ", $this->scopes());

    return $return;
  }

  /**
   * @param int $role_id
   * @return bool
   */
  public function give_role(int $role_id)
  {
    return (new Discord)->give_role($role_id, $this->vendor_id);
  }

  /**
   * @param int $role_id
   * @return bool
   */
  public function take_role(int $role_id)
  {
    return (new Discord)->take_role($role_id, $this->vendor_id);
  }
}
