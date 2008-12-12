<?php /* $Id: product_order_item_reception.class.php 5037 2008-10-20 10:38:21Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 5037 $
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$order_id    = mbGetValueFromGet('order_id');
$force_print = mbGetValueFromGet('force_print');
$receptions_list = mbGetValueFromGet('receptions_list');

$order = new CProductOrder();
$order->load($order_id);
$order->loadRefsFwd();

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
if ($order->_id) {
	foreach ($order->_ref_order_items as &$item) {
		$item->loadRefsBack();
		$item->loadRefsFwd();
		$item->_ref_reference->loadRefsFwd();
		$item->_ref_reference->_ref_product->loadRefsFwd();
		
		foreach ($item->_ref_receptions as &$reception) {
			if(!$reception->barcode_printed || $force_print) {
				for ($i = 0; $i < $reception->quantity; $i++) {
	        $data[$j] = array();
	        $d = &$data[$j];
        
					$d[] = $item->_ref_reference->_ref_product->name;
					$d[] = $item->_ref_reference->_ref_product->_ref_societe->_view;
					$d[] = $reception->lapsing_date;
					$d[] = $reception->code;
					
					$d[] = array(
					  'barcode' => $item->_ref_reference->_ref_product->code." ".$reception->code,
					  'type'    => 'C128B'
					);
					$j++;
				}
			}
		}
	}
}
else if ($receptions_list) {
  foreach ($receptions_list as $reception_id) {
    $reception = new CProductOrderItemReception();
    $reception->load($reception_id);
    
      if(!$reception->barcode_printed || $force_print) {
      $reception->loadRefOrderItem();
      $item = $reception->_ref_order_item;

      for ($i = 0; $i < $reception->quantity; $i++) {
        $data[$j] = array();
        $d = &$data[$j];
        
	      $d[] = $item->_ref_reference->_ref_product->name;
	      $d[] = $item->_ref_reference->_ref_product->_ref_societe->_view;
	      $d[] = $reception->lapsing_date;
	      $d[] = $reception->code;
	      
	      $d[] = array(
	        'barcode' => $item->_ref_reference->_ref_product->code." ".$reception->code,
	        'label'   => "{$item->_ref_reference->_ref_product->code} {$reception->code}",
	        'type'    => 'C128B'
	      );
	      $j++;
      }
    }
  }
}

$pdf->WriteBarcodeGrid(8,8,210-16,297-16,4,10, $data);

// Nom du fichier: prescription-xxxxxxxx.pdf   / I : sortie standard
$pdf->Output("barcodes.pdf","I");

?>