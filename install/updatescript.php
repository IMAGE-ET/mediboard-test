<html>
<head>
</head>
<body onload="this.style='background: #fff; opacity: 1;'">

<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
$action = $_POST["action"];
isset($_POST["passwd"]) ? $sudo = "echo {$_POST['passwd']}|sudo -S" : $sudo = '';
isset($_POST["rev"]) && is_numeric($_POST["rev"]) ? $rev = " -r {$_POST['rev']}" : $rev = '';

exec("$sudo sh ../shell/update.sh $action $rev",$res);

foreach($res as $_res) {
  echo utf8_decode("$_res<br/>");
}
?>
</body>
</html>