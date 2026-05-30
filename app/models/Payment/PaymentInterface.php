<?php

namespace Bruder\Heiakim\Model\Payment;

interface PaymentInterface
{
  public function new(object $params);
  public function success(object $params);
  public function get_connection();

  /**
   * @param string $vendor
   * @return ?array
   */
  public function get_credentials(string $vendor);
}