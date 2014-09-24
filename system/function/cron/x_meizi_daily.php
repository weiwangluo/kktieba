<?php
if(!defined('IN_KKFRAME')) exit();
$date = date('Ymd', TIMESTAMP+900);
DB::query("alter table x_meizi_log CHANGE `date` `date` INT NOT NULL DEFAULT '{$date}'");
DB::query("insert ignore into x_meizi_log (id, uid) SELECT id, uid FROM x_meizi_a");
$delete_date = date('Ymd', TIMESTAMP - 86400*10);
DB::query("DELETE FROM x_meizi_log WHERE date<'$delete_date'");
$nextrun=getSetting('x_mz_nextrun');
DB::query("update cron set nextrun='$nextrun' where id='x_meizi_vote'");
define('CRON_FINISHED', true);