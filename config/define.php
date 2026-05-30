<?php

use Bruder\Utils\Utils;
use Bruder\Application\Application;
use Bruder\Application\Setting;
use Bruder\Application\Cookie;
use Bruder\Application\CurrentUser;
use Bruder\Heiakim\Enum\Privilege;
use Bruder\Heiakim\Model\Session;
use Bruder\Heiakim\Model\User;

define("ENV", _env());
define("DEV", current_env() === "dev");
define("STAGE", current_env() === "stage");
define('PROD', current_env() === "prod");

/**
 * Define Application constants to use around the website.
 */
define("CURRENT_TIMESTAMP",  date("Y-m-d H:i:s", time()));
define("APP_SETTING", (object) Setting::first()->getAttributes());
define("APP_SLOGAN", ENV->APP_SLOGAN);
define("SEO_DESCRIPTION", ENV->SEO_DESCRIPTION);
define("SEO_KEYWORDS", ENV->SEO_KEYWORDS);
define('APP', new Application);
define("ROOT", _root());
define("PREROOT", dirname(ROOT));
define("VENDOR", ROOT . '/vendor');
define("CONFIG", ROOT . '/config');
define("ROUTE", ROOT . '/config/routes');
define("MODEL", ROOT . '/app/models');
define("BUILD", ROOT . '/app/build');
define("LOG", ENV->LOG_PATH);
define("JSON_RESPONSE", 'Content-type: application/json');
define("HTML_RESPONSE", 'Content-type: text/html');
define("JSON", "JSON");
define("HTML", "HTML");
define(false, 0);
define(true, 1);

/**
 * Locations.
 */
define("NOT_FOUND", 'location: /a/404');

/**
 * Template directory/UI components.
 */
define("TEMPLATE", ROOT . '/app/templates');
define("LOGO", TEMPLATE . '/global/logos');
define("HELPER", TEMPLATE . '/helper');
define("COMPONENT", TEMPLATE . '/components/');
define("UNAVAILABLE", TEMPLATE . '/global/unavailable.php');
define("GET_CONTENT_NOTHING", TEMPLATE . '/global/get-content-nothing.html');
define("SIGN_UP_NOW", TEMPLATE . '/global/_join_now_inline.php');
define("SNOW", TEMPLATE . '/global/_snow.php');
define("DOTLOADER", COMPONENT . "/dot-loader.html");
define("CIRLOADER", COMPONENT . "/circle-loader.html");

/**
 * Base definitions.
 */
define('APP_VERSION', ENV?->APP_VERSION);
define('MAINTENANCE', ENV?->MAINTENANCE);
define("APP_NAME", ENV?->APP_NAME);
define("HOME_URL", ENV?->SERVER_ADDRESS);
define("IMAGE", HOME_URL . "/assets/images");
define("SCRIPT", HOME_URL . "/assets/js");
define("STYLE", HOME_URL . "/assets/css");
define("FONT", HOME_URL . "/assets/fonts");
define("ICON", HOME_URL . "/icons");
define("VIDEO", HOME_URL . "/videos");
define("EMOJI", IMAGE . '/emojis');
define("SOUND", "https://raw.githubusercontent.com/brudermusscode/heia.kim/master/sounds");

/**
 * Default texts for returning just like the default dialogues from
 * Application class.
 */
define("TRY_OR_STAFF", "Try again. If the error persists, open a support ticket on our <a extern target='_blank' href='" . ENV?->DISCORD_INVITE . "'>Discord &nbsp; <i class='ri-link-unlink'></i></a> using the '# Support'-channel.");
define("ONE_USER_POLICY", APP_NAME . " follows a one account per user policy. If you are having an account already, you are risking to get restricted in your gameplay.");

/**
 * Pathing.
 */
define("ASSET", ROOT . "/public/assets");
define("DATA_DIR", ENV?->DATA_DIR);
define("AVATAR_DIR", ENV?->AVATAR_DIR);
define("AVATAR_TMP_DIR", ENV?->AVATAR_TMP_DIR);
define("AVATAR_WEB_DIR", ENV?->AVATAR_WEB_DIR);
define("AVATAR_HISTORY_DIR", ENV?->AVATAR_HISTORY_DIR);
define("CLAN_LOGO", DATA_DIR . "/squads/logo-images");
define("CLAN_HEADLINE", DATA_DIR . "/squads/headline-images");

/**
 * URLS
 */
