<?php

/**
 * Standard ini values for error logging.
 */
ini_set("log_errors", 1);
ini_set("error_log", _env("LOG_PATH") . "/php_errors.log");
error_reporting(E_ALL & ~E_DEPRECATED);

/**
 * Set error displaying based on current evnironment.
 */
if (current_env() == "dev" && !_env("STAGING")) {
  ini_set("display_errors", 1);
  ini_set("display_startup_errors", 1);
} else {
  ini_set("display_errors", 0);
  ini_set("display_startup_errors", 0);
}

/**
 * Reformat the php exception message.
 */
set_exception_handler(function ($ex) {

  header("HTTP/1.1 400 Bad Request");

  $type = get_class($ex);
  $time = date('d.m.Y<;>H:i:s');

  /**
   * Log to the default log file defined for the local environment.
   */
  error_log("\n$time<;>$type<;>{$ex->getMessage()}<;>{$ex->getFile()}:{$ex->getLine()}<;>{$ex->getTraceAsString()}<&>\n");

  /**
   * Only show the exceptions in dev env.
   */
  if (current_env() !== "dev" && !_env("STAGING"))
    return;

  /**
   * @var string
   */
  $stacktrace = $ex->getTraceAsString();

  /**
   * @var bool
   */
  $app_init = defined("APP_INIT") && APP_INIT === true;

  /**
   * Start the outpout buffer and include the main styles if the
   * app is not yet completly initialized. Otherwise fallback to
   * an empty string, because we already have the main styles included.
   */
  ob_start();

  /**
   * Any js and css file.
   */
  include ROOT . "/app/templates/global/_yield-requires.php";


  $include_styles = $app_init ? "" : ob_get_clean();

  /**
   * Clean the output buffer if the app is initialized.
   */
  if ($app_init) ob_end_clean();

  /**
   * Return the nice newly formatted exception screen!
   */
  echo <<<HTML
  $include_styles

  <exception-container>
    <mbutton wide close-overlay icon-only clickable
      style="position:fixed;top:1.2em;right:1.2em;">
      <mi wide>emoji_symbols</mi>
    </mbutton>

    <inside fl fldircol gap=smol+>
      <p text wide bold tac mb18>$type</p>

      <container elevated class=inner rounded=wide pinline12 pt18 pb12>
        <div fl gap=smol alic title-inline>
          <mi>bug_report</mi>
          <p text smol ttup bold>Description</p>
        </div>

        <div>
          <div style="background: white;" pblock24 pinline28 rd24 mblock12>
            <p text midler style="font-family:'Times New Roman', serif;">{$ex->getMessage()}</p>
          </div>
        </div>

        <div class=bottom rd32 fl gap24 alic jucsb>
          <p text>{$ex->getFile()}</p>
          <p class=line text semibold>
            <mi mid>water</mi>
            &nbsp;:{$ex->getLine()}
          </p>
        </div>
      </container>

      <container elevated class=inner rd38 gap=smol fl fldircol gap=smol+
          style=background:#de4832;color:#ffecee;>
        <div fl gap=smol alic pt18 pinline32>
          <mi>stacks</mi>
          <p text smol ttup bold>Stacktrace</p>
        </div>

        <pre text smol>$stacktrace</pre>
      </container>
    </inside>
  </exception-container>
  HTML;
});

/**
 * Reformat the php exception message.
 */
set_error_handler(function (
  int $errno,
  string $errstr,
  ?string $errfile = null,
  ?int $errline = null,
  ?array $errcontext = null
) {

  # Was @ used to suppress errors?
  if (!(error_reporting() & $errno))
    return true;

  /**
   * @var string
   */
  $type = match ($errno) {
    E_ERROR => "PHP Error",
    E_NOTICE => "PHP Notice",
    E_WARNING => "PHP Warning",
    E_PARSE => "PHP Parse Error",
    default => "Unknown Error Type"
  };

  /**
   * @var string
   */
  $time = date("d.m.Y<;>H:i:s");

  /**
   * Log to the default log file defined for the local environment.
   */
  error_log("\n{$time}<;>{$type}<;>{$errstr}<;>{$errfile}:{$errline}<&>\n");

  /**
   * Only show the errors in dev env.
   */
  if (current_env() !== "dev" && !_env("STAGING"))
    return;

  echo "<span><strong>{$type}</strong><br> $errstr<br>📁 {$errfile}:{$errline}</span>";
});
