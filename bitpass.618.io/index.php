<?php
/**
 * Index
 * 
 * @author panzhibiao@bitfund.pe
 * @since 2013-08
 * @lastmodify 2013-11
 */
define('IN_SYSTEM', 1);
require_once dirname(__FILE__).'/common.php';

$sm = bitpass_insert_new_message();

$qr_msg = "bitpass:?sm=".rawurlencode($sm)."&cbk=".rawurlencode($_CFG['callback_url']);
$qr_img_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=".urlencode($qr_msg);

?>
<html>
<head>
<title>bitpass demo page</title>
</head>

<body>

<table border=1>
	<thead>
		<tr>
			<th>Source Message Text</th>
			<th>QR Code</th>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td>
			<textarea rows=10><?php echo $qr_msg;?></textarea>
			<ul>
				<li>message: <code><?php echo htmlspecialchars($sm);?></code></li>
				<li>callback url: <code><?php echo htmlspecialchars($_CFG['callback_url']);?></code></li>
			</ul>
		</td>
		<td>
		  <img style="width: 250px;height:250px;" src="<?php echo htmlspecialchars($qr_img_url);?>" />
		</td>
	</tr>
	</tbody>
</table>

<div id="verify_status">verify_status: <span></span></div>

<hr/>
<h3>Help</h3>
<ul>
	<li>refresh page to get new source message</li>
	<li><a href="https://github.com/dearcoin/bitpass-demo">bitpass@Github</a>
</ul>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script>
var sm = "<?php echo $sm;?>";
$(document).ready(function() {
	setInterval(function() {
		$("#verify_status span").html('checking...');
		$.get("trylogin.php", {message: sm}, function(data) {
			$("#verify_status span").html(data);
		});
	}, 2000);
}); 
</script>

</body>
</html>
