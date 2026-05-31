<?php

use Heiakim\Database\Redis;

$Redis = Redis::connect();

pdie($Redis->keys('*'));
