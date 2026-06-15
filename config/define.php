<?php

use Heiakim\Utils\Utils;
use Heiakim\Application\Application;
use Heiakim\Application\Setting;
use Heiakim\Application\Cookie;
use Heiakim\Model\Session;
use Heiakim\Model\User;

# Environmental.
define("ENV", _env());
define("DEV", current_env() === "dev");
define("STAGE", current_env() === "stage");
define("PROD", current_env() === "prod");

# Application.
define("CURRENT_TIMESTAMP",  date("Y-m-d H:i:s", time()));
define("SEO_DESCRIPTION", ENV->SEO_DESCRIPTION);
define("SEO_KEYWORDS", ENV->SEO_KEYWORDS);
define('APP', new Application);
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

# Uhm...
define("APP_NAME", ENV?->APP_NAME);
define("APP_SLOGAN", ENV?->APP_SLOGAN);
define("APP_VERSION", ENV?->APP_VERSION);
define("SERVER_ADDRESS", ENV?->SERVER_ADDRESS);
define("CURRENCY", ENV?->CURRENCY);
define('MAINTENANCE', ENV?->MAINTENANCE);
define("HOME_URL", ENV?->SERVER_ADDRESS);
define("IMAGE", HOME_URL . "/assets/images");
define("SCRIPT", HOME_URL . "/assets/js");
define("STYLE", HOME_URL . "/assets/css");
define("FONT", HOME_URL . "/assets/fonts");
define("ICON", HOME_URL . "/icons");
define("VIDEO", HOME_URL . "/videos");
define("EMOJI", IMAGE . '/emojis');
define("SOUND", "https://raw.githubusercontent.com/brudermusscode/heia.kim-cdn/master/sounds");

# Locations.
define("NOT_FOUND", 'location: /a/404');

# Dynamic user content URLs.
define("USER_CONTENT_URL", ENV?->USER_CONTENT_URL);
define("AVATAR", ENV?->AVATAR_URL);
define("AVATAR_HISTORY", USER_CONTENT_URL . "/users/profile-images-history");
define("CLAN_IMAGE_URL", ENV?->USER_CONTENT_URL . "/squads");
define("CLAN_DEFAULT_LOGO_URL", CLAN_IMAGE_URL . "/logo-images/default.jpg");
define("CLAN_DEFAULT_HEADLINE_URL", CLAN_IMAGE_URL . "/headline-images/default.jpg");

# Templates.
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
define("REQUEST", TEMPLATE . "/global/_request.php");

# Directory pathing from root.
define("ASSET", ROOT . "/public/assets");
define("DATA_DIR", ENV?->DATA_DIR);
define("AVATAR_DIR", ENV?->AVATAR_DIR);
define("AVATAR_TMP_DIR", ENV?->AVATAR_TMP_DIR);
define("AVATAR_WEB_DIR", ENV?->AVATAR_WEB_DIR);
define("AVATAR_HISTORY_DIR", ENV?->AVATAR_HISTORY_DIR);
define("CLAN_LOGO", DATA_DIR . "/squads/logo-images");
define("CLAN_HEADLINE", DATA_DIR . "/squads/headline-images");

# Default return messages.
define("TRY_OR_STAFF", "Try again. If the error persists, open a support ticket on our <a extern target='_blank' href='" . ENV?->DISCORD_INVITE . "'>Discord &nbsp; <i class='ri-link-unlink'></i></a> using the '# Support'-channel.");
define("ONE_USER_POLICY", APP_NAME . " follows a one account per user policy. If you are having an account already, you are risking to get restricted in your gameplay.");

# Maintenance.
define(
  "BYPASS_MAINTENANCE",
  isset($_COOKIE['MAINTENANCE_TOKEN'])
    && $_COOKIE['MAINTENANCE_TOKEN'] == Utils::get_token("maintenance")
    ? 1 : 0
);
define(
  "IS_MAINTENANCE",
  MAINTENANCE
    && CurrentUser->id !== 3
    && !defined("MAINTENANCE_ENABLED")
    && !BYPASS_MAINTENANCE
);




# -------------------------------------------
# TODO: Please make better with user comback.
$__USER_COMEBACK = null;
$__TOKEN_COMEBACK = null;

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
# -------------------------------------------




# Privacy.
define('COOKIE_CONSENT', !empty($_COOKIE['COOKIE_CONSENT']) && $_COOKIE['COOKIE_CONSENT'] == 'true' ? true : false);
define('LEGAL_LANG', !empty($_COOKIE['LEGAL_LANG']) ? $_COOKIE['LEGAL_LANG'] : 'en');

/**
 * Thought of as an array that contains the states of various
 * informational popups or tooltips. Useful for guides and
 * tours.
 *
 * @var array
 */
define("INFO_WINDOWS", explode(",", Cookie::get("INFO_WINDOWS")));

# Frontend settings.
define("METRIC_NAME", ENV->METRIC_NAME);
define("METRIC_ICON", "stylus_laser_pointer");
define("PREMIUM_ICON", "workspace_premium");
define("PREMIUM_NAME", ENV->PREMIUM_NAME);
define("PREMIUM_PRICE", ENV->PREMIUM_PRICE);
define("PREMIUM_DISCORD_ROLE_ID", 1147920868907946045);
define("EDITOR_ICON", "shape_line");
define("ENABLE_ANIMATIONS", Cookie::exists("ANIMATIONS") && Cookie::get("ANIMATIONS") == 1 || !Cookie::exists("ANIMATIONS"));
define("ANIMATIONS_ENABLED", ENABLE_ANIMATIONS);
define("SOUNDS_ENABLED", Cookie::exists("SOUNDS") && Cookie::get("SOUNDS") == 1 || !Cookie::exists("SOUNDS"));
