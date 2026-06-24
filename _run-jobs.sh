#!/bin/sh

/usr/bin/php tasks/run-jobs.php >>storage/logs/cron.log 2>&1
