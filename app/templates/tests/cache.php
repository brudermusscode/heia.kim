<?php

use Bruder\Database\Redis;

$Redis = Redis::connect();

p($Redis->keys('*'));
