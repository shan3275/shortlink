<?php
	include 'dbo.php';
	include 'config.php';
	date_default_timezone_set("PRC");

	/*
	RESTAPI is changed to nodejs
	
	if(substr($_SERVER['REQUEST_URI'], 0, 5) == '/api/'){
		$method = $_SERVER['REQUEST_METHOD'];
		$request = trim($_SERVER['REQUEST_URI']);
		$data = json_decode(file_get_contents('php://input'), true);

		if($method == 'POST'){
			if($request == '/api/shortlink'){
				if(empty($data['action']) || empty($data['short']) || empty($data['link'])){
					echo json_encode(['code' => 3, 'msg' => 'wrong parameter']);
					return;
				}
				if($data['action'] == 'enable'){
					$links = find($db, 'links', ['short' => $data['short'], 'url' => $data['link']]);
					$cnt = count($links);
					if($cnt == 0){
						echo json_encode(['code' => 100, 'msg' => 'link not found']);
					}else{
						update($db, 'links', ['short' => $data['short']], ['$unset' => ['enabled' => 1]], ['multi' => 1]);
						update($db, 'links', ['short' => $data['short'], 'url' => $data['link']], ['$set' => ['enabled' => true]]);
						update($db, 'shorts', ['_id' => $data['short']], ['$set' => ['url' => $data['link'], 'updated' => new MongoDB\BSON\UTCDateTime()]]);
						echo json_encode(['code' => 0, 'msg' => 'short link updated']);
					}
				}else{
					echo json_encode(['code' => 3, 'msg' => 'wrong parameter']);
				}
			}else{
				echo json_encode(['code' => 2, 'msg' => 'wrong api called']);
			}
		}else{
			echo json_encode(['code' => 1, 'msg' => 'please use post']);
		}
	}else{*/
		$shortId = substr($_SERVER['REQUEST_URI'], 1);
		$short = findOne($db, $dbname, 'shorts', ['_id' => $shortId, 'expired' => ['$in' => [false, null]]]);
		if(empty($short)){
			header('Location: expired.php');
			die();
			return;
		}
		$diff = date_diff($short->created->toDateTime(), new DateTime("now"));
		if(!empty($short->exp) && $diff->invert == 0 && $diff->days > intval($short->exp)){
			update($db, $dbname, 'shorts', ['_id' => $shortId], ['$set' => ['expired' => true]]);
			header('Location: expired.php');
			die();
			return;
		}

		$url = $short->url;
		if(substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://'){
			$url = 'http://' . $url;
		}
		//echo $url;
		header('Location: '. $url);
		die();
	//}
?>
