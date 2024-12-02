<?php 
	
	session_start();
	echo "Dump REQ:";
	//var_dump($_REQUEST);
	unset($_SESSION['name']);
	unset($_SESSION['devices']);
	unset($_SESSION['timezone']);
	unset($_SESSION['Username']);
	unset($_SESSION['accountperm']);
	session_unset();
	session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);

	echo "Session halted. Halt Code: (should be null)".(isset ($_SESSION['Username']));
	if(isset ($_SESSION['Username']))
		echo "WARN: Username ".$_SESSION['Username'];
	echo "<br>VAR_DUMP DEBUG CONTROL: ";

	var_dump($_SESSION);
?>