<?php	
function getMessagesNotes($fromFriend=''){
	global $userid;
	$sql = "
	select *
	from facebook.messages
	inner join facebook.expressions
	on
	messages.expressionid = expressions.expressionid
	where
	friendid = '$userid'
	and
	messages.expressionid not in
	(select reaches.expressionid from facebook.reaches where reaches.userid = '$userid')";
	
	if ($fromFriend){
		$sql .= "\n and expressions.userid = '$fromFriend'";
	}
	
	$notes = executeQuery($sql)->num_rows;
	if ($notes){
		return " (+$notes)";
	} else{
		return '';
	}
}

function getCommentsNotes($fromPost=-1){
	global $userid;
	$sql = "
	select *
	from facebook.comments
	where
	postid";

	if ($fromPost != -1){
		$sql .= " = '$fromPost'";
	} else{
		$sql .= "
		in
		(select postid from facebook.posts where posts.expressionid in
		(select expressions.expressionid from facebook.expressions where userid = '$userid'))";
	}
	$sql .="
	and
	comments.expressionid not in
	(select reaches.expressionid from facebook.reaches where reaches.userid = '$userid')";

	$notes = executeQuery($sql)->num_rows;
	if ($notes){
		return " (+$notes)";
	} else{
		return '';
	}
}

function getPostsNotes(){
	global $userid;
	$sql = "
	select *
	from facebook.posts
	inner join facebook.expressions
	on
	posts.expressionid = expressions.expressionid
	where
	expressions.userid != '$userid'
	and
	posts.expressionid not in
	(select reaches.expressionid from facebook.reaches where reaches.userid = '$userid')";
	$notes = executeQuery($sql)->num_rows;
	if ($notes){
		return " (+$notes)";
	} else{
		return '';
	}
}
?>
