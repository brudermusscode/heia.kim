<?php

namespace Heiakim\Application;

use Heiakim\Database\Manager as DBM;
use Heiakim\Application\Cookie;
use Heiakim\Time\Time;

class Application
{

  protected static object $default_dialogues;

  public static array $themes = [
    "sangria-sunset",
    "springtime-symphony",
    "lemon-zest",
    "azure-dreams",
    "twilight-noir",
    "nocturnal-nebula",
    "discord",
    "golden-embrace",
    // "youtube",
  ];

  public string $current_theme;

  public string $main_theme;

  public bool $dark_mode_enabled;

  public string $current_theme_class;

  public Setting $settings;

  public function __construct()
  {
    $this->current_theme = $this->get_current_theme();
    $this->main_theme = $this->get_main_theme();
    $this->dark_mode_enabled = Cookie::exists("DARKMODE") && Cookie::get("DARKMODE") == 1 || !Cookie::exists("DARKMODE");
    $this->current_theme_class = $this->get_current_theme_class();
  }

  /**
   * Gets the current theme or sets the default one, if none is set.
   *
   * @return string The theme.
   */
  public function get_current_theme()
  {
    $current_theme = Cookie::get("THEME");
    $main_theme = self::get_main_theme();

    /**
     * Any theme is set?
     */
    if (!$current_theme || !in_array($current_theme, self::$themes)) {
      Cookie::set(
        name: "THEME",
        value: $main_theme,
        time: "+2 years",
        httponly: false,
      );

      return $main_theme;
    }

    $this->current_theme = $current_theme;

    return $this->current_theme;
  }

  /**
   * Gets a string for the body class to display for theme to take effect.
   *
   *  @return string The string for class.
   */
  public function get_current_theme_class()
  {
    $theme = "theme--" . $this->current_theme;

    if ($this->dark_mode_enabled)
      $theme .= "-dark";

    return $theme;
  }

  /**
   * Sets the main theme to a one from the themes array.
   *
   * @return string The main theme.
   */
  public function get_main_theme()
  {
    return self::$themes[3];
  }

  /**
   * Returns all themes available.
   *
   * @return array
   */
  public function get_all_themes()
  {
    return self::$themes;
  }

  /**
   * Fetches all features
   *
   * @param string $name (OPTIONAL) The name of the feature.
   * @return object Associative PDO object.
   */
  public static function get_features(?string $name = null)
  {

    $sql = "SELECT * FROM features";

    if ($name)
      $sql .= " WHERE name = '$name' ";

    $sql .= " ORDER BY id ASC ";

    $stmt = (new DBM)->select(
      $sql,
      [],
      $name ? false : true
    );

    return $stmt ? (object) $stmt : null;
  }

