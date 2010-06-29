<html>
<head>
<style type="text/css">
* {
  font-family: monospace; 
  font-size: 11px;
}

body.loaded {
  background: #fff;
}
</style>
</head>
<body onload="document.getElementsByTagName('body')[0].className = 'loaded'">

<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

if (isset($_POST["action"])) {
  $action = $_POST["action"];
  
  if (in_array($action, array("info", "real"))) {
    $rev  = isset($_POST["rev"]) && is_numeric($_POST["rev"]) ? " -r {$_POST['rev']}" : "";
    $sudo = isset($_POST["passwd"]) ? "echo {$_POST['passwd']}|sudo -S" : "";
    
    exec("$sudo sh ../shell/update.sh $action $rev", $res);
    
    if (empty($res)) {
      echo "Une erreur s'est produite";
    }
    else {
      foreach($res as $_res) {
        echo utf8_decode("$_res<br/>");
      }
    }
  }
}

?>

</body>
</html>