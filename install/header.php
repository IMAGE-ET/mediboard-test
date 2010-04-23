<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

set_time_limit(180);

$mbpath = "../";

require_once($mbpath."classes/mbarray.class.php");
require_once($mbpath."classes/value.class.php");
require_once($mbpath."includes/mb_functions.php");
require_once($mbpath."includes/version.php");
require_once($mbpath."classes/chrono.class.php");

$stepsText = array (
  "check" => "Prérequis", 
  "fileaccess" => "Permissions en écriture", 
  "install" => "Installation", 
  "configure" => "Configuration", 
  "initialize" => "Initialisation", 
  "feed" => "Remplissage des bases", 
  "finish" => "Finalisation",
  "phpinfo" => "Infos PHP",
  "errorlog" => "Logs d'erreur",
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
  global $stepsText, $currentStepKey, $currentStep, $steps, $version;
  
  header("Content-type: text/html; charset=iso-8859-1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mediboard :: Assistant d'installation &mdash; Etape <?php echo $currentStepKey+1; ?> : <?php echo $currentStep; ?></title>
  <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
  <meta name="Description" content="Mediboard SIH Assistant d'installation" />
  <meta name="Version" content="<?php echo $version["string"]; ?>" />
  <link rel="stylesheet" type="text/css" href="../style/mediboard/main.css" />
  <link rel="stylesheet" type="text/css" href="../style/e-cap/main.css" />
</head>

<body class="wizard">

<div class="toc">
  <ol>
    <?php foreach ($stepsText as $step => $stepName) { ?>
    <li>
      <?php if ($currentStep == $step) { ?>
      <strong><?php echo $stepName; ?></strong>
      <?php } else { ?>
      <a href="<?php echo $step; ?>.php"><?php echo $stepName; ?></a>
      <?php } ?>
    </li>
    <?php } ?>
  </ol>
</div>

<h1>Installation de Mediboard <?php echo $version["string"]; ?> &mdash; Etape <?php echo $currentStepKey+1; ?>/<?php echo count($steps); ?></h1>
<?php 
}

function showFooter() {
  global $stepsText, $currentStepKey, $currentStep, $steps, $chrono;
  $chrono->stop();
  
  $prevStep = $currentStepKey > 0 ? $steps[$currentStepKey-1] : null;
  $nextStep = $currentStepKey+1 < count($steps) ? $steps[$currentStepKey+1] : null;
?>
<hr />
<div class="navigation">
  <?php if ($prevStep) { ?><button class="left" onclick="location.href='<?php echo $prevStep; ?>.php'"><?php echo $stepsText[$prevStep]; ?></button><?php } ?>
  <?php if ($nextStep) { ?><button class="right rtl" onclick="location.href='<?php echo $nextStep; ?>.php'"><?php echo $stepsText[$nextStep]; ?></button><?php } ?>
</div>

<div class="generated">
  Page générée en <?php printf("%.3f", $chrono->total); ?> secondes.
</div>

</body>

</html>
<?php } ?>