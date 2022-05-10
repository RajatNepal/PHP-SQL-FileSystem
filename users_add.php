<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Apache + PHP File Sharing</title>
  </head>
  <body>
    <h1>Apache + PHP File Sharing</h1>
    <h4>Adding Users</h4>
    <?php
    
        $linux_user = "rnepal";
        $successful_addition = main();

        function main() {
            global $linux_user;
            
            $user_list = (string)$_POST['user_list'];
            $user_array = explode(",",$user_list);

            for ($i = 0; $i<count($user_array); $i++){
                
                $user_array[$i] = trim($user_array[$i]); //removes whitespace

                if( !preg_match("/^[\w_\-]+$/", $user_array[$i]) ){ //Filters input
                    print("<p>One of these users has invalid characters in their name.</p>");
                    return false;
                }

                $str = $user_array[$i];
                if (user_exists($str)){ //checking if the user already exists
                    print("<p>User already exists.</p>");
                    return false;
                }
            }    

            $user_array = array_unique($user_array); //Filters out all duplicates entered by the user.

            
           //creates directories and adds users to text file
            $file_path = "/srv/m2_group_secure";
           for ($i = 0; $i<count($user_array); $i++){
                $str = $user_array[$i];
                $a=mkdir("$file_path/$str", 0770); //permissions are 770
                $b=file_put_contents("$file_path/users/users.txt", "$str\n", FILE_APPEND);
                //important to set FILE_APPEND flag

                if (!$a) {
                    print("<p>Failed to add directory to Linux!</p>");
                }
                if (!$b) {
                    print("<p>Failed to add user to text file!</p>");
                }

                if (!$a || !$b){
                    return false;
                }
               
            }
            

            return true;
           
        }


        function user_exists($user) {
            $user_file_path = "/srv/m2_group_secure/users/users.txt";
            $user_file = fopen($user_file_path, "r");
            $in_list = false;

            if ($user_file == false){
                print("<p>Error! Apache couldn't read this file!</p>");

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

        if($successful_addition){
            header("Location: redirects/users_add_success.html");
          }
          else {
            header("Location: redirects/users_add_failure.html");
          }
    ?>
    <a href="/~antond/m2_group/user_dash.php">Return to Dashboard</a>
    <!-- <a href="/~rnepal/m2_group/user_dash.php">Return to Dashboard</a> -->
  </body>
</html>