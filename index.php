<?php

	$globals = [];
	require 'config.php';
	$filename = $globals['log_path'];	

	$command = (isset($_REQUEST['command'])) ? $_REQUEST['command'] : 'refresh';

	if ($command == 'live') {
		echo file_get_contents ( $filename );
		$handle = fopen($filename, 'r+');
		ftruncate($handle, 0 );
		fclose($handle);
		die();
	} else {
		$db = mysql_connect($globals['db_host'],$globals['db_login'],$globals['db_password']) or die('Cannot connect to DB');	
		switch ($command) {
			case 'start':			
				mysql_query("SET global log_output = 'FILE';", $db);
				mysql_query("SET global general_log_file='" . $filename . "';", $db);
				mysql_query("SET global general_log = 1;", $db);
				break;
			case 'stop':
				$db = mysql_connect($globals['db_host'],$globals['db_login'],$globals['db_password']) or die('Cannot connect to DB');
				mysql_query("SET global general_log = 0;",$db);
				break;
			case 'reset':
				$handle = fopen($filename, 'r+');
				ftruncate($handle, 0 );
				fclose($handle);
				break;		
		}
	}

?>
<html>
<body>
	
	<script>
		
		var xmlhttp = new XMLHttpRequest();
		
		var myTimer, isLive = false;
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var el = document.getElementById('watch')
				el.innerHTML += xmlhttp.responseText;
				var elc = document.getElementById('container')
				elc.scrollTop = el.scrollHeight;
			}
		}
		
		function liv() {
			isLive = !(isLive);
			var el = document.getElementById('live');
			if (isLive) {
				myTimer = setInterval(_get,	500, true);				
				el.style.backgroundColor = 'red';
			} else {
				clearInterval(myTimer);
				el.style.backgroundColor = '';
			}
		}
		
		function _get() {
			xmlhttp.open('GET', '?command=live', true);			
			xmlhttp.send();
		}
		
		
	</script>
	
	<form method="post" style="">
		<div><b>Logging status: </b>
			<?php 
				$result = mysql_query("SHOW VARIABLES LIKE 'general_log%';", $db) or die('Debile query:  '. $query);
				while($row = mysql_fetch_assoc($result)) {
					echo $row['Variable_name'] . " = " . $row['Value'] . ", ";
				}
			?>
		</div>
		<div>
			<input type="submit" name="command" value="start" />
			<input type="submit" name="command" value="stop" />
			<input type="submit" name="command" value="refresh" />
			<input type="submit" name="command" value="reset" />
			<input id="live" type="button" name="command" value="LIVE!" onclick="javascript:liv();" />
		</div>
	</form>
	<div id="container" style="position:fixed;top:60px;left:10px;right:10px;bottom:10px;overflow:auto;border:solid 1px Black;">
		<pre id="watch"><?php echo file_get_contents ( $filename ); ?></pre>
	</div>
	
</body>
</html>
