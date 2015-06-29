<?php 

/**
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->makePDFarchive();