<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Apache + PHP File Sharing</title>
  </head>
  <body>
    <h1>Apache + PHP File Sharing</h1>
    <?php
	$linux_user = "antond";
	ini_set('display_errors',1);
	main();

	function main(){
		global $linux_user;
		session_start();	
		$user = (string)$_SESSION["user"];
		//Need to do session_start() anytime you want to pull from the session also
		
		$user_clean = htmlentities($user);
		print("<h2>$user_clean</h2>");

		
		$file_name = basename($_FILES["file"]["name"]);
		//Is space an invalid character? I'm just using the REGEX they gave us on the wiki
		if( !preg_match('/^[\w_\.\-]+$/', $file_name) ){
			print("<h4>Filename contains invalid characters!</h4>
				<form action='/~$linux_user/m2_group/user_dash.php' method='post'>
					<button type='submit'> Return to Dashboard </button>
				</form>");
			exit();
		}

		$file_destination = sprintf("/srv/m2_group_secure/$user/$file_name");

		//tmp_name is the name of the temporary location on the server

		$a =move_uploaded_file($_FILES['file']['tmp_name'], $file_destination);

		//ran sudo chown apache /home/antond/m2_group_secure to make it the owner of this directory, meaning it has read write access

	}
    ?>
  </body>
</html>


