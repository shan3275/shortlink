<?php
	session_start();
	if(!$_SESSION['user']){
		header('Location: '. 'login.php');
		die();
	}
	$shortId = $_GET['short'];
?>
<html>
<head>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>
<style>
	.disabled{
		background-color: #DCDCDC;
	}
	.enabled{
		background-color: #1B3987;
	}
	.url{
		color: black;
		width: 400px;
	}
	button{
		width: 124px;
		height: 60px;
		border: none;
	}
	button.act{
		width: 166px;
	}
	button.delete{
		background-color: #FF4343;
	}
	table{
		margin: auto;
		border-spacing: 0 18px;
	}
</style>
<body>
<?php include('./header.html'); ?>
<div class="wmax ac">
	<br/>
	<table id="links"><thead>
		<tr>
			<td>短链接</td>
		</tr>
		<tr>
			<td><label id="short" style="font-size:150%"><?php echo empty($shortId) ? '保存后生成' : $shortId  ?></label></td>
		</tr>
		<tr>
			<td class="al">原网址</td>
			<th></th>
		</tr>
	</thead><tbody>
	</tbody></table>
	<button class="act cmd" onclick="addRow('', false)">增加</button>
	<button class="act" id="save" onclick="save()" disabled="disabled" style="background-color:#16BA26;margin:20px 300px 0 20px">保存</button>
</div>
<script>
	var shortId = '<?php echo $shortId ?>';
	function checkInput(){
		var links = $('#links').find('input.link');
		if(links.length == 0){
			$('#save').attr('disabled', 'disabled');
			return;
		}
		for(var i=0;i<links.length;i++){
			if(!links.eq(i).val().trim()){
				$('#save').attr('disabled', 'disabled');
				return;
			}
		}
		$('#save').removeAttr('disabled');
	}
	function addRow(url, enabled){
		$('#links > tbody:last-child').append(`<tr>
			<td class="url"><input class="wmax link" type="text" value="` + url + `" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()"></td>
			<td>
				<button class="` + (enabled ? `enabled` : 'disabled') + `" style="margin-left:15px" onclick="enable(this)">有效</button>
				<button class="delete" onclick="del(this)" style="margin-left:15px">删除</button>
			</td>
		</tr>`);
		checkInput();
	}
	function getLinks(){
		if(shortId){
			$.post('dbo.php', {
				action: 'links',
				short: shortId
			}, links => {
				$('#links > tbody > tr').remove();
				links.forEach(link => {
					addRow(link.url, link.enabled);
				})
			}, 'JSON');
		}else{
			addRow('', true);
		}
	}
	function del(obj){
		$(obj).closest('tr').remove();
		checkInput();
	}
	function enable(obj){
		$('#links').find('button.enabled').removeClass('enabled').addClass('disabled');
		$(obj).removeClass('disaled').addClass('enabled');
	}
	function save(){
		var inputs = $('#links').find('input[type=text]');
		var links = [];
		for(var i=0;i<inputs.length;i++){
			var input = inputs.eq(i);
			var link = {url: input.val()};
			if(input.closest('tr').find('button').hasClass('enabled')){
				link.enabled = true;
			}
			links.push(link);
		}
		var postval = {
			action: 'save',
			user: '<?php echo $_SESSION['user'] ?>',
			links: links
		};
		if(shortId){
			postval.short = shortId;
		}
		$.post('dbo.php', postval, () => {
			window.location.href = 'usrlist.php';
		});
	}
	getLinks();
</script>
</body>
</html>

<!--
	TODO: 二维码，有效期检查，UI，注册时付费，输入检查：非空，密码一致，姓名汉字，字数，身份证和手机号位数，密码位数
	网址格式检查（正则）	
-->