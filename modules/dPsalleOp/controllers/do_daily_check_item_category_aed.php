<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$do = new CDoObjectAddEdit("CDailyCheckItemCategory");
$do->redirect = "m=dPsalleOp&a=vw_daily_check_item_category";
$do->doIt();
