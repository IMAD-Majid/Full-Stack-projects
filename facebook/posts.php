<?php
$userid = $_COOKIE["userid"];
include "connect.php";

$targetPost = '';
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if (isset($_POST["read_post"])){
		session_start();
		$_SESSION["postid"] = $_POST["postid"];
		header("Location: comments.php");
	} elseif(isset($_POST["delete_post"])){
		$postid = $_POST["postid"];
		$sql = "delete from facebook.posts
		where posts.postid = '$postid';";
		executeQuery($sql);
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
				<?php include "nav generator.php"; generateNav(['u','m','h']) ?>
            </nav>
            <form id="search-container" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">                <input type="text" placeholder="To search" value="<?php echo $targetPost ?>" name="to_search">
				<input type="submit" value="Search" name="search_for">
            </form>
            <hr>
            <div id="posts-container">
                <ul>
<?php
// include "notifications.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if (isset($_POST["search_for"])){
		$targetPost = $_POST["to_search"];
	}
}

$sql = "select postid, createdat, text
from facebook.posts
inner join facebook.expressions
on
posts.expressionid = expressions.expressionid
where expressions.userid = '$userid'";

if ($targetPost != ''){
	$sql .= "\n and expressions.text like '%$targetPost%'";
	echo "Results for: " . $targetPost;
}

$sql .= "\n order by expressions.createdat desc;";


$results = executeQuery($sql);
while ($row = $results->fetch_assoc()){
	$postid = $row["postid"];
	$notifs = getCommentsNotes($postid);

	$postdate = (new DateTime($row["createdat"]))->format("Y-m-d");
	$posttext = $row["text"];
	echo "<li>
		<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
			<input type='hidden' value='$postid' name='postid'>
			<div class='metadata'>
				<span></span>
				<input readonly class='date' value='$postdate'>
			</div>
			<textarea readonly>$posttext</textarea>
			<input type='submit' class='anchor' value='Comments$notifs' name='read_post'>
			<input class='danger' type='submit' value='Delete' name='delete_post'>
		</form>
	</li>";
}
?>
                </ul>
</html>
