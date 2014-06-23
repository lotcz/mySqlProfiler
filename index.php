<?php

	$db = mysql_connect('localhost','root','') or die('Cannot connect to DB');

	$command = (isset($_POST['command'])) ? $_POST['command'] : 'refresh';

	$filename = "D:\\mysql_log.log";
	
	switch ($command) {
		case 'start':
			mysql_query("SET global log_output = 'FILE';", $db);
			mysql_query("SET global general_log_file='$filename';", $db);
			mysql_query("SET global general_log = 1;", $db);
			break;
		case 'stop':
			mysql_query("SET global general_log = 0;",$db);
			break;
		case 'reset':
			$handle = fopen($filename, 'r+');
			ftruncate($handle, 0 );
			fclose($handle);
			break;
	}

?>
<html>
<body>
	<form method="post">
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
		</div>
	</form>
	
	<textarea style="width:100%;height:500px">
	<?php echo file_get_contents ( $filename ); ?>
	</textarea>
</body>
</html>
