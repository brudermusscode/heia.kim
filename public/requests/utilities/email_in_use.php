<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\User;
use Heiakim\Validate\Validate;

if (empty($_POST['email'])) exit(0);

if (!Validate::mail($_POST['email']))
  exit(json_encode(0));

if (User::mail_in_use($_POST['email']))
  exit(json_encode(0));

exit(json_encode(1));
