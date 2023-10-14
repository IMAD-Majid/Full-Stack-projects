<?php
include "connect.php";
$userid = $_COOKIE["userid"];

session_start();
if (isset($_SESSION["friendid"])){
	$friendid = $_SESSION["friendid"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	echo $_POST;
	if (isset($_POST["select_friend"])){
		$_SESSION["friendid"] = $_POST["select_friend"];
		header("Location: messages.php");
	} elseif (isset($_POST["deselect_friend"])){
		unset($_SESSION["friendid"]);
		header("Location: messages.php");
	} elseif (isset($_POST["send_message"])){
		$curdate = date("YmdHis");
		$msgtext = $_POST["message_text"];
		$sql = "insert into facebook.expressions
		(userid, createdat, text)
		values
		('$userid', '$curdate', '$msgtext')";
		executeQuery($sql);
		$sql = "insert into facebook.messages
		(expressionid, friendid)
		SELECT MAX(expressionid), '$friendid'
		FROM facebook.expressions
		WHERE expressions.userid = '$userid';";
		executeQuery($sql);
		header("Location: messages.php");
	}
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
        <style>
            #chat{
                display: flex;
                min-height: 25em;
            }
            #friends-container{
				width:100%;
            }
            #friends-container ul{
                list-style-type:none;
                padding:0;
            }
            #friends-container ul li{
                margin:1em 0;
            }
            #messages{
                width: 100%;
                padding:0 2em;
                box-sizing:border-box;
            }
            div#messages input, button{
                display:block;
                width:100%;
            }
            #messages-container{
				min-height:16em;
            }
            #messages-container p{
                padding:0.75em 1.5em;
                width: max-content;
				margin:0.25em 0;
            }
            #messages-container p span.message-date{
                display:block;
                font-size: 80%;
                color:grey;
            }
            #messages-container p.sent, #messages-container p.sent span{
                background-color: #242;
                margin-left:auto;
            }
            #messages-container p.received, #messages-container p.received span{
                background-color: #224;
                margin-right:auto;
            }
			form input[type=submit].disconnect{
				background-color:grey;
			}
        </style>
    </head>
    <body>
        <div id="root-container">
            <nav>
				<?php include "nav generator.php"; generateNav(['u','h','p']) ?>
            </nav>
            <div id="chat">
<?php
if (!isset($friendid)){
	echo "<div id='friends-container'>";
	echo "<ul>";

	// include "notifications.php";
	$sql = "
SELECT
	user_id,
    MAX(createdat) AS latest_message_date,
    text AS latest_message
FROM (
    SELECT
        e.userid AS user_id,
        e.createdat,
        e.text
    FROM
        facebook.expressions AS e
    INNER JOIN
        facebook.messages AS m
        ON e.expressionid = m.expressionid
    WHERE
        m.friendid = '$userid'
    UNION
    SELECT
        m.friendid AS user_id,
        e.createdat,
        e.text
    FROM
        facebook.expressions AS e
    INNER JOIN
        facebook.messages AS m
        ON e.expressionid = m.expressionid
    WHERE
        e.userid = 'userid'
) AS combinedResults
GROUP BY
    user_id
ORDER BY
    latest_message_date ASC;
";

	$results = executeQuery($sql);
	while ($row = $results->fetch_assoc()){
		$friendid = $row["user_id"];
		$notifs = getMessagesNotes($friendid);
		
		$friendtext = $row["latest_message"];

		echo "<li>
			<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
				<input class='contactuser' type='submit' value='$friendid' name='select_friend' readonly>
				<span class='friend-notifications'>$notifs</span>
				<p class='last-message'>$friendtext</p>
			</form>
		</li>";
	}

	echo "</ul>";
	echo "</div>";
	
} else{
	echo "<div id='messages'>";
	echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
		<input class='disconnect' type='submit' value='$friendid' name='deselect_friend' readonly>
	</form>";
	echo "<div id='messages-container'>";

	$sql = "select userid, createdat, text
	from facebook.messages
	inner join facebook.expressions
	on
	messages.expressionid = expressions.expressionid
	where
	(
		friendid = '$userid'
		or
		userid = '$userid'
	)
	and
	(
		friendid = '$friendid'
		or
		userid = '$friendid'
	)
	order by expressions.createdat asc;";

	$results = executeQuery($sql);
	while ($row = $results->fetch_assoc()){
		$msgerid = $row["userid"];

		$msgtype = "received";
		if ($msgerid == $userid){
			$msgtype = "sent";
		}

		$msgdate = (new DateTime($row["createdat"]))->format("H:i");
		$msgtext = $row["text"];

		echo "<p class='$msgtype'>
		$msgtext

		<span class='message-date'>
		$msgdate
		</span>

		</p>";
	}

	// reaching
	$sql = "insert ignore into facebook.reaches
	(expressionid, userid)
	select messages.expressionid, '$userid'
	from facebook.messages
	inner join facebook.expressions
	on
	expressions.expressionid = messages.expressionid
	where
	userid = '$friendid'
	and
	friendid = '$userid'
	";
	executeQuery($sql);

	echo "
	</div>
		<form method='post' action=". htmlspecialchars($_SERVER["PHP_SELF"]) .">
			<input type='text' id='message-entry' name='message_text'>
			<input type='submit' value='Envoyer' name='send_message'>
		</form>
	</div>";
}
?>
            </div>
        </div>
    </body>
</html>
