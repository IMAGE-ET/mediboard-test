<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Current user and current function
$mediuser = CMediusers::get();
$function = $mediuser->loadRefFunction();

// Filter
$filter = new CPlageconsult();
$filter->_function_id       = CValue::get("_function_id", $function->type == "cabinet" ? $function->_id : null);
$filter->_date_min          = CValue::get("_date_min", CMbDT::date("last month"));
$filter->_date_max          = CValue::get("_date_max", CMbDT::date());
$filter->_user_id           = CValue::get("_user_id", null);
$compute_mode               = CValue::get("compute_mode");
$csv                        = CValue::get("csv");
$page                       = (int)CValue::get("page", 0);

$limit = 1000;
if ($csv) {
  $limit = 10000;
}

$group_id = CGroups::loadCurrent()->_id;

$ds = $filter->getDS();
$list = array();

if ($compute_mode == "adresse_par") {
  $query = "SELECT
              consultation.adresse_par_prat_id,
              COUNT(consultation.consultation_id) AS total
            FROM consultation
            LEFT JOIN plageconsult        ON plageconsult.plageconsult_id    = consultation.plageconsult_id
            LEFT JOIN users_mediboard     ON users_mediboard.user_id         = plageconsult.chir_id
            LEFT JOIN functions_mediboard ON functions_mediboard.function_id = users_mediboard.function_id
            WHERE
              functions_mediboard.group_id = ?1 AND
              consultation.adresse_par_prat_id IS NOT NULL";
  $query = $ds->prepare($query, $group_id);

  if ($filter->_date_min) {
    $query .= $ds->prepare(" AND plageconsult.date >= ?", $filter->_date_min);
  }

  if ($filter->_date_max) {
    $query .= $ds->prepare(" AND plageconsult.date <= ?", $filter->_date_max);
  }

  if ($filter->_user_id) {
    $query .= $ds->prepare(" AND users_mediboard.user_id = ?", $filter->_user_id);
  }

  if ($filter->_function_id) {
    $query .= $ds->prepare(" AND functions_mediboard.function_id = ?", $filter->_function_id);
  }

  $query .= "
            GROUP BY consultation.adresse_par_prat_id
            ORDER BY total DESC
            LIMIT $page, $limit;";

  $list = $ds->loadHashList($query);
}
elseif ($compute_mode == "correspondants") {
  $tag = CPatient::getTagIPP();

  if ($tag) {
    $query = "SELECT
              medecin.medecin_id,
              COUNT(DISTINCT(patients.patient_id)) AS total

            FROM patients
            LEFT JOIN id_sante400   ON id_sante400.object_id = patients.patient_id AND id_sante400.object_class = 'CPatient'
            LEFT JOIN correspondant ON correspondant.patient_id = patients.patient_id
            LEFT JOIN medecin       ON medecin.medecin_id = correspondant.medecin_id
            WHERE
              id_sante400.tag = ?1 AND
              correspondant.medecin_id IS NOT NULL
            GROUP BY correspondant.medecin_id
            ORDER BY total DESC
            LIMIT $page, $limit;";
    $query_corresp = $ds->prepare($query, $tag);

    $query = "SELECT
              patients.medecin_traitant,
              COUNT(DISTINCT(patients.patient_id)) AS total

            FROM patients
            LEFT JOIN id_sante400 ON id_sante400.object_id = patients.patient_id AND id_sante400.object_class = 'CPatient'
            WHERE
              id_sante400.tag = ?1 AND
              patients.medecin_traitant IS NOT NULL
            GROUP BY patients.medecin_traitant
            ORDER BY total DESC
            LIMIT $page, $limit;";
    $query_traitant = $ds->prepare($query, $tag);
  }
  else {
    $query_corresp = "SELECT
              medecin.medecin_id,
              COUNT(DISTINCT(patients.patient_id)) AS total

            FROM patients
            LEFT JOIN correspondant ON correspondant.patient_id = patients.patient_id
            LEFT JOIN medecin       ON medecin.medecin_id = correspondant.medecin_id
            WHERE
              correspondant.medecin_id IS NOT NULL
            GROUP BY correspondant.medecin_id
            ORDER BY total DESC
            LIMIT $page, $limit;";

    $query = "SELECT
              patients.medecin_traitant,
              COUNT(DISTINCT(patients.patient_id)) AS total

            FROM patients
            WHERE
              patients.medecin_traitant IS NOT NULL
            GROUP BY patients.medecin_traitant
            ORDER BY total DESC
            LIMIT $page, $limit;";
    $query_traitant = $ds->prepare($query, $tag);
  }

  $list_corresp  = $ds->loadHashList($query_corresp);
  $list_traitant = $ds->loadHashList($query_traitant);

  foreach ($list_traitant as $_medecin_id => $_count) {
    if (array_key_exists($_medecin_id, $list_corresp)) {
      $list_corresp[$_medecin_id] += $_count;
    }
    else {
      $list_corresp[$_medecin_id] = $_count;
    }
  }

  arsort($list_corresp);
  $list = $list_corresp;
}

$where = array(
  "medecin_id" => $ds->prepareIn(array_keys($list))
);
$medecin = new CMedecin();
/** @var CMedecin[] $medecins */
$medecins = $medecin->loadList($where);

if ($csv) {
  $csvfile = new CCSVFile();
  $titles = array(
    "Total",
    CAppUI::tr("CMedecin-nom"),
    CAppUI::tr("CMedecin-prenom"),
    CAppUI::tr("CMedecin-type"),
    CAppUI::tr("CMedecin-tel"),
    CAppUI::tr("CMedecin-fax"),
    CAppUI::tr("CMedecin-email"),
    CAppUI::tr("CMedecin-adresse"),
    CAppUI::tr("CMedecin-cp"),
    CAppUI::tr("CMedecin-adeli"),
    CAppUI::tr("CMedecin-rpps"),
  );
  $csvfile->writeLine($titles);
  
  foreach ($list as $_medecin_id => $_count) {
    $_medecin = $medecins[$_medecin_id];
    $_line = array(
      $_count,
      $_medecin->nom,
      $_medecin->prenom,
      $_medecin->type,
      $_medecin->tel,
      $_medecin->fax,
      $_medecin->email,
      $_medecin->adresse,
      $_medecin->cp,
      $_medecin->adeli,
      $_medecin->rpps,
    );

    $csvfile->writeLine($_line);
  }

  $csvfile->stream("Médecins correspondants");
}
else {
  $smarty = new CSmartyDP();
  $smarty->assign("medecins", $medecins);
  $smarty->assign("counts", $list);
  $smarty->display("inc_stats_medecins.tpl");
}
