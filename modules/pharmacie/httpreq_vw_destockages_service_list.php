<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');

// Calcul de date_max et date_min
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');


// remplissage de liste destockages (a titre d'exemple de structure

$list_destockages = array(
  '0645821' => array( /// Code CIP
    'quantite' => 3,
    'conditionnement' => 'Boites',
  ), 
  
  /// ...

);



// Création du template
$smarty = new CSmartyDP();
$smarty->assign('list_destockages', $list_destockages);
$smarty->display('inc_destockages_service_list.tpl');

?>