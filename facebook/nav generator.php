<?php
include "notifications.php";

function generateNav($sites){
	echo "<ul>";
	foreach ($sites as $siteLetter){
		switch($siteLetter){
			case 'u':
				echo "<li><a href='profile.php'>Profile</a></li>";
				break;
			case 'h':
				echo "<li><a href='home.php'>Home". getPostsNotes() ."</a></li>";
				break;
			case 'm':
				echo "<li><a href='messages.php'>Messages". getMessagesNotes() ."</a></li>";
				break;
			case 'p':
				echo "<li><a href='posts.php'>Posts". getCommentsNotes() ."</a></li>";
				break;
		}
	}
	echo "</ul>";
}
?>
