<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Intaller
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

set_time_limit(180);

$session_name = basename(dirname(dirname($_SERVER["REQUEST_URI"])));
$session_name = preg_replace("/[^a-z0-9]/i", "", $session_name);

session_name($session_name);
session_start();

if (isset($_GET["logout"])) {
  session_unset();
  session_destroy();
  session_start();
}

if (defined("E_DEPRECATED")) {
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

$mbpath = "../";

if (!file_exists($mbpath."classes/CMbArray.class.php")){
  $mbpath = "./";
}

require_once $mbpath."classes/CMbArray.class.php";
require_once $mbpath."classes/CValue.class.php";
require_once $mbpath."includes/mb_functions.php";
require_once $mbpath."includes/version.php";
require_once $mbpath."classes/Chronometer.class.php";

// Autoloader
function autoloader($class_name) {
  $dir = dirname(__FILE__)."/../classes";
  $file = "$dir/$class_name.class.php";

  if (file_exists($file)) {
    include $file;
  }

  return false;
}

spl_autoload_register("autoloader");

global $stepsText;
$stepsText = array(
  "01_check"      => "Prérequis",
  "02_fileaccess" => "Permissions en écriture",
  "03_install"    => "Installation",
  "04_configure"  => "Configuration",
  "05_initialize" => "Initialisation",
  "06_feed"       => "Remplissage des bases",
  "07_finish"     => "Finalisation",
  "08_phpinfo"    => "Infos PHP",
  "09_errorlog"   => "Logs d'erreur",
  //"10_update"     => "Mise à jour du système",
);

$steps = array_keys($stepsText);

$currentStep = basename($_SERVER["PHP_SELF"], ".php");

if (!in_array($currentStep, $steps)) {
  trigger_error("Etape $currentStep inexistante", E_USER_ERROR);
}

$currentStepKey = array_search($currentStep, $steps);

$chrono = new Chronometer();
$chrono->start();

function showHeader() {
  global $currentStepKey, $currentStep, $steps, $version;
  
  ob_end_clean(); // Turn off output buffering

  header("Content-type: text/html; charset=iso-8859-1");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Mediboard :: Assistant d'installation &mdash; Etape <?php echo $currentStepKey+1; ?> : <?php echo $currentStep; ?></title>
  <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
  <meta name="Description" content="Mediboard SIH Assistant d'installation" />
  <meta name="Version" content="<?php echo $version["string"]; ?>" />
  <link rel="stylesheet" type="text/css" href="../style/mediboard/main.css" />
  <link rel="stylesheet" type="text/css" href="../style/e-cap/main.css" />
</head>

<body class="wizard">

<h1>Installation de Mediboard <?php echo $version["string"]; ?> &mdash; Etape <?php echo $currentStepKey+1; ?>/<?php echo count($steps); ?></h1>
<?php
  showToc();
}

function showToc() {
  global $stepsText;
  $currentStep = basename($_SERVER["PHP_SELF"], ".php");

  // 01
  $version     = new CPHPVersion;
  $extension   = new CPHPExtension;
  $package     = new CPearPackage;
  $restriction = new CUrlRestriction;
  
  // 02
  $pathAccess  = new CPathAccess;
  
  // 03
  $library     = new CLibrary;

  $valid = array(
    "01_check"      => $version->checkAll(false) &&
                       $extension->checkAll(false) &&
                       $package->checkAll(false) &&
                       $restriction->checkAll(false),
    "02_fileaccess" => $pathAccess->checkAll(false),
    "03_install"    => $library->checkAll(false),
  );
?>
<div class="toc">
  <ol>
    <?php foreach ($stepsText as $step => $stepName) { ?>
    <li>
      <?php
      if ($currentStep == $step) { 
        ?><strong><?php 
      }
      else { 
        ?><a href="<?php echo $step; ?>.php"><?php 
      }
      
      if (isset($valid[$step])) {
        ?><img src="../style/mediboard/images/buttons/<?php echo ($valid[$step] ? "tick" : "cancel"); ?>.png" /><?php
      }
      
      echo $stepName;
        
      if ($currentStep == $step) { 
        ?></strong><?php 
      }
      else { 
        ?></a><?php 
      }

    ?>
    </li>
    <?php } ?>
  </ol>
  <?php if (isset($_SESSION["auth_username"])) { ?>
    <a href="?logout=1" class="logout">Déconnexion</a>
  <?php } ?>
</div>
<?php }

function showFooter() {
  global $stepsText, $currentStepKey, $steps, $chrono;
  $chrono->stop();

  $prevStep = $currentStepKey > 0 ? $steps[$currentStepKey-1] : null;
  $nextStep = $currentStepKey+1 < count($steps) ? $steps[$currentStepKey+1] : null;
?>
<hr />
<div class="navigation">
  <?php if ($prevStep) { ?><a class="button left" href="<?php echo $prevStep; ?>.php"><?php echo $stepsText[$prevStep]; ?></a><?php } ?>
  <?php if ($nextStep) { ?><a class="button right rtl" href="<?php echo $nextStep; ?>.php"><?php echo $stepsText[$nextStep]; ?></a><?php } ?>
</div>

<div class="generated">
  Page générée en <?php printf("%.3f", $chrono->total); ?> secondes.
</div>

</body>

</html>
<?php 
exit(0);
} ?>