<?php
/**
 * Mediboard system offline page
 *
 * @package Mediboard
 * @author  SARL OpenXtrem <dev@openxtrem.com>
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version SVN: $Id$
 */

// If CApp doesn't exist, go back to the index
if (!class_exists("CApp")) {
  header("Location: index.php");
  die;
}

header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 300");
header("Content-Type: text/html; charset=iso-8859-1");

$path = "images/pictures";
$logo = (file_exists(__DIR__."/$path/logo_custom.png") ? "$path/logo_custom.png" : "$path/logo.png");

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset=iso-8859-1" />
  <meta name="Description" content="Mediboard: Plateforme Open Source pour les Etablissements de Santé" />

  <title>Mediboard SIH &mdash; Service inaccessible</title>

  <link rel="shortcut icon" type="image/ico" href="style/mediboard/images/icons/favicon.ico" />
  <link rel="stylesheet" type="text/css" href="style/mediboard/main.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/e-cap/main.css" media="all" />
</head>

<body style="padding-top: 3em; text-align: center;">
  <h1>Mediboard est momentanément indisponible</h1>
  <img src="<?php echo $logo; ?>" width="350" />
  <h2>
    <?php echo htmlentities(CApp::$message); ?>
    <br />
    <br />
    Merci de réessayer ultérieurement.
  </h2>
  <br />
  <button class="change" onclick="document.location.reload(); return false;">Accéder à Mediboard</button>
</body>

</html>