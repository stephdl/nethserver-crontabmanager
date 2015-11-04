================
CronTab Settings
================

The software utility Cron is a time-based job scheduler in Unix-like computer operating systems. People who set up and maintain software environments use cron to schedule jobs (commands or shell scripts) to run periodically at fixed times, dates, or intervals.

CRON expression (advanced settings)
===================================


minute hour dom month dow


* minute (0 - 59)
* hour (0 - 23)
* day of month (1 - 31)
* month (1 - 12)
* day of week (0 - 7) (0 or 7 is Sunday)


Common Schedule
===============

* @yearly  : Run once a year at midnight of January 1
* @monthly : Run once a month at midnight of the first day of the month
* @weekly  : Run once a week at midnight on Sunday morning
* @daily   : Run once a day at midnight
* @hourly  : Run once an hour at the beginning of the hour
* @reboot  : Run at startup

You Need to replace the five fields of cron command with one of these keywords.

Allowed special character
=========================

* Asterik(*) – Match all values in the field or any possible value.
* Hyphen(-) – To define range.
* Slash (/) – 1st field /10 meaning every ten minute or increment of range.
* Comma (,) – To separate items

Any lines that begin with a hash mark (#) are comments and are not processed.
