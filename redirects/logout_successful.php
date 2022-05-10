

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Apache + PHP File Sharing</title>
  </head>
  <body>
    <h1>Apache + PHP File Sharing</h1>
    <h4>Logout Successful!</h4>
    <?php
        session_start(); //Necessary? Yes.
        session_destroy();
    ?>
    <!--<a href="/~antond/m2_group/index.html">Return to Login</a>-->
     <a href="/~rnepal/m2_group/index.html">Return to Login</a> 
  </body>
</html>