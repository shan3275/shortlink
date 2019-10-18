<?php
	include 'config.php';
	session_start();
	if(empty($_SESSION['user'])){
		header('Location: '. 'login.php');
		die();
	}
	$userId = $_GET['user'];
?>
<html>
<head>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/clipboard.min.js"></script>
	<script type="text/javascript" src="js/qrcode.min.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>
<style>
	.short a:visited{
		color: #0075FF;
	}
	.short a{
		color: #0075FF;
	}
	.url a{
		color: darkgray;
	}
	.url a:visited{
		color: darkgray;
	}
	.short{
		color: #0075FF;
		width: 320px;
		padding-right: 0;
	}
	.url{
		color: black;
		width: 400px;
	}
	button{
		width: 88px;
		height: 44px;
	}
	button.delete{
		background-color: #FF4343;
		border: none;
	}
	tbody > tr{
		height: 76px;
	}
	table{
		margin: auto;
		border-spacing: 0px 18px;
	}
	table tr th,table tr td {
		border-right: none;
		padding: 15px;
	}
	table tr td {
		background-color: white;
	}
	td.short{
		border-top-left-radius: 8px;
		border-bottom-left-radius: 8px;
	}
	td.action{
		border-top-right-radius: 8px;
		border-bottom-right-radius: 8px;
	}
	img{
		width: 24px;
		height: 24px;
		margin-left: 5px;
	}
</style>
<body>
<?php include('./header.html'); ?>
<div class="wmax ac" style="margin-top:20px">
	<table id="usrlist" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="3" class="al">短链接</th>
				<th colspan="3" class="al">原网址(有效)</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<button onclick="newShort()" class="big cmd" style="width:160px;margin:30px 800px 0 0">新建</button>
</div>
<script>
	function newShort(){
		window.location.href = 'usredit.php';
	}
	function getShorts(){
		$.post('dbo.php', {
			action: 'shorts',
			user: '<?php echo $_SESSION['user'] ?>'
		}, shorts => {
			shorts.forEach(short => {
				var otherLinks = '';
				short.links.forEach(link => {
					if(link.url == short.url) return;
					otherLinks += `<br/><a target="_blank" href="` + (link.url.startsWith('http') ? link.url : 'http://' + link.url) + `">` + link.url + `</a>`;
				});
				var tr = $(`<tr short=` + short._id + `> 
					<td class="short">
						<a class="stlk" target="_blank" href="<?php echo $site ?>/` + short._id + `"><?php echo $site ?>/` + short._id + `</a>
					</td><td style="padding:0">
						<img class="copy" src="img/copy.png" data-clipboard-action="copy" />
					</td><td style="padding:0 20px 0 0">
						<div class="qrcode" style="display:none;width:128px;height:128px"></div>
						<a class="download" download="` + short._id + `.png"><img src="img/qrcode.png" onclick="download(this)" /></a>
					</td>
					<td class="url">
						<a class="enabled" target="_blank" style="font-weight:bold" href="` + (short.url.startsWith('http') ? short.url : 'http://' + short.url) + `">` + short.url + `</a>`
						+ otherLinks +
					`</td>
					<td>
						<a href="usredit.php?short=` + short._id + `"><button class="edit cmd">编辑</button></a>
					</td><td class="action">
						<button class="delete" onclick="delShort(this)" style="margin-left:15px">删除</button>
					</td>
				</tr>`);
				$('#usrlist > tbody:last-child').append(tr);
				new ClipboardJS('.copy', {
					target: function(trigger) {
						return $(trigger).closest('tr').find('a.stlk')[0];
					}
				});
				console.log('tr', tr)

				var qrcode = new QRCode(tr.find('div.qrcode')[0], {
					width : 128,
					height : 128
				});
				qrcode.makeCode(tr.find('a.stlk').attr('href'));
			})
		}, 'JSON');
	}
	function delShort(obj){
		var tr = $(obj).closest('tr');
		var shortId = tr.attr('short');
		var url = tr.find('a.enabled').text();
		if(confirm('确认要删除指向「' + url + '」的短链接吗？（短链接下所有的网址都将被删除！）')){
			$.post('dbo.php', {
				action: 'delete',
				short: shortId
			}, () => {
				tr.remove();
			});
		}
	}
	function download(obj){
		var tr = $(obj).closest('tr');
		tr.find('a.download').attr('href', tr.find('div.qrcode').find('img').attr('src'));
	}
	getShorts();
</script>
</body>

</html>
