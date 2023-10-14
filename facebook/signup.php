<!DOCTYPE html>
<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$userid = test_input($_POST["userid"]);
	$email = test_input($_POST["email"]);
	$password = test_input($_POST["password"]);
	$passwordconf = test_input($_POST["passwordconf"]);
	// exception handling
	if ($userid != '' && $password != '' && $password == $passwordconf){
		$sql = "select userid from facebook.users where
		userid = '$userid'";
		$results = executeQuery($sql);
		if ($results->num_rows == 0){
			$sql = "insert into facebook.users
			(userid, userpassword)
			values
			('$userid', '$password');";
			executeQuery($sql);
			setcookie("userid", $userid, time() + 365*24*60*60);
			// go to home
			header("Location: home.php");
		}
	}
} else{
	// define variables and set to empty values
	$userid = $email = $password = $passwordconf = "";
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
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
                            <label>Email</label>
                        </td>
                        <td>
                            <input type="text" name="email" value="<?php echo $email ?>">
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
                    <tr>
                        <td>
                            <label>Password Confirmation</label>
                        </td>
                        <td>
                            <input type="text" name="passwordconf" value="<?php echo $passwordconf ?>">
                        </td>
                    </tr>
                </table>
                <input type="submit" value="Sign up">
            </form>
            <a href="login.php">Log in</a>
        </div>
    </body>
</html>
