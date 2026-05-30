<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Bruder\Heiakim\Job\CreateDefaultProfile;
use Bruder\Heiakim\Job\Mailing\BirthdayWishes;
use Bruder\Heiakim\Job\Mailing\LongTimeNoSee;
use Bruder\Heiakim\Job\Mailing\PremiumEnds;
use Bruder\Heiakim\Job\RedisCacheLeaderboardDevelopment;
use Bruder\Heiakim\Job\RemoveOpenOrders;
use Bruder\Heiakim\Job\SaveCurrentRanks;
use Bruder\Heiakim\Job\RemovePremium;
use Bruder\Heiakim\Job\RestrictFrozenAccounts;

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,, OCCASIONAL JOBS ,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */

/**
 * Initialize database connection.
 */
new Bruder\Database\Database;

// (new CreateDefaultProfile)->execute();

/**
 * Save rankings for profile graph.
 */
(new SaveCurrentRanks)->execute();

/**
 * Update Premium+ roles on expiration.
 */
(new RemovePremium)->execute();

/**
 * Restrict accounts that are frozen after the timehas run up.
 * It's set as $interval inside the class.
 */
(new RestrictFrozenAccounts)->execute("+12 hours");

/**
 * Remove all open orders older than one hour.
 */
(new RemoveOpenOrders)->execute("+1 week");

/**
 * Save leaderboard rank development. Interval is in class.
 */
(new RedisCacheLeaderboardDevelopment)->execute("+1 week");

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,, MAILINGS ,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */

/**
 * Remind inactive users.
 */
(new LongTimeNoSee)->execute("+1 day");

/**
 * Remind Premium+ will end soon.
 */
(new PremiumEnds)->execute();

/**
 * Wish happy birthday!
 */
(new BirthdayWishes)->execute();

exit;
