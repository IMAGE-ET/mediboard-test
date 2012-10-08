<?php
/**
 * Installation authentication checker
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "header.php";

if (!@include_once $mbpath."includes/config.php") { 
  return;
}

function computeHash($password, $salt) {
  return hash("SHA256", $salt.$password);
}

/**
 * Show login page 
 * 
 * @return void
 */
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
    <td><input type="text" name="username" autofocus="autofocus" /></td>
  </tr>
  <tr>
    <th><label for="password">Mot de passe :</label></th>
    <td><input type="password" name="password" /></td>
  </tr>
  <tr>
    <td class="button" colspan="2"><button class="submit" type="submit">Valider</button></td>
  </tr>
</table>

</form>

<?php
  showFooter();
}

$dbConfig = $dPconfig["db"]["std"];
$host = $dbConfig["dbhost"];
$user = $dbConfig["dbuser"];
$pass = $dbConfig["dbpass"];
$name = $dbConfig["dbname"];

$table = "users";
$userCol = "user_username";
$passCol = "user_password";
$saltCol = "user_salt";

$db = new CMbDb($host, $user, $pass, $name);

if (!$db->connect()) {
  // DB not configured yet, don't need to auth
  return;
}

$list = $db->getAssoc("SELECT * FROM $table");

if (!$list || !isset($list[1])) {
  return; 
}

$admin_user = $list[1];

// Abandon if only user is still admin/admin
if ($admin_user[$userCol] == "admin" && $admin_user[$passCol] == md5("admin")) {
  return;
}

// Check if any authentification
if (!empty($_POST["username"]) && !empty($_POST["password"])) {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $username_db = $admin_user[$userCol];
  $password_db = $admin_user[$passCol];
  $salt_db     = $admin_user[$saltCol];
  
  sleep(2); // Intentional to avoid brute force
  
  if ($username == $username_db && (md5($password) == $password_db || computeHash($password, $salt_db) == $password_db)) {
    $_SESSION["auth_username"] = $username;
  }
  else {
    session_unset();
    session_destroy();
    showLogin();
    return;
  }
}
elseif (empty($_SESSION["auth_username"])) {
  showLogin();
  return;
}
