<?php
$userid = $_COOKIE["userid"];
include "connect.php";

$targetPost = '';
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if (isset($_POST["create_post"])){
		$post_text = $_POST["post_text"];
		$curdate = date("YmdHis");

		$sql = "insert into facebook.expressions
		(userid, createdat, text)
		values
		('$userid', '$curdate', '$post_text')";
		executeQuery($sql);
		$sql = "insert into facebook.posts
		(expressionid)
		SELECT MAX(expressionid)
		FROM facebook.expressions
		WHERE expressions.userid = '$userid';";
		executeQuery($sql);
		header("Location: home.php");
	} elseif(isset($_POST["comment_on_post"])){
		session_start();
		$_SESSION["postid"] = $_POST["postid"];
		header("Location: comments.php");
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
				<?php include "nav generator.php"; generateNav(['u','m','p']) ?>
            </nav>
            <div id="post-entry">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <textarea name="post_text"></textarea>
                    <input type="submit" value="Post" name="create_post">
                </form>
            </div>
            <form id="search-container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <input type="text" placeholder="To search" value="<?php echo $targetPost ?>" name="to_search">
				<input type="submit" value="Search" name="search_for">
            </form>
            <hr>
            <div id="posts-container">
                <ul>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if (isset($_POST["search_for"])){
		$targetPost = $_POST["to_search"];
	}
}

$sql = "select postid, userid, createdat, text
from facebook.posts
inner join facebook.expressions
on
posts.expressionid = expressions.expressionid";

if ($targetPost != ''){
	$sql .= "\n where expressions.text like '%$targetPost%'";
	echo "Results for: " . $targetPost;
}

$sql .= "\n order by expressions.createdat desc;";

$results = executeQuery($sql);
while ($row = $results->fetch_assoc()){
	$postid = $row["postid"];
	$posterid = $row["userid"];
	$postdate = (new DateTime($row["createdat"]))->format("Y-m-d");
	$posttext = $row["text"];
	echo "<li>
		<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
			<input type='hidden' value='$postid' name='postid'>
			<div class='metadata'>
				<input type='text' readonly class='userid' value='$posterid'>
				<input type='text' readonly class='date' value='$postdate'>
			</div>
			<textarea readonly>$posttext</textarea>
			<input type='submit' class='anchor' value='Comment' name='comment_on_post'>
		</form>
	</li>";
}

// reaching
$sql = "insert ignore into facebook.reaches
(expressionid, userid)
select posts.expressionid, '$userid'
from facebook.posts
inner join facebook.expressions
on
expressions.expressionid = posts.expressionid
where
userid != '$userid'
";
executeQuery($sql);

?>
                </ul>
            </div>
        </div>
    </body>
</html>
