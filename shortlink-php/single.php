<?php
	include 'config.php';
	$userId = $_GET['user'];

	if($_GET['action'] == 'logout'){
		session_start();
		session_unset();
		session_destroy();
	}
?>
<html>
<head>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/qrcode.min.js"></script>
	<script type="text/javascript" src="js/clipboard.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>
<style>
	a{
		color: #0075FF;
	}
	a:visited{
		color: #0075FF;
	}
</style>
<body>
<?php include('./header.html'); ?>
<div class="ac">
	<div class="title">最稳定的动态短链接生成器</div>
	<div style="margin:24px;opacity:0.5">粘贴要缩短的网址开始使用短链接生成服务</div>
	<button class="big selected">静态短链接</button>
	<button class="big default" onclick="goMulti()">动态短链接</button><br/>
	<div style="width:80vw;margin:35px 10vw" class="al">
		有效期: 
		<select id="exp" style="margin-left:10px" onclick="exp()">
			<option value="7">一星期(7天)</option>
			<option value="30">一个月(30天)</option>
			<option value="90">三个月(90天)</option>
			<option value="180">六个月(180天)</option>
			<option value="360">一年(360天)</option>
			<option value="-">永久</option>
		</select>
		<div class="wmax" style="margin-top:15px">
			<input type="text" placeholder="请输入网址" id="url" style="width:60vw;height:60px" oninput="checkInput()" onchange="checkInput()" onpropertychange="checkInput()" />
			<button id="gen" onclick="genShort()" class="big cmd" style="width:17vw;margin-left:1.4vw" disabled="disabled">一键生成</button>
		</div>
		<div id="result" class="wmax hide" style="border-radius:8px;background-color:white;margin-top:15px">
			<div style="height:64px;border-bottom:solid 1px lightgray;color:#0075FF;padding-left:15px">
				<span style="line-height:64px">短链接：<a id="short" target="_blank"></a></span>
				<button id="copy" class="small" data-clipboard-action="copy" data-clipboard-target="#short" style="float:right;width:88px;margin:10px">复制</button>
			</div>
			<div class="wmax ac">
				<div id="qrcode" style="width:128px;height:128px;margin:40px auto 20px"></div><br/>
				<a id="download" download="qrcode.png"><button class="small" style="width:156px;margin:0 0 40px" onclick="download()">下载二维码</button></a>
			</div>
		</div>
	</div>
</div>
<script>
	function exp(){
		if($('#exp').val() == '-'){
			window.location.href = 'usrlist.php';
		}
	}
	function checkInput(){
		if(!$('#url').val().trim()){
			$('#gen').attr('disabled', 'disabled');
		}else{
			$('#gen').removeAttr('disabled');
		}
	}
	function genShort(){
		$.post('dbo.php', {
			action: 'single',
			url: $('#url').val(),
			exp: $('#exp').val()
		}, ret => {
			$('#short').text('<?php echo $site ?>/' + ret.short).attr('href', '<?php echo $site ?>/' + ret.short);
			qrcode.makeCode('<?php echo $site ?>/' + ret.short);
			$('#download').attr('download', ret.short + '.png');
			$('#result').show();
		}, 'JSON');
	}
	function goMulti(){
		window.location.href = 'usrlist.php';
	}
	function download(){
		$('#download').attr('href', $('#qrcode').find('img').attr('src'));
	}
	new ClipboardJS('#copy');
	var qrcode = new QRCode($("#qrcode")[0], {
		width : 128,
		height : 128
	});
</script>
</body>
</html>
