<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Apache + PHP File Sharing</title>
  </head>
  <body>
    <h1>Apache + PHP File Sharing</h1>
    <?php
	
	ini_set('display_errors',1);
	main();

	error_reporting(E_ALL);

	function main(){
//
//
		$linux_user = "rnepal";
    //
    //
	session_start();	
		

  if (@$_SESSION["user"] != null){
    $user = @$_SESSION["user"];
  }

  //if user is not logged in
  else {
    
        print("<h4>Please log in to delete files. Do not try deleting through a URL!</h4>
        <form action='/~$linux_user/m2_group/index.html' method='post'>
          <button type='submit'> Log in </button>
        </form> ");
        session_destroy();
        exit();
      

    
  }
	print("<h2>$user</h2>");


     //FIEO
	$file_name = (string) htmlentities($_POST["file"]);
   
 

	$file_destination = sprintf("/srv/m2_group_secure/$user/$file_name");
    
    //print("<p>$file_destination</p>");

    $file_exists = file_exists($file_destination);
    //file does not exist
    if(!$file_exists){
        print("file does not exist");
    }
    //if file exists and we can delete it, say deleted success
	  else if(unlink($file_destination)){

	  	print("<h4>Deleted $file_name successfully.</h4><br><br>");
	  }
    //if we cant delete
    else {
		
	  	print("<h4>Failed to delete $file_name.</h4><br><br>");
	  }

    //back to userdashboard link
    print("<a href='/~$linux_user/m2_group/user_dash.php'>Return to Dashboard</a>");
	}
    ?>
    
  </body>
</html>


