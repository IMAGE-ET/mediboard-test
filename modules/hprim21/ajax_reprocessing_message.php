<?php

/**
 * Reprocessing des messages Hprim21
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$echg_hprim21_id = CValue::get("echange_hprim21_id");

// Chargement de l'objet
$echg_hprim21 = new CEchangeHprim21();
$echg_hprim21->load($echg_hprim21_id);

$hprimFile = tmpfile();
fwrite($hprimFile, $echg_hprim21->message);
fseek($hprimFile, 0);

$hprimReader = new CHPrim21Reader();
$hprimReader->_echange_hprim21 = $echg_hprim21;
$hprimReader->readFile(null, $hprimFile);

// Mapping de l'échange
$echg_hprim21 = $hprimReader->bindEchange();

if (!count($hprimReader->error_log)) {
  $echg_hprim21->message_valide = true;
}
else {
  $echg_hprim21->message_valide = false;
  CAppUI::setMsg("Erreur(s) pour le fichier '$echg_hprim21->nom_fichier' : $hprimReader->error_log", UI_MSG_WARNING);
}
    
$echg_hprim21->store();

CAppUI::setMsg("Message HPRIM 2.1 retraité", UI_MSG_OK);

echo CAppUI::getMsg();

