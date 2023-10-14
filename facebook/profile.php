<?php
include "connect.php";

$userid = $_COOKIE["userid"];
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["update"])){
		$password = test_input($_POST["password"]);
		$passwordconf = test_input($_POST["passwordconf"]);
		// exception handling
		if ($password != '' && $password == $passwordconf){
			$sql = "update facebook.users
			set
			userpassword = '$password'
			where
			userid = '$userid';";
			$results = executeQuery($sql);
			header("Location: profile.php");
		}
	} else{
		setcookie("userid", '', time()-1);
		unset($_SESSION["friendid"]);
		header("Location: login.php");
	}
} else{
	// define variables and set to empty values
	$password = $passwordconf = "";
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
        </style>
    </head>
    <body>
        <div id="root-container">
            <nav>
				<?php include "nav generator.php"; generateNav(['h','m','p']) ?>
            </nav>
            <h1><?php echo $userid?></h1>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <table>
                    <tr>
                        <td>
                            <label>New Password</label>
                        </td>
                        <td>
                            <input type="text" name="password" value="<?php echo $password ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Password Confirmation</label>
                        </td>
                        <td>
                            <input type="text" name="passwordconf" value="<?php echo $passwordconf ?>">
                        </td>
                    </tr>
                </table>
                <input type="submit" value="Update" name="update">
                <input type="submit" class="danger" value="Log out" name="logout">
            </form>
        </div>
    </body>
</html>
