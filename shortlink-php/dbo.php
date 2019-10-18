<?php
	include 'config.php';
	
	function find($db, $dbname, $table, $filter, $options = []){
		return $db->executeQuery("$dbname.$table", new MongoDB\Driver\Query($filter, $options))->toArray();
	}
	function findOne($db, $dbname, $table, $filter, $options = []){
		$rows = $db->executeQuery("$dbname.$table", new MongoDB\Driver\Query($filter, $options))->toArray();
		if(count($rows) > 0){
			return $rows[0];
		}
		return null;
	}
	function aggregate($db, $dbname, $table, $pipeline){
		return $db->executeCommand($dbname, new MongoDB\Driver\Command([
			'aggregate' => $table,
			'pipeline' => $pipeline
		]))->toArray()[0]->result;
	}
	function insert($db, $dbname, $table, $data){
		$bulk = new MongoDB\Driver\BulkWrite();
		$bulk->insert($data);
		$db->executeBulkWrite("$dbname.$table", $bulk);
	}
	function update($db, $dbname, $table, $query, $data){
		$bulk = new MongoDB\Driver\BulkWrite();
		$bulk->update($query, $data);
		return $db->executeBulkWrite("$dbname.$table", $bulk);
	}
	function remove($db, $dbname, $table, $query){
		$bulk = new MongoDB\Driver\BulkWrite();
		$bulk->delete($query);
		$db->executeBulkWrite("$dbname.$table", $bulk);
	}

	//$db = new MongoDB\Driver\Manager('mongodb://{user}:{pass}@{domain}:{port}/{dbname}');
	$db = new MongoDB\Driver\Manager('mongodb://localhost:27017');

	function postval($key){
		return isset($_POST[$key]) ? $_POST[$key] : null;
	}

	function randomstr($length){
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		$key = '';
		for($i=0;$i<$length;$i++) 
		{
			$key .= $pattern{mt_rand(0,62)};    //生成php随机数 
		} 
		return $key;
	} 
	function genShort($db, $dbname){
		while(true){
			$short = randomstr(6);
			$s = findOne($db, $dbname, 'shorts', ['_id' => $short]);
			if(empty($s)){
				return $short;
			}
		}
	}

	$now = new MongoDB\BSON\UTCDateTime(1000 * time());	
	$action = postval('action');
	if($action == 'shorts'){
		$user = postval('user');
		$shorts = find($db, $dbname, 'shorts', ['user' => $user]);
		foreach($shorts as $short){
			$short->links = find($db, $dbname, 'links', ['short' => $short->_id]);
		}
		echo json_encode($shorts);
	}else if($action == 'links'){
		$short = postval('short');
		$links = find($db, $dbname, 'links', ['short' => $short]);
		echo json_encode($links);
	}else if($action == 'delete'){
		$short = postval('short');
		remove($db, $dbname, 'links', ['short' => $short]);
		remove($db, $dbname, 'shorts', ['_id' => $short]);
	}else if($action == 'single'){
		$short = genShort($db, $dbname);
		$url = postval('url');
		$exp = postval('exp');
		insert($db, $dbname, 'shorts', ['_id' => $short, 'url' => $url, 'exp' => intval($exp), 'created' => $now]);
		echo json_encode(['short' => $short]);
	}else if($action == 'login'){
		$user = postval('user');
		$pass = postval('pass');
		$u = findOne($db, $dbname, 'users', ['_id' => $user, 'pass' => md5($pass . $passSuffix)]);
		if(!empty($u)){
			session_start();
			$_SESSION['user'] = $user;
			echo json_encode([]);
		}else{
			echo json_encode(['error' => ['msg' => '用户名或密码错']]);
			return;
		}
	}else if($action == 'register'){
		$user = postval('user');
		$pass = postval('pass');
		//$idc = postval('idc');
		//$name = postval('name');
		$phone = postval('phone');
		$u = findOne($db, $dbname, 'users', ['_id' => $user]);
		if(!empty($u)){
			echo json_encode(['error' => ['msg' => '用户名已存在']]);
			return;
		}
		$u = findOne($db, $dbname, 'users', ['phone' => $phone]);
		if(!empty($u)){
			echo json_encode(['error' => ['msg' => '该手机号已注册']]);
			return;
		}
		/*$u = findOne($db, 'users', ['idcard' => $idc]);
		if(!empty($u)){
			echo json_encode(['error' => ['msg' => '该身份证号已注册']]);
			return;
		}*/
		insert($db, $dbname, 'users', ['_id' => $user, 'pass' => md5($pass . $passSuffix), /*'name' => $name, 'idcard' => $idc,*/ 'phone' => $phone, 'created' => $now]);
		echo json_encode([]);
	}else if($action == 'save'){
		$short = postval('short');
		$user = postval('user');
		$links = postval('links');
		$new = false;
		if(empty($short)){
			$short = genShort($db, $dbname);
			$new = true;
		}else{
			remove($db, $dbname, 'links', ['short' => $short]);
		}
		$enabledUrl = '';
		foreach($links as $link){
			$val = ['_id' => new MongoDB\BSON\ObjectId(), 'short' => $short, 'url' => $link['url'], 'created' => $now];
			if(!empty($link['enabled'])){
				$val['enabled'] = true;
				$enabledUrl = $link['url'];
				if($new){
					insert($db, $dbname, 'shorts', ['_id' => $short, 'user' => $user, 'url' => $link['url'], 'created' => $now]);
				}else{
					update($db, $dbname, 'shorts', ['_id' => $short], ['$set' => ['url' => $link['url'], 'updated' => $now]]);
				}
			}
			insert($db, $dbname, 'links', $val);
		}
	}
?>
