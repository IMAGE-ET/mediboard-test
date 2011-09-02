<?php  /* $Id: ajax_modif_plan_etage.php  $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Récupération des paramètres

$chambre_id 	= CValue::get("chambre_id");
$plan_x			  = CValue::get("plan_x");
$plan_y			  = CValue::get("plan_y");

if($chambre_id!=""){
	$chambre= new CChambre();
	$chambre->load($chambre_id);
	$chambre->plan_x=$plan_x;
	$chambre->plan_y=$plan_y;
	$chambre->store();
}

?>