<?php

ini_set("log_errors", 1);
ini_set("error_log", _env("LOG_PATH") . "/php_errors.log");
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL & ~E_DEPRECATED);

# Disable all error displaying in production mode, as we do not want to show the User
# that we suck at programming 🙂
if (current_env() == "prod") {
  ini_set("display_errors", 0);
  ini_set("display_startup_errors", 0);
}

/**
 * Reformat the php exception message.
 *
 * @param ?callable $callable
 * @return void
 */
set_exception_handler(function ($ex) {

  header("HTTP/1.1 400 Bad Request");

  $type = get_class($ex);
  $time = date('d.m.Y<;>H:i:s');

  error_log("\n$time<;>$type<;>{$ex->getMessage()}<;>{$ex->getFile()}:{$ex->getLine()}<;>{$ex->getTraceAsString()}<&>\n");

  # We do not want to show any exception to the User in production, so return early.
  if (current_env() === "prod")
    return;

  $stacktrace = $ex->getTraceAsString();
  $app_inititialized = defined("APP_INIT") && APP_INIT === true;

  # Save all base js & css files to the below variable inside an output buffer, so we
  # can easily include it in our exception message.
  ob_start();
  include ROOT . "/app/templates/global/_yield-requires.php";
  $include_styles = $app_inititialized ? "" : ob_get_clean();

  if ($app_inititialized) ob_end_clean();

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
 * Reformat the php error/warning message.
 *
 * @param int $errno
 * @param string $errstr
 * @param ?string $errfile
 * @param ?int $errline
 * @param ?array $errcontext
 * @return void
 */
set_error_handler(function (
  int $errno,
  string $errstr,
  ?string $errfile = null,
  ?int $errline = null,
  ?array $errcontext = null
) {

  # Was @ used to suppress errors? In this case we return immediately to really sur-
  # press it 🙂
  if (!(error_reporting() & $errno))
    return;

  $type = match ($errno) {
    E_ERROR => "PHP Error",
    E_NOTICE => "PHP Notice",
    E_WARNING => "PHP Warning",
    E_PARSE => "PHP Parse Error",
    default => "Unknown Error Type"
  };

  $time = date("d.m.Y<;>H:i:s");

  # This will log the error to the standart error log file, which should be located in
  # storage/logs/php_errors.log -
  error_log("\n{$time}<;>{$type}<;>{$errstr}<;>{$errfile}:{$errline}<&>\n");

  # In production mode, we do not want to show any errors. We return here, right after
  # logging.
  if (current_env() === "prod")
    return;

  echo "<span><strong>{$type}</strong><br> $errstr<br>📁 {$errfile}:{$errline}</span>";
});
