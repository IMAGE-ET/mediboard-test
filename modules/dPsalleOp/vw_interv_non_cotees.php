<?php

/**
 * dPsalleOp
 *  
 * @category dPsalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$_GET["all_prats"] = "1";

CAppUI::requireModuleFile("dPboard", "vw_interv_non_cotees");

?>