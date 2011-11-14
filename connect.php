<?

$db = mysql_connect('localhost', 'dbname', 'password') or die('Could not connect.');
if(!$db) 
	die("no db");
if(!mysql_select_db('dbname',$db)) //$db
 	die("No database selected.");
?>