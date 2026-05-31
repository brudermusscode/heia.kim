<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Heiakim\Job\CreateDefaultProfile;
use Heiakim\Job\Mailing\BirthdayWishes;
use Heiakim\Job\Mailing\LongTimeNoSee;
use Heiakim\Job\Mailing\PremiumEnds;
use Heiakim\Job\RedisCacheLeaderboardDevelopment;
use Heiakim\Job\RemoveOpenOrders;
use Heiakim\Job\SaveCurrentRanks;
use Heiakim\Job\RemovePremium;
use Heiakim\Job\RestrictFrozenAccounts;

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,, OCCASIONAL JOBS ,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */

/**
 * Initialize database connection.
 */
new Heiakim\Database\Database;

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
