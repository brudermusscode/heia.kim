<?php

use Bruder\Application\Session as SessionManager;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\Squad\SquadUser;

/**
 * Initialize a new session.
 */
new SessionManager;

/**
 * @var int
 */
$user_id = SessionManager::get("session")?->user_id ?? 0;

/**
 * @var User
 */
global $CurrentUser;
$CurrentUser = $user_id
  ? User::find($user_id) ?? User::guest()
  : User::guest();

unset($user_id);

global $CurrentUser;
$CurrentUser = $CurrentUser;

/**
 * @var ?Squad
 */
global $CurrentSquad;
$CurrentSquad = $CurrentUser->exists && $CurrentUser->has_squad()
  ? $CurrentUser->squad
  : null;

global $CurrentSquadUser;
$CurrentSquadUser = $CurrentSquad && $CurrentSquad->exists
  ? $CurrentUser->squad_user
  : null;

/**
 * Ensure the user having all dependencies.
 */
if ($CurrentUser->id > 0) {

  /**
   * Create privacy settings.
   */
  if (!$CurrentUser->privacy)
    $CurrentUser->privacy()
      ->create();

  /**
   * Create settings.
   */
  if (!$CurrentUser->settings)
    $CurrentUser->settings()
      ->create();

  /**
   * Update activity.
   */
  // $CurrentUser->update([
  //   "latest_activity" => time(),
  // ]);
}