  /**
   * @return array
   */
  public static function get_default_dialogues()
  {

    /**
     * @var object
     */
    $env = _env();

    $discord_link_channel =
      "please visit our <a extern target='_blank' href='" . $env->DISCORD_INVITE . "'>Discord &#160; <i class='ri-link-unlink'></i></a> and open a ticket through the (🆘 Tickets) channel.";

    return [
      "TRY_OR_STAFF" => "Try again. If the error persists, $discord_link_channel",

      "NEED_HELP" => "If you need help, $discord_link_channel",

      "THINK_MISTAKE" => "If you think this is a mistake, $discord_link_channel",

      "UNAVAILABLE_ASK_SUPPORT" => "If it's not there, $discord_link_channel",

      "LOST_CREDENTIALS_RESET" => "If you have lost your credentials, try to <a href='/password-reset'>reset your password &nbsp; <i class='ri-link-unlink'></i></a>.",

      "ONLY_STAFF" => "Please open a support ticket on our <a extern target='_blank' href='" . $env->DISCORD_INVITE . "'>Discord &#160; <i class='ri-link-unlink'></i></a> using the '# Support'-channel and tell us where this error happened.",

      "ONE_USER_POLICY" => $env->APP_NAME . " follows a one account per human policy. If you have signed up already, you are risking to get restricted in your gameplay.",

      "SUPPORT_US" => "Consider supporting " . $env->APP_NAME . " and get some more!",

      "INVALID_REQUEST" => "<strong>An invalid request has been sent to the client.</strong> If you keep seeing this error, $discord_link_channel",

      "ERROR_TRANSACTION" => "<strong>An error occurred while processing your request.</strong> If you keep seeing this error, $discord_link_channel",

      "NO_PERMISSIONS" => "<strong>You have no permissions to take action here, my friend.</strong>",

      "RESTRICTED" => "You are currently in restricted mode and therefore not able to take action here.",

      "UNVERIFIED" => "You are not yet verified and therefore not able to take action here.",

      "NOT_LOGGED" => "<strong>You need to <a href='/login'>Login &#160; <i class='ri-link-unlink'></i></a> before taking action here.</strong>",
      "ALREADY_LOGGED" => "<strong>You are already logged in.</strong>",

      "SOCIALLY_EXCLUDED" => "<strong>You can't interact with the community at the moment.</strong>",

      "SOCIALLY_EXCLUDED_THIRD" => "<strong>This player can't interact with the community at the moment.</strong> Wait for them to get reintegrated.",

      "FIELDS_MISSING" => "Some required fields have not been passed, check the form!",

      "FEATURE_DISABLED" =>
      "This feature is currently disabled. Please wait for it to be up again.",

      # ? API/Vendor/OAuth
      "API_CONNECTED_ALREADY" => "<strong>This user is connected already!</strong>",

      "INVALID_API_CALL" => "<strong>Call to third party's API was scuffed.</strong>",

      "AUTH_FAILED" => "<strong>Authentication failed.</strong> Please restart the authentication process.",

      # ? Premium - call to actions.
      "UNLOCK_MORE_WITH_PREMIUM" => "You can unlock more with buying <a data-action=\"popup:open\" data-href=\"/user/buy-premium\">Premium+ &#160; <i class='ri-link-unlink'></i></a>",

      "NO_PREMIUM_BUY" => "<strong>You are not a Premium+ member!</strong> <a data-action=\"popup:open\" data-href=\"/user/buy-premium\">Unlock it now &#160; <i class='ri-link-unlink'></i></a>",

      "PREMIUM_UNLOCK_NOW" => "<a data-action=\"popup:open\" data-href=\"/user/buy-premium\">Unlock it now &#160; <i class='ri-link-unlink'></i></a>",

      # ? Squad.
      "NO_SQUAD" => "<strong>Join a squad first!</strong>",
      "HAS_SQUAD" => "<strong>You are already a member of a squad.</strong>",
    ];
  }

  /**
   * @param string $template
   * @return string
   */
  // TODO: Exchange braced variables in GET files.
  public static function replace_braced_variables(string $template, array $dependencies)
  {
    $return = str_replace("{privacy-policy-link}", "<a href='/legal/privacy' normal>Privacy Policy</a>", $template);
    $return = str_replace("{profile-editor-link}", "<a href='/editor' normal>Profile Editor</a>", $template);
    $return = str_replace("{discord-link}", "<a href='" . $dependencies["DISCORD"] . "' extern target='_blank' normal>Discord server</a>", $return);
    $return = str_replace("{app-name}", $dependencies["APP_NAME"], $return);
    $return = str_replace("{name-changes-count}", $dependencies["LOGGED"] ? $dependencies["CurrentUser"]?->settings->name_changes_left : 0, $return);
    $return = str_replace("{wipes-left-count}", $dependencies["LOGGED"] ? $dependencies["CurrentUser"]?->settings->account_wipes_left : 0, $return);
    $return = str_replace(
      "{last-wipe-ago}",
      $dependencies["LOGGED"]
        && $dependencies["CurrentUser"]?->settings->account_wiped_at
        ? Time::ago($dependencies["CurrentUser"]?->settings->account_wiped_at, true)
        : "",
      $return
    );
    $return = str_replace("{premium-feature-name}", $dependencies["PREMIUM_NAME"], $return);
    $return = str_replace("{legal-applications-link}", '<a href="/legal/applications" normal>Applications page</a>', $return);

    return $return;
  }
}
