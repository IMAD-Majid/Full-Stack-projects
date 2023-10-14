<?php
include "connect.php";

session_start();
$userid = $_COOKIE["userid"];
$postid = $_SESSION["postid"];

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if (isset($_POST["comment_on_post"])){
		$commtext = $_POST["commenttext"];
		$curdate = date("YmdHis");

		$sql = "insert into facebook.expressions
		(userid, createdat, text)
		values
		('$userid', '$curdate', '$commtext');";
		executeQuery($sql);
		$sql = "insert into facebook.comments
		(expressionid, postid)
		select max(expressionid), '$postid'
		from facebook.expressions
		where expressions.userid = '$userid';";
		executeQuery($sql);
		header("Location: comments.php");
	} elseif (isset($_POST["contact_poster"])){
		$_SESSION["friendid"] = $_POST["contact_poster"];
		header("Location: messages.php");
	}
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div id="root-container">
            <nav>
				<?php include "nav generator.php"; generateNav(['h']) ?>
            </nav>
<?php
$sql = "select userid from facebook.posts
inner join facebook.expressions
on
posts.expressionid = expressions.expressionid
where
postid = '$postid'
";
$row = executeQuery($sql)->fetch_assoc();
$posterid = $row["userid"];
if ($posterid != $userid){
	echo "
	<form method='post' action=". htmlspecialchars($_SERVER["PHP_SELF"]) .">
		<input class='contactuser' type='submit' value='$posterid' name='contact_poster'>
	</form>
	";
}

?>
            <div id="post-to-comment">
<?php
echo "<textarea readonly>";
$sql = "select text from facebook.expressions
where expressions.expressionid in
(select expressionid from facebook.posts
where postid = '$postid');";
$results = executeQuery($sql);
$row = $results->fetch_assoc();
echo $row["text"];
echo "</textarea>";
?>
            </div>
            <div id="comment-entry">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <textarea name="commenttext"></textarea>
                    <input type="submit" value="Comment" name="comment_on_post">
                </form>
            </div>
            <hr>
            <div id="comments-container">
                <ul>

<?php
$sql = "select userid, createdat, text
from facebook.comments
inner join facebook.expressions
on
comments.expressionid = expressions.expressionid
where comments.postid = '$postid'
order by expressions.createdat desc;";

$results = executeQuery($sql);
while ($row = $results->fetch_assoc()){
	$commerid = $row["userid"];
	$commdate = (new DateTime($row["createdat"]))->format("Y-m-d");
	$commtext = $row["text"];
	echo "<li>
		<div>
			<div class='metadata'>
				<input type='text' readonly class='userid' value='$commerid'>
				<input type='text' readonly class='date' value='$commdate'>
			</div>
			<textarea readonly>$commtext</textarea>
		</div>
	</li>";
}

// reaching
$sql = "insert ignore into facebook.reaches
(expressionid, userid)
select comments.expressionid, '$userid'
from facebook.comments
inner join facebook.expressions
on
expressions.expressionid = comments.expressionid
where
postid = '$postid'
and
expressions.userid != '$userid'
";
executeQuery($sql);

?>
                </ul>
            </div>
        </div>
    </body>
</html>
