<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$ds_logicmax = CSQLDataSource::get("logicmax");

$query = "UPDATE S_F_NOMENCLATURE 
          SET S_NOM_TARIF_1 = '23', S_NOM_PRIX_PRATIQUE = '23' 
					WHERE S_NOM_CODE = 'C'";
$ds_logicmax->exec($query);

CAppUI::stepAjax("Action effectuée", UI_MSG_OK);

?>