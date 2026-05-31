<?php

// L::2024-12-15 21:39:01#

namespace Heiakim\Job;

use Heiakim\Job;
use Heiakim\Enum\Privilege;
use Heiakim\Model\Order;
use Heiakim\Model\User;
use Heiakim\Time\Time;
use DateTime;

class RemoveOpenOrders extends Job
{
  /**
   * @var string
   */
  protected $interval = "+1 week";

  /**
   * @return void
   */
  public function execute(?string $interval = null)
  {
    if (!Time::has_passed($this->last_executed(__FILE__), $interval ?? $this->interval))
      return;

    $this->update_last_executed(__FILE__);

    /**
     * Build timestamps.
     */
    $timestamp_now = date("Y-m-d H:i:s");
    $date_now = new DateTime($timestamp_now);
    $date_old = $date_now->modify("-1 hour");

    /**
     * @var string
     *
     * Get a timestamp 1 hour in the past as UNIX.
     */
    $timestamp_old = $date_old->getTimestamp();

    /**
     * @var ?Order
     */
    $OpenOrders = Order::where("order_status", "OPEN")
      ->whereRaw("UNIX_TIMESTAMP(created_at) < ?", $timestamp_old)
      ->get();

    /**
     * @var int
     */
    $count = $OpenOrders->count();

    /**
     * No orders?
     */
    if (!$count) return;

    /**
     * Delete them!
     */
    foreach ($OpenOrders as $Order)
      $Order->delete();

    /**
     * Return.
     */
    echo "\n" . date("d.m.Y<;>H:i:s") . "<;>Removed $count open orders!<&>\n";
  }
}
