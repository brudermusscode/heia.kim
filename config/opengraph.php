<?php

/**
 * Predefine opengraph variables to always show an embedd when being
 * included in a link on Twitter, Facebook, Discord, ...
 */
$og = (object) [];
$og->url = HOME_URL . $_SERVER['REQUEST_URI'];
$og->app = APP_NAME;
$og->title = TITLE;
$og->desc = SEO_DESCRIPTION;
$og->image = IMAGE . "/logo/opengraph/large-bird-text-bg.png";

/**
 * Above variables will be overwritten by variables set through these
 * file includes. Any of those holding openpragh information related
 * to the page being included/viewed
 */
foreach (glob(CONFIG . "/opengraph/*.php") as $filename)
  include_once $filename;

$og_include = <<<TEXT
  <meta property="og:url" content="$og->url">
  <meta property="og:type" content="website">
  <meta property="og:title" content="$og->title">
  <meta property="og:description" content="$og->desc">
  <meta property="og:image" content="$og->image">
TEXT;

$twitter_og_include = <<<TEXT
  <meta name="twitter:card" content="summary_large_image">
  <meta property="twitter:domain" content="$og->app">
  <meta property="twitter:url" content="$og->url">
  <meta name="twitter:title" content="$og->title">
  <meta name="twitter:description" content="$og->desc">
  <meta name="twitter:image" content="$og->image">
TEXT;
