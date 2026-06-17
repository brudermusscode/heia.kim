<?php

if (LOGGED) {
  include __DIR__ . "/_page-navigator.php";
  include __DIR__ . "/_feed.php";
} else
  include __DIR__ . "/_landing.php";
