<?php
	$userId = $_GET['user'];
?>
<html>
<head>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>
<style>
</style>
<body>
<?php include('./header.html'); ?>
<div class="wmax ac">
	<div style="font-size:150%;margin:50px">用户登录</div>
	<input style="width:50vw" type="text" placeholder="请输入用户名" id="usr" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /><br/><br/>
	<input style="width:50vw" type="password" placeholder="请输入密码" id="pwd" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /><br/><br/>
	<button class="big cmd" style="width:300px;margin:15px" onclick="login()" id="login" disabled="disabled">登录</button><br/>
	没有账户？<a href="register.php">立即注册</a><br/>
	<label style="color:red" id="error"></label>
<script>
	function checkInput(){
		if(!$('#usr').val().trim() || !$('#pwd').val().trim()){
			$('#login').attr('disabled', 'disabled');
		}else{
			$('#login').removeAttr('disabled');
		}
	}
	function login(){
		$.post('dbo.php', {
			action: 'login',
			user: $('#usr').val(),
			pass: $('#pwd').val()
		}, ret => {
			if(ret.error){
				$('#error').text(ret.error.msg);
			}else{
				window.location.href = 'usrlist.php';
			}
		}, 'JSON');
	}	
</script>
</body>

</html>