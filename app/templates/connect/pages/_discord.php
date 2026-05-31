<?php

use Heiakim\Controller\Controller\Connect\ConnectDiscordController;

/**
 * Retrieve the Data from the vendor API.
 */
$Vendor = (new ConnectDiscordController)->create([
  "code" => $code,
  "state" => $state,
]);

/**
 * @var bool
 */
$failed = !$Vendor->status;

/**
 * @var bool
 */
$is_new_user = isset($Vendor->existing_user) ? !$Vendor->existing_user : true;
