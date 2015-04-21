<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$view_current = CValue::get("view_current", 0);

CView::enforceSlave();

$smarty = new CSmartyDP();

// Pour l'établissement courant
if ($view_current) {
  $etab          = CGroups::loadCurrent();
  $current_group = $etab->_id;

  $res_current_etab = array();
  $where            = array();
  $ljoin            = array();
  
  // - Nombre de séjours
  $tag_NDA               = CSejour::getTagNDA($current_group);
  $where["tag"]          = " = '$tag_NDA'";
  $where["object_class"] = " = 'CSejour'";

  $idex = new CIdSante400;
  $res_current_etab["CSejour-_NDA"] = $idex->countList($where);
  
  // - Patients IPP
  $tag_ipp               = CPatient::getTagIPP($current_group);
  $where["tag"]          = " = '$tag_ipp'";
  $where["object_class"] = " = 'CPatient'";

  $idex = new CIdSante400;
  $res_current_etab["CPatient-_IPP"] = $idex->countList($where);
  
  // - Nombre de consultations
  $where        = array();
  $consultation = new CConsultation;

  $ljoin["plageconsult"]        = "consultation.plageconsult_id = plageconsult.plageconsult_id";
  $ljoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
  $ljoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";

  $where["functions_mediboard.group_id"] = " = '$current_group'";
  $res_current_etab["CConsultation"]     = $consultation->countList($where, null, $ljoin);
  
  // - Lits
  $ljoin = array();
  $where = array();
  $lit   = new CLit;

  $ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"] = "chambre.service_id = service.service_id";

  $where["service.group_id"] = " = '$current_group'";

  $res_current_etab["CLit"] = $lit->countList($where, null, $ljoin);
  
  // - Chambres
  $ljoin   = array();
  $where   = array();
  $chambre = new CChambre;

  $ljoin["service"]             = "chambre.service_id = service.service_id";
  $where["service.group_id"]    = " = '$current_group'";

  $res_current_etab["CChambre"] = $chambre->countList($where, null, $ljoin);
  
  // - Utilisateurs
  $ljoin    = array();
  $where    = array();
  $mediuser = new CMediusers;

  $ljoin["functions_mediboard"]          = "users_mediboard.function_id = functions_mediboard.function_id";
  $where["functions_mediboard.group_id"] = " = '$current_group'";

  $res_current_etab["CMediusers"]        = $mediuser->countList($where, null, $ljoin);

  $smarty->assign("res_current_etab", $res_current_etab);
  $smarty->display("inc_metrique_current_etab.tpl");
}
// Vue générale
else {
  $ds     = CSQLDataSource::get("std");
  $etab   = CGroups::loadCurrent();
  $result = array();

  $listeClasses = CApp::getInstalledClasses();
  
  foreach ($listeClasses as $class) {
    $object = new $class;

    if ($object->_spec->measureable) {
      $sql = "SHOW TABLE STATUS LIKE '{$object->_spec->table}'";
      $statusTable = $ds->loadList($sql);

      if ($statusTable) {
        $result[$class] = $statusTable[0];
        $result[$class]["Update_relative"] = CMbDate::relative($result[$class]["Update_time"]);
      }
    }
  }

  $smarty->assign("result",   $result);
  $smarty->assign("etab",     $etab);
  $smarty->assign("nb_etabs", $etab->countList());
  $smarty->display("view_metrique.tpl");
}
