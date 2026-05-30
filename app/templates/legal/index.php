<?php

/**
 * @var string
 */
$sub = filter_var(get("sub") ?? "index", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$action = filter_var(get("action") ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Create a base path which to use across all sites in this legal section
 */
$base_url = "/legal";

/**
 * The date displayed for the privacy policies last update
 */
$privacy_last_updated = date("d. M Y", strtotime(APP_SETTING->privacy_policies_updated_at));

echo "<div class=legal>";

$file_path = __DIR__ . "/pages/_$sub.php";
include file_exists($file_path) ? $file_path : __DIR__ . "/pages/_index.php";

echo "</div>";

?>

<div color=white>
  <?php include TEMPLATE . "/global/_scroll_end_logo.php"; ?>
</div>