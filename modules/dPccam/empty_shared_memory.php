<?php 

/**
 * $Id$
 *  
 * @category CCAM
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$count = SHM::remKeys("code_ccam-*");
CAppUI::stepAjax("module-ccam-msg-cache-code_ccam-suppr", UI_MSG_OK, $count);
