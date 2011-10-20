<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=7" />   
    <script>
      function MyShowModal(){
        var args = {win: window};
        showModalDialog("modal.php", args);
      }
			
			function MyShowModalMB(){
        var args = {win: window};
        showModalDialog("iframe.php", null, 'dialogHeight:700px;dialogWidth:1000px;center:yes;resizable:no;scroll:no;');
			}
      
      function MyShowModalIntermediate(){
				window.open("intermediate.php");
      }
    </script>
  </head>
  <body>
    Parent Page
    <br>
    <span>Session ID : <?php echo session_id(); ?></span>
    <br>
    <button onclick="MyShowModal()">
      showModalDialog
    </button>
    <br>
    <button onclick="MyShowModalMB()">
      showModalDialog > iframe
    </button>
    <br>
    <button onclick="MyShowModalIntermediate()">
      intermediate > showModalDialog > iframe
    </button>
  </body>
</html>
