<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("header.php");

if (!@include_once($mbpath."includes/config.php")) { 
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