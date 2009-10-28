<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: $
 * @author Thomas Despoix
 */

global $can;
$can->needsAdmin();

$operation = new COperation;

$operation->facture = "1";
$count = $operation->countMatchingList();
CAppUI::stepAjax("'%s' opérations facturées trouvées", UI_MSG_OK, $count);
$operation->facture = "0";
$ids = $operation->loadIds();
CAppUI::stepAjax("'%s' opérations non facturées trouvées", UI_MSG_OK, $count);

?>