define("USER_CONTENT_URL", ENV?->USER_CONTENT_URL);
define("AVATAR", ENV?->AVATAR_URL);
define("AVATAR_HISTORY", USER_CONTENT_URL . "/users/profile-images-history");
define("CLAN_IMAGE_URL", ENV?->USER_CONTENT_URL . "/squads");
define("CLAN_DEFAULT_LOGO_URL", CLAN_IMAGE_URL . "/logo-images/default.jpg");
define("CLAN_DEFAULT_HEADLINE_URL", CLAN_IMAGE_URL . "/headline-images/default.jpg");

/**
 * Privacy measaures
 */
define('COOKIE_CONSENT', !empty($_COOKIE['COOKIE_CONSENT']) && $_COOKIE['COOKIE_CONSENT'] == 'true' ? true : false);
define('LEGAL_LANG', !empty($_COOKIE['LEGAL_LANG']) ? $_COOKIE['LEGAL_LANG'] : 'en');

/**
 * Website settings
 */
define("ENABLE_ANIMATIONS", Cookie::exists("ANIMATIONS") && Cookie::get("ANIMATIONS") == 1 || !Cookie::exists("ANIMATIONS"));
define("ANIMATIONS_ENABLED", ENABLE_ANIMATIONS);
define("SOUNDS_ENABLED", Cookie::exists("SOUNDS") && Cookie::get("SOUNDS") == 1 || !Cookie::exists("SOUNDS"));
define("PAGE_NAVIGATOR", TEMPLATE . "/global/_page_navigator.php");

/**
 * @var bool
 */
define("LOGGED", CurrentUser::is_authenticated());

/**
 * @var \Bruder\Heiakim\Model\User
 */
define("NINGEN", $CurrentUser);

/**
 * @var \Bruder\Heiakim\Model\User
 */
define("USER", NINGEN);

/**
 * @var bool
 */
define(
  "VERIFIED",
  LOGGED && $CurrentUser->privacy?->accepts_policies
);

/**
 * @var bool
 */
define(
  "RESTRICTED",
  LOGGED && !$CurrentUser->has_privileges_of(Privilege::UNRESTRICTED)
);

/**
 * @var bool
 */
define(
  "FROZEN",
  LOGGED && $CurrentUser->frozen_at
);

/**
 * Hide certain things from users when not logged in. Should prevent
 * Crawlers from indexing our images.
 */
define("GDPR", !LOGGED);

/**
 * Settings
 */
/**
 * Bypass maintenance
 *
 * Get the bypass maintenance token and check if a cookie with the
 * value of it is present in the current user's client.
 */
$token = Utils::get_token("maintenance");

define(
  "BYPASS_MAINTENANCE",
  isset($_COOKIE['MAINTENANCE_TOKEN'])
    && $_COOKIE['MAINTENANCE_TOKEN'] == $token
    ? 1 : 0
);

unset($token);

/**
 * Maintenance
 */
define(
  "IS_MAINTENANCE",
  APP_SETTING->is_maintenance
    && $CurrentUser->id !== 3
    && !defined("MAINTENANCE_ENABLED")
    && !BYPASS_MAINTENANCE
);

/**
 * Owner & Admins
 */
define("OWNER", in_array($CurrentUser->id, [3, 4]));
define("SUPER_USER", $CurrentUser->is_super_user());

/**
 * User had been logged in before. This is to prevent multiple
 * sign ups.
 */
$__USER_COMEBACK = Cookie::get(CurrentUser::$persistent_cookies[2]);
$__TOKEN_COMEBACK = Cookie::get(CurrentUser::$persistent_cookies[3]);

/**
 * @var ?Session
 */
$__SESSION_COMBACK = $__USER_COMEBACK && $__TOKEN_COMEBACK
  ? Session::where("token", $__TOKEN_COMEBACK)
  ->where("user_id", (int) $__USER_COMEBACK)
  ->first()
  : null;

unset($__USER_COMEBACK, $__TOKEN_COMEBACK);

/**
 * @var ?User
 */
define("USER_COMEBACK", $__SESSION_COMBACK->user ?? null);

/**
 * Get the INFO_WINDOWS cookie and split it into an array taht can
 * be used to check whether or not an info window in any section
 * of the website should be shown or hidden.
 *
 * Set the cookie if it is not available.
 */
if (!Cookie::exists("INFO_WINDOWS"))
  Cookie::set("INFO_WINDOWS", false, "+2 years");

/**
 * @var array
 */
define("INFO_WINDOWS", explode(",", Cookie::get("INFO_WINDOWS")));

/**
 * Premium & Icons
 */
define("METRIC_NAME", APP_SETTING->metric);
define("METRIC_ICON", "stylus_laser_pointer");
define("PREMIUM_ICON", "workspace_premium");
define("PREMIUM_NAME", APP_SETTING->premium_feature_name);
define("PREMIUM_DISCORD_ROLE_ID", 1147920868907946045);
define("EDITOR_ICON", "shape_line");
