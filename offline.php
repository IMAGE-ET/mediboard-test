<?php
/**
 * Mediboard system offline page
 *
 * @package Mediboard
 * @author  SARL OpenXtrem <dev@openxtrem.com>
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version SVN: $Id$
 */

require "includes/config_all.php";

$reason = isset($_GET["reason"]) ? $_GET["reason"] : null;

switch ($reason) {
  case "maintenance":
    $msg = "Le système est désactivé pour cause de maintenance.";
    break;

  case "bdd":
    $msg = "La base de données n'est pas accessible.";
    break;

  case "backup":
    $msg = "La base de données est en cours de sauvegarde.";
    break;

  default :
    if (!($dPconfig["offline"] || $dPconfig["offline_non_admin"])) {
      header("Location: index.php");
    }

    $msg = "Le système est désactivé pour cause de maintenance.";
}

$path = "images/pictures";
$logo = (file_exists("$path/logo_custom.png") ? "$path/logo_custom.png" : "$path/logo.png");

header("Content-type: text/html; charset=iso-8859-1");

?>
<!DOCTYPE html>
<html>

<head>
  <title>Mediboard SIH &mdash; Service inaccessible</title>
  <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
  <meta name="Description"
    content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />

  <link rel="shortcut icon" type="image/ico" href="style/mediboard/images/icons/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="style/mediboard/main.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/e-cap/main.css" media="all" />
</head>

<body style="padding-top: 3em; text-align: center;">
  <h1>MEDIBOARD EST INACCESSIBLE POUR LE MOMENT</h1>
  <img src="<?php echo $logo; ?>" width="350" />
  <h2>
    <?php echo $msg; ?>
    <br />
    Merci de réessayer ultérieurement.
  </h2>
  <br />
  <a class="button change" href="index.php">Accéder à Mediboard</a>
</body>

</html>