<?php

use Bruder\Http\Request;

/**
 * @var Request
 */
$Request = new Request;

/**
 * @var ?string
 */
$file_name = filter_var(GET->file_name ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$file_path = __DIR__ . "/$file_name.php";

/**
 * Begin output buffer.
 */
ob_start();

if (file_exists($file_path)) :
  include $file_path;
else :
  include GET_CONTENT_NOTHING;
endif;

die($Request->success(data: ob_get_clean()));
