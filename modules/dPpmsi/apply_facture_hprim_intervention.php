<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $can;
$can->needsAdmin();

CAppUI::stepAjax("Fonctionnalité désactivée pour le moment", UI_MSG_ERROR);
return;

$operation = new COperation;

$operation->facture = "1";
$count = $operation->countMatchingList();
CAppUI::stepAjax("'%s' opérations facturées trouvées", UI_MSG_OK, $count);
$operation->facture = "0";
$count = $operation->countMatchingList();
CAppUI::stepAjax("'%s' opérations non facturées trouvées", UI_MSG_OK, $count);

$start = 30000;
$max = 100;
$limit = "$start, $max";

foreach ($operation->loadMatchingList(null, $limit) as $_operation) {
	$operation->loadHprimFiles();
	if ($count = count($_operation->_ref_hprim_files)) {
    CAppUI::stepAjax("'%s' HPRIM files for operation '%s'", UI_MSG_OK, $count, $_operation->_view);
	}
}
?>