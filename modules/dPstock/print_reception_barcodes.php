<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$reception_id = CValue::get('reception_id');
$force_print  = CValue::get('force_print');

$reception = new CProductReception();
$reception->load($reception_id);
$reception->loadRefsFwd();
$reception->loadRefsBack();

$pdf = new CMbPdf(); 

$pdf->setFont("vera", '', "10");

// Définition des marges de la pages
//$pdf->SetMargins(15, 15);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Creation d'une nouvelle page
$pdf->AddPage();

$data = array();
$j = 0;
if ($reception->_id) {
	foreach ($reception->_ref_reception_items as &$item) {
		$item->loadRefsBack();
		$item->loadRefsFwd();
		$item->_ref_order_item->loadReference();
		
		$reference = $item->_ref_order_item->_ref_reference;
		$reference->loadRefsFwd();
		$reference->_ref_product->loadRefsFwd();
		
		if(!$item->barcode_printed || $force_print) {
			for ($i = 0; $i < $item->quantity; $i++) {
        $data[$j] = array();
        $d = &$data[$j];
        
				$d[] = substr($reference->_ref_product->name, 0, 30);
        $d[] = CMbString::truncate(substr($reference->_ref_product->name, 30, 30));
				$d[] = $reference->_ref_product->_ref_societe->_view;
				$d[] = $item->lapsing_date;
				
				$d[] = array(
				  'barcode' => "{$reference->_ref_product->code} $item->code",
				  'type'    => 'C128B'
				);
				$j++;
			}
		}
	}
}

$pdf->WriteBarcodeGrid(8, 8, 210-16, 297-16, 3, 10, $data);

// Nom du fichier: prescription-xxxxxxxx.pdf   / I : sortie standard
$pdf->Output("barcodes.pdf","I");
