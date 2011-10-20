<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="X-UA-Compatible" content="IE=8" />   
	</head>
  <body>
    Modal Page
    <br>
    <span>Session ID : <?php echo session_id(); ?></span>
    <br>
    <button onclick="dialogArguments.win.open('page1.php')">
      Open Page 1 (workaround)
    </button>
    <br>
    <button onclick="window.open('page1.php')">
      Open Page 1 (problem)
    </button>
  </body>
</html>
