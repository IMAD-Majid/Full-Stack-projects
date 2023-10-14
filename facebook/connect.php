<?php

$conn = new mysqli("localhost", "root", "");

function executeQuery($sql){
	global $conn;
	return $conn->query($sql);
}

?>
