<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$do = new CDoObjectAddEdit("CExamPossum", "exampossum_id");
$do->redirect = null;
$do->doIt();
?>