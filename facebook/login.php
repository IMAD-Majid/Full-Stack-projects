<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$userid = test_input($_POST["userid"]);
	$password = test_input($_POST["password"]);
	// exception handling
	$sql = "select userid from facebook.users where
	userid = '$userid' and userpassword = '$password';";
	$results = executeQuery($sql);
	if ($results->num_rows > 0){
		// go to home
		setcookie("userid", $userid, time() + 365*24*60*60);
		header("Location: home.php");
	}
} else{
	// define variables and set to empty values
	$userid = $password = "";
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
        <style>
            table{
                width:100%;
            }
            input{
                width:100%;
            }
            input[type=submit]{
                margin:2em 0;
            }
            a {
                color:cyan;
                font-size:90%;
            }
        </style>
    </head>
    <body>
        <div id="root-container">
            <nav>
            </nav>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <table>
                    <tr>
                        <td>
                            <label>User ID</label>
                        </td>
                        <td>
                            <input type="text" name="userid" value="<?php echo $userid ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Password</label>
                        </td>
                        <td>
                            <input type="text" name="password" value="<?php echo $password ?>">
                        </td>
                    </tr>
                </table>
                <input type="submit" value="Log in">
            </form>
            <a href="signup.php">New account</a>
        </div>
    </body>
</html>
