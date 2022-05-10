<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Apache + PHP File Sharing</title>
  </head>
  <body>
    <h1>Apache + PHP File Sharing</h1>
    <?php

	// ini_set('upload_max_filesize', '10M');
	// ini_set('post_max_size', '10M');
	// ini_set('max_input_time', 300);
	// ini_set('max_execution_time', 300);
	
	$linux_user = "rnepal";
	main();

	function main(){
		global $linux_user;

		//If someone tries to access user_dash with no active session through the url
		//and there is no POST request (not being accessed from the login page)
		//They'll get a login error. This needs to come before all other code.

		session_start();
		
		$user = verify_session();
		if(!$user){
			session_destroy();
			exit();
		}

		//verify_session filters input
		
		$_SESSION["user"]=$user;

		$user_clean = htmlentities($user);
		print("<h2>$user_clean</h2>");
		print("<h4>Files</h4>");


		//you can basically map anything into the session array
		//and access it anywhere else in your code as long as that session is active
	
		display_files($user);
		
		//This HTML creates the form for file submission
		//this will send a post request to upload_file.php

		display_file_upload($user);


		if (strcmp($user,"ADMIN")==0){
			display_admin_panel();
			
		}

		display_logout_button();
		
	}
	
	function user_exists($user){
		$user_file_path = "/srv/m2_group_secure/users/users.txt";
		$user_file = fopen($user_file_path, "r");
		$in_list = false;

		if ($user_file == false){
			print("<p>Error! Apache couldn't read the users.txt file!</p>");

		}
		else {
			while (!feof($user_file)){
				$line = trim(fgets($user_file));
				if (strcmp($user, $line)==0){
					$in_list = true;
				}
			}
			
		}
		fclose($user_file);
		return $in_list;
	}

	function display_logout_button(){
		global $linux_user;
		print("<form action='/~$linux_user/m2_group/redirects/logout_successful.php' method='post'>
		<button type='submit'> Log out </button>
		</form>");
	}

	function display_file_upload($user){
		print("<h4>Upload New File</h4>");
		print("<form enctype='multipart/form-data' action='upload_file.php' method='post'>
			
			<input type='file' name='file'>
			<input type='submit' value='Upload'>
			
			</form>");
	}

	function display_files($user){
		global $linux_user;
		$all_files = scandir("/srv/m2_group_secure/$user");

		foreach ($all_files as $file){
			if (strcmp($file, ".")==0 || strcmp($file, "..")==0){

			}
			else{

				$file_clean = htmlentities($file);
				print("<p>$file_clean<br> </p>");

				//view buttons
				print("<form action='/~$linux_user/m2_group/view.php' target='_blank' method='post'>
				<input type='hidden' name='file' value='$file'>
					<button type='submit'> View </button>
				</form>");

				//delete buttons
				print("<form action='/~$linux_user/m2_group/delete.php'  method='post'>
					<button type='submit'> Delete </button>
					<input type='hidden' name='file' value='$file'>
				</form>");
				
				
			}
		}
	}

	function display_admin_panel(){
		global $linux_user;
		print("<h4>Users</h4>");


			$user_file_path = "/srv/m2_group_secure/users/users.txt";
			$user_file = fopen($user_file_path, "r");
		

			if ($user_file == false){
				print("<p>Error! Apache couldn't read this file!</p>");

			}
			else {
				while (!feof($user_file)){
					$line = trim(fgets($user_file));
					if (strcmp($line,'ADMIN')==0){
						$user = htmlentities($line);
						print("<label for='admin'> $user </label>");
					}
					else if (strcmp($line,"")==0 || strpos($line,"~")){
						//If line is empty, or this line has been marked as having a deleted user
					}
					else {
						print("
						<form action='/~$linux_user/m2_group/user_remove.php' 
						id='remove_button' name='remove_user' method='post'>
						<label for='remove_button'> $line </label>
							<button id ='remove_button' name='remove_user' value='$line'>Remove</button>
						</form>");
					}
				
				}
				
			}
			fclose($user_file);

			print("<h4> Add users </h4>
				<form action='/~$linux_user/m2_group/users_add.php' method='post'>
					<label>
					List Users, separate with commas:
					<input type='text' name='user_list' />
					</label>
					<input type='submit' value='Add' />
				</form>");

	}

	function verify_session(){
		global $linux_user;
		//Returns false if there's no valid session
		//Returns the user name if there is a valid sesison
		//Returns "" if theres a bug lol

		$rVal = "";

		if ((empty(@$_SESSION)) && (empty($_POST))){
			print("<h4>Please log in! Attempt to access user dash without an active session or login!</h4>
			<form action='/~$linux_user/m2_group/index.html' method='post'>
				<button type='submit'> Log in </button>
			</form>");
			return false;
		}
		
		//If the session already has a user, we don't need to connect to this page via post or get
		//from a re-direct. We just grab user from the session.
		if (@$_SESSION["user"] != null){
			$user = @$_SESSION["user"]; //This user has already been filtered
			$rVal = $user;
		}
		else {
			//If the session doesn't have a user yet
			//we get the user from the post request made to this page from login
			$user = (string)$_POST["u"];

			//Filter user
			if( !preg_match('/^[\w_\-]+$/', $user) ){
				print("<h4>Username contains invalid characters!</h4>
				<form action='/~$linux_user/m2_group/index.html' method='post'>
					<button type='submit'> Log in </button>
				</form>");
				return false;
			}

			//Necessary because of how ADMIN user implements deletion
			if(strpos( $user,"~")!= false){
				
				print("2");
				print("<h4>Username contains invalid characters!</h4>
				<form action='/~$linux_user/m2_group/index.html' method='post'>
					<button type='submit'> Log in </button>
				</form>");
				return false;
			}

			
			//If the username field is blank
			if (strcmp($user, "") == 0){
				print("<h4>Please enter a user name!</h4>
				<form action='/~$linux_user/m2_group/index.html' method='post'>
					<button type='submit'> Log in </button>
				</form>");
				return false;
			}

			
			//If the user doesn't exist
			if (!user_exists($user)){
				print("<h4>User does not exist!</h4>
				<form action='/~$linux_user/m2_group/index.html' method='post'>
					<button type='submit'> Log in </button>
				</form>
				");
				return false;
			}

			//If all these tests pass, we can return the username from this function
			$rVal = $user;
			
		}

		return $rVal;
		
	}
    ?>
  </body>
</html>



