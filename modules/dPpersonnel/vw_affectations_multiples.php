<?php /* $Id: vw_affectations_pers.php 6326 2009-05-19 07:20:26Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 6326 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$affectation = new CAffectationPersonnel();

$ds = $affectation->_spec->ds;
$query = "CREATE TEMPORARY TABLE `affectations_multiples` AS (
  SELECT COUNT(*) AS `affect_count`, `object_class`, `object_id`, `personnel_id`
  FROM `affectation_personnel` 
  GROUP BY `object_class`, `object_id`, `personnel_id`)";
$ds->exec($query);

$query = "SELECT * 
	FROM `affectations_multiples`
	WHERE `affect_count` > 1
	ORDER BY `affect_count` DESC";
$multiples = $ds->loadList($query);

$objects = array();
$personnels = array();
foreach ($multiples as &$_multiple) {
  $affectation->object_class = $_multiple["object_class"];
  $affectation->object_id    = $_multiple["object_id"   ];
  $affectation->personnel_id = $_multiple["personnel_id"];
  
  $affectation->_ref_object = null;
  $affectation->loadTargetObject();
  $_multiple["object"] = $affectation->_ref_object;
  
  $affectation->loadRefPersonnel();
  $_multiple["personnel"] = $affectation->_ref_personnel;

  $_multiple["affectations"] = $affectation->loadMatchingList();   
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("multiples", $multiples);

$smarty->display("vw_affectations_multiples.tpl");
?>
