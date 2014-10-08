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

$count = CExObject::clearLocales();

CAppUI::stepAjax("module-forms-msg-cache-ex_class-suppr", UI_MSG_OK, $count);
