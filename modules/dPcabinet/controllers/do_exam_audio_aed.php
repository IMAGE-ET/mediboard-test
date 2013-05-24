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

// Sets the values to the session too
CValue::postOrSessionAbs("_conduction");
CValue::postOrSessionAbs("_oreille");

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->doIt();
?>
