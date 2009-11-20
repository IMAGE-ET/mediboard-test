<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit("CDailyCheckItemCategory");
$do->redirect = "m=dPsalleOp&a=vw_daily_check_item_category";
$do->doIt();

?>