<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Apache + PHP File Sharing</title>
  </head>
  <body>
    <h1>Apache + PHP File Sharing</h1>
    <h4>User Removed!</h4>
    <?php
      $successful_deletion = main();

      function main() {
          $target_user = (string)$_POST['remove_user'];


          if( !preg_match('/^[\w_\-]+$/', $target_user) ){
            return false;
          }

          $all_files = scandir("/srv/m2_group_secure/$target_user");

  
          
  
          //Before you delete the directory you have to delete all files within it
          foreach ($all_files as $file){
                  
                  if (strcmp($file, ".")==0 || strcmp($file, "..")==0){
                    //Ignore the invisible files, they mess up unlinking process
                  }
                  else{
                      $unlinked = unlink("/srv/m2_group_secure/$target_user/$file");
                      if (!$unlinked){
                          return false;
                      }
                  }
                  
          }
  
          //Deleting the directory for this user
          rmdir("/srv/m2_group_secure/$target_user");
  
          //Now remove the user from the users.txt file
  
          //!Need to safeguard against duplicate account creation!
  
          // Might just append a specific character, rather than deleting the line
  
          $userstxt_file_path = "/srv/m2_group_secure/users/users.txt";
          $user_file = fopen($userstxt_file_path, "r+");
       
          while (!feof($user_file)){
              $line = trim(fgets($user_file));
              if (strcmp($line,$target_user)==0){
                //Massive pain to write
                fseek($user_file,-2, SEEK_CUR);
                 $c = fwrite($user_file,"~");
                 
                fseek($user_file,+0, SEEK_CUR);
                 $c = fwrite($user_file,"\n");
  
              }
          }
  
          fclose($user_file);

          return true;
      }


      if($successful_deletion){
        header("Location: redirects/user_remove_success.html");
      }
      else {
        header("Location: redirects/user_remove_failure.html");
      }
        
    ?>
    <a href="/~antond/m2_group/user_dash.php">Return to Dashboard</a>
    <!-- <a href="/~rnepal/m2_group/user_dash.php">Return to Dashboard</a> -->
  </body>
</html>