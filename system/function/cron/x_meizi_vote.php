<?php
if(!defined('IN_KKFRAME')) exit();
require_once ROOT.'./plugins/x_meizi/core.php';
$date = date('Ymd', TIMESTAMP+900);
$count_a=DB::result_first("select COUNT(*) from x_meizi_log where date='$date' and status=0");
if($count_a){
	$nowid=getSetting('x_mz_nowid',true);
	if(!$nowid){
		$offset = rand(1,$count_a)-1;
		$nowid=DB::result_first("select id from x_meizi_log where date='$date' and status=0 limit $offset,1");
		saveSetting('x_mz_nowid', $nowid);
	}
	$meizi=DB::fetch_first("select * FROM x_meizi_a WHERE id='$nowid'");
	$count_b=DB::result_first("select COUNT(*) from x_meizi_b where voted=0 and islogin=0");
	for($num=0;$num<=25;$num++,$count_b--){
		if($count_b==0){
			DB::query("update x_meizi_log set status=1 WHERE id='{$nowid}' AND date='$date'");
			DB::query("update x_meizi_b set voted=0");
			saveSetting('x_mz_nowid', 0);
			break;
		}
		$offset = rand(1,$count_b)-1;
		$voter=DB::fetch_first("select * from x_meizi_b where voted=0 and islogin=0 limit $offset,1");
		list ( $status, $result )=x_meizi_vote ($meizi,$voter);
		if($status==1){
			DB::query("update x_meizi_log set success=success+1 WHERE id='{$meizi[id]}' AND date='$date'");
			DB::query("update x_meizi_a set statue='$result' WHERE id='{$meizi[id]}'");
		}else{
			DB::query("update x_meizi_log set failed=failed+1 WHERE id='{$meizi[id]}' AND date='$date'");
		}
		DB::query("update x_meizi_b set voted=1 WHERE id='{$voter[id]}'");
		sleep(1);
	}
}else{
	DB::query("update x_meizi_log set status=0");
	$nextrun=TIMESTAMP+4.5*3600;
	saveSetting ('x_mz_nextrun',$nextrun);
	DB::query("update cron set nextrun='$nextrun' where id='x_meizi_vote'");
}