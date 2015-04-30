<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$patient_id = CValue::get('patient_id', 0);
$sejour_id  = CValue::get('sejour_id');

$patient = new CPatient();
$patient->load($patient_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

if ($patient->_id) {
  $constantes = $patient->loadRefLatestConstantes(null, array('poids', 'taille'));

  $poids       = '&mdash;';
  $taille      = '&mdash;';
  $imc         = '&mdash;';
  $unit_poids  = CConstantesMedicales::$list_constantes['poids']['unit'];
  $unit_taille = CConstantesMedicales::$list_constantes['taille']['unit'];

  if ($constantes[0]->poids) {
    $date  = CMbDT::format($constantes[1]['poids'], CAppUI::conf('datetime'));
    $poids = "<span title='$date'>{$constantes[0]->poids} {$unit_poids}</span>";

    if ($sejour && $sejour->_id) {
      if ($constantes[1]['poids'] < $sejour->entree || $constantes[1]['poids'] > $sejour->sortie) {
        // Weight outdated
        $msg   = utf8_encode(CAppUI::tr('CPatient-msg-Entry is outdated'));
        $date  = CMbDT::format($constantes[1]['poids'], CAppUI::conf('datetime'));
        $poids = "<span title='{$msg} : $date' style='color: firebrick;'><strong>{$constantes[0]->poids} {$unit_poids}</strong></span>";
      }
    }
  }

  if ($constantes[0]->taille) {
    $taille = "{$constantes[0]->taille} {$unit_taille}";
  }

  if ($constantes[0]->_imc) {
    $imc = $constantes[0]->_imc;
  }

  CApp::json(
    array(
      'poids'  => $poids,
      'taille' => $taille,
      'imc'    => $imc
    )
  );
}

CApp::rip();