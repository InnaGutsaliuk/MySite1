<?php
if (!isset($_GET['module'])) {
	if (Core::$CONTROLLER == 'modules/admin') {
		$_GET['module'] = 'authorization';
	}
}
if (isset($_SESSION['user'])) {
	$res = DB::_() -> query("
		SELECT *
		FROM `users`
		WHERE `id` = '".$_SESSION['user']['id']."'
		LIMIT 1
	");
	$row = $res -> fetch_assoc();
	$res -> close();
	if ($row['active'] != 5) { 			// 5 - администратор
		unset($_SESSION['user']);
		session_destroy();
		header('Location: /cab/exit&access=del');
		exit();
	}
} elseif(isset($_COOKIE['id'])) {
	$res = DB::_() -> query("
		SELECT *
		FROM `users`
		WHERE `id` = '".(int)$_COOKIE['id']."'
		LIMIT 1
	");
	if($res -> num_rows) {
		$row = $res -> fetch_assoc();
		$res -> close();
		if(($row['hash'] == $_COOKIE['hash']) && ($row['ip'] == $_SERVER['REMOTE_ADDR']) && ($row['user_agent'] == $_SERVER['HTTP_USER_AGENT']) && ($row['active'] == 5)) {
			$_SESSION['user'] = $row;
		}
		else {
			setcookie('id','',time()-3600,'/');
			header('Location: /cab/exit&access=del');
			exit();
		}
	}
} else {
	header('Location: /cab/exit&access=del');
	exit();
}
