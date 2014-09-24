<?php
if (!defined('IN_KKFRAME')) exit('Access Denied');

class ccsmtp extends mailer {
	var $id = 'ccsmtp';
	var $name = 'CC SMTP发件代理';
	var $description = 'CC|贴吧云签到平台提供发件';
	var $config = array(
		array('SMTP服务器', 'host', '', ''),
		array('SMTP邮箱', 'mail', '', '', 'email'),
		array('SMTP用户名', 'user', '', '', 'email'),
		array('SMTP密码', 'pass', '', '', 'password'),
		array('SMTP发件人名称', 'fromname', '', ''),
		array('API地址', 'agentapi', '', 'http://api.lovecc.ml'),
		);

	function isAvailable() {
		return true;
	}

	function post($url, $content) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	function send($mail) {
		$data = array('to' => $mail -> address,
			'title' => $mail -> subject,
			'content' => $mail -> message,
			'host' => $this -> _get_setting('host'),
			'address' => $this -> _get_setting('mail'),
			'user' => $this -> _get_setting('user'),
			'pass' => $this -> _get_setting('pass'),
			'fromname' => $this -> _get_setting('fromname'),
			);
		$agentapi = $this -> _get_setting('agentapi');
		$sendresult = json_decode($this -> post($agentapi, $data), true);
		if ($sendresult['err_no']==0) return true;
		return false;
	}

}

?>