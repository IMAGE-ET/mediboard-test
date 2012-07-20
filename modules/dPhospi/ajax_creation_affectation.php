<?php  /** $Id: ajax_creation_affectation.php  $ **/

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
// Récupération des paramètres
$sejour_id    = CValue::getOrSession("sejour_id");
$lit_id       = CValue::getOrSession("lit_id");
$entree       = CValue::getOrSession("entree");
$sortie       = CValue::getOrSession("sortie");

$affectation = new CAffectation();
$affectation->sejour_id = $sejour_id;
$affectation->lit_id    = $lit_id;
$affectation->entree    = $entree;
$affectation->sortie    = $sortie;

$affectation->store();
?>