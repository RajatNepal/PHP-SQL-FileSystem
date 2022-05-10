
    <?php
	
	ini_set('display_errors',1);
	main();

	error_reporting(E_ALL);

	function main(){

		$linux_user = "rnepal";
	session_start();	
	if (@$_SESSION["user"] != null){
        $user = @$_SESSION["user"];
      }

      //if user is not logged in
      else {
        
            print("<head>
            <title>Apache + PHP File Sharing</title>
          </head>
          <body>
            <h1>Apache + PHP File Sharing</h1><h4>Please log in to view files. Do not try viewing through a URL!</h4>
            <form action='/~$linux_user/m2_group/index.html' method='post'>
              <button type='submit'> Log in </button>
            </form></body>");
            session_destroy();
            exit();

      }


    //FIEO
	$file_name = (string) htmlentities($_POST["file"]);


	$file_destination = sprintf("/srv/m2_group_secure/$user/$file_name");
    
    $file_exists = file_exists($file_destination);
    
    if(!$file_exists){
        print("file does not exist");
        print("<a href='/~$linux_user/m2_group/user_dash.php'>Return to Dashboard</a>");
    }
    else{
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file_destination);

        ob_clean();
        header("Content-Type: $mime");
       
        header("Content-Disposition: filename=$file_name");
        readfile($file_destination);

    }

	}
    ?>
