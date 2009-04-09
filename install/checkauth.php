<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("header.php");
require_once("mbdb.class.php");

require_once("Auth.php");

if (!@include_once($mbpath."includes/config.php")) { 
  return;
}

function showLogin() {
  showHeader();
?>

<form name="login" method="post" action="">

<table class="form">
  <tr>
    <th class="title" colspan="2">Authentification administrateur obligatoire</th>
  </tr>
  <tr>
    <th><label for="username">Nom d'utilisateur :</label></th>
    <td><input type="text" name="username" /></td>
  </tr>
  <tr>
    <th><label for="password">Mot de passe :</label></th>
    <td><input type="password" name="password" />
  </td>
  <tr>
    <td class="button" colspan="2"><button class="submit" type="submit">Valider</button></td>
  </tr>
</table>

</form>

<?php
  showFooter();
  die();
}

$dbConfig = $dPconfig["db"]["std"];
$host = $dbConfig["dbhost"];
$name = $dbConfig["dbname"];
$user = $dbConfig["dbuser"];
$pass = $dbConfig["dbpass"];
$table = "users";
$userCol = "user_username";
$passCol = "user_password";

$params = array(
  "dsn" => "mysql://$user:$pass@$host/$name",
  "table" => $table,
  "usernamecol" => $userCol,
  "passwordcol" => $passCol
);

$auth = new Auth("DB", $params);
$auth->setShowLogin(false);
$auth->start();

// Abandon if authentification is not possible
$users = $auth->listUsers();
if (PEAR::isError($users)) {
  return;
}

// Abandon if only user is still admin/admin
if ($users[0][$userCol] == "admin" and $users[0][$passCol] == md5("admin")) {
  return;
}

// Check if any authentification
if (!$auth->checkAuth()) {
  showLogin();
}

// Check if authentified user is root admin with user_id = 1
$db = new CMbDb($host, $user, $pass, $name);
if ($db->connect()) {
  $authUserId = $db->getOne("SELECT * FROM $table WHERE `$userCol` = ?", $auth->getUsername());
  if ($authUserId != 1) {
    $auth->logout();
    showLogin();
  }
}
?>
