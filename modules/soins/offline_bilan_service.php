<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::get("service_id");

// Redirection vers le bilan par service
// avec toutes les catégories cochées
// (entête caché)
CAppUI::redirect("m=dPhospi&a=vw_bilan_service&token_cat=all&service_id=$service_id&offline=1&dialog=1");

?>