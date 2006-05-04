<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("header.php");

if (!@include_once("$mbpath/includes/config.php")) { 
  showHeader();
?>

<div class="big-error">
  Erreur : Le fichier de configuration n'a pas été validé, merci de revenir à l'étape 
  précédante.
</div>

<?php
  showFooter();
  die();
}
?>