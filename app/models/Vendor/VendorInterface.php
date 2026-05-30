<?php

namespace Bruder\Heiakim\Model\Vendor;

interface VendorInterface
{

  /**
   * Builds a client for a given vendor service which following
   * functions can retrieve data from.
   *
   * @return object
   */
  public function get_client();

  /**
   * Create a link to the vendor API.
   *
   * @param object $params
   * @return object
   */
  public function get_auth_uri(object $params);

  /**
   * Starts a new request to the vendor API to retrieve data and
   * create a new entry in the database.
   *
   * @param object $params
   * @return object
   */
  public function new(object $params);

  /**
   * Things to do, when the request to the vendor API has been
   * successfully executed and we have got the data.
   *
   * @param object $params
   * @return object
   */
  public function success(object $params);

  /**
   * Retrieve the vendor tokens to connect to the API.
   *
   * @param string $vendor
   * @return ?array
   */
  public function oauth_credentials(string $vendor);
}
