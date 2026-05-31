<?php

use Heiakim\Application\Cookie;
use Heiakim\Application\Session as SessionManager;
use Heiakim\Enum\Privilege;
use Heiakim\Model\Session;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var ?Session
 */
define("SESSION", Session::valid(
  user_id: Cookie::get(Session::$persistent_cookies[0]),
  token: Cookie::get(Session::$persistent_cookies[1])
));

# Repersist the Session's relations so anything is set.
# Always. And it will refresh all instances inside the
# PHP session object.
SESSION?->persist();

# As I have been using LOGGED around the app for always
# but returning a Session instance, we can define the old
# defitnion for LOGGED and set it to the new SESSION.
define("LOGGED", SESSION);

/**
 * @var User
 */
define("CurrentUser", LOGGED ? SessionManager::get("User") : User::guest());

# All bools, some settings for the current user.
define("OWNER", LOGGED && in_array(CurrentUser->id, [3, 4]));
define("SUPER_USER", LOGGED && CurrentUser->is_super_user());
define("VERIFIED", LOGGED && CurrentUser->privacy?->accepts_policies);
define("RESTRICTED", LOGGED && !CurrentUser->has_privileges_of(Privilege::UNRESTRICTED));
define("FROZEN", LOGGED && CurrentUser->frozen_at);

# Users having registered in the very early days will
# most likely not have some relations generated. So we
# can do this here! 🙂
if (LOGGED && !CurrentUser->profile)
  CurrentUser->profile()->create();

if (LOGGED && !CurrentUser->privacy)
  CurrentUser->privacy()->create();

if (LOGGED && !CurrentUser->settings)
  CurrentUser->settings()->create();

/**
 * @var ?Squad
 */
global $CurrentSquad;
$CurrentSquad = CurrentUser->exists && CurrentUser->has_squad()
  ? CurrentUser->squad
  : null;

/**
 * @var ?SquadUser
 */
global $CurrentSquadUser;
$CurrentSquadUser = $CurrentSquad && $CurrentSquad->exists
  ? CurrentUser->squad_user
  : null;
