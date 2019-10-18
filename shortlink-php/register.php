<?php
	$userId = $_GET['user'];
?>
<html>
<head>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>
<style>
	input{
		width: 40vw;
	}
	td{
		height: 70px;
	}
</style>
<body>
<?php include('./header.html'); ?>
<div class="wmax ac">
	<div style="font-size:150%;margin:50px">新用户注册</div>
	<table style="margin:auto"><tr>
		<td>用户名：</td>
		<td><input type="text" id="usr" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /></td>
	</tr><tr>
		<td>密码：</td>
		<td><input type="password" id="pwd" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /></td>
	</tr><tr>
		<td>确认密码：</td>
		<td><input type="password" id="pwd2" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /></td>
<!--	</tr><tr>
		<td>真实姓名：</td>
		<td><input type="text" id="name" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /></td>
	</tr><tr>
		<td>身份证号：</td>
		<td><input type="text" id="idc" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /></td>
-->	</tr><tr>
		<td>手机号：</td>
		<td><input type="text" id="phone" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" /></td>
	</tr><tr></table>
	<br/>
	<button id="reg" class="big cmd" style="width:300px;margin:15px" disabled="disabled" onclick="register()">注册</button><br/>
	已有账户？<a href="login.php">立即登录</a><br/>
	<label style="color:red" id="error"></label><br/><br/><br/>
</div>
<script>
	function checkInput(){
		if(!$('#usr').val().trim() || !$('#pwd').val().trim() || !$('#pwd2').val().trim() ||
			/*!$('#name').val().trim() || !$('#idc').val().trim() ||*/ !$('#phone').val().trim()
		){
			$('#reg').attr('disabled', 'disabled');
		}else{
			$('#reg').removeAttr('disabled');
		}
	}
	function register(){
		if($('#pwd').val() != $('#pwd2').val()){
			$('#error').text('密码和确认密码不一致');
			return;
		}
		if($('#pwd').val().length < 6){
			$('#error').text('密码至少为6位');
			return;
		}
		// if(!/^[\u4e00-\u9fa5]{2,6}$/.test($('#name').val().trim())){
		// 	$('#error').text('请输入您的真实姓名');
		// 	return;
		// }
        // if(!/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test($('#idc').val().trim())){
		// 	$('#error').text('请输入正确的身份证号');
		// 	return;
		// }
		if(!/^\d{11}$/.test($('#phone').val().trim())){
			$('#error').text('请输入正确的手机号');
			return;
		}
		

		$.post('dbo.php', {
			action: 'register',
			user: $('#usr').val(),
			pass: $('#pwd').val(),
			//name: $('#name').val(),
			//idc: $('#idc').val(),
			phone: $('#phone').val()
		}, ret => {
			if(ret.error){
				$('#error').text(ret.error.msg);
			}else{
				window.location.href = 'login.php';
			}
		}, 'JSON');
	}
</script>
</body>

</html>