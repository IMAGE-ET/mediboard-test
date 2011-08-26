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
$zone			= CValue::get("zone");

if($chambre_id!=""){
	$chambre= new CChambre();
	$chambre->load($chambre_id);
	$chambre->plan=$zone;
	$chambre->store();
}

?>