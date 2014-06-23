<?php

	$db = mysql_connect('localhost','root','') or die('Cannot connect to DB');
	mysql_select_db('ghobok',$db) or die('Cannot select DB');

	$command = (isset($_POST['command'])) ? $_POST['command'] : 'refresh';

	$filename = "D:\\mysql_log.log";
	
	switch ($command) {
		case 'start':
			mysql_query("SET global log_output = 'FILE';", $db) or die('Debile query 1.');
			mysql_query("SET global general_log_file='$filename';", $db) or die('Debile query 2.');
			mysql_query("SET global general_log = 1;", $db) or die('Debile query 3.');
			break;
		case 'stop':
			mysql_query("SET global general_log = 0;",$db);			
			break;
		case 'reset':
			unlink( $filename );
			break;
	}

?>
<html>
<body>
	<form action="myDebug.php" method="post">
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
