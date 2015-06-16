<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CApp::setMemoryLimit("768M");

$where = array();
$ljoin = array();

$user_id             = CValue::get("user_id");
$classes_atc         = CValue::get("classes_atc");
$keywords_atc        = CValue::get("keywords_atc");
$code_cis            = CValue::get("code_cis");
$code_ucd            = CValue::get("code_ucd");
$libelle_produit     = CValue::get("libelle_produit");
$keywords_composant  = CValue::get("keywords_composant");
$composant           = CValue::get("composant");
$keywords_indication = CValue::get("keywords_indication");
$indication          = CValue::get("indication");
$type_indication     = CValue::get("type_indication");
$commentaire         = CValue::get("commentaire");
$section             = CValue::get("section_choose");
$export              = CValue::get("export", 0);

CValue::setSession("produit"            , CValue::get("produit"));
CValue::setSession("user_id"            , $user_id);
CValue::setSession("classes_atc"        , $classes_atc);
CValue::setSession("keywords_atc"       , $keywords_atc);
CValue::setSession("code_cis"           , $code_cis);
CValue::setSession("code_ucd"           , $code_ucd);
CValue::setSession("libelle_produit"    , $libelle_produit);
CValue::setSession("keywords_composant" , $keywords_composant);
CValue::setSession("composant"          , $composant);
CValue::setSession("keywords_indication", $keywords_indication);
CValue::setSession("indication"         , $indication);
CValue::setSession("type_indication"    , $type_indication);
CValue::setSession("commentaire"        , $commentaire);

$start = intval(CValue::get("start", 0));

$patient = new CPatient();

$ds = $patient->_spec->ds;

// fields
$fields = array(
  "CPatient" => array(
    "sexe" => "=", 
    "_age_min" => null, 
    "_age_max" => null, 
    "medecin_traitant" => null,
    "patient_id" => "="
  ),
  "CAntecedent" => array(
    "rques" => "LIKE", 
  ),
  "CTraitement" => array(
    "traitement" => "LIKE", 
  ),
  "CConsultation" => array(
    "motif" => "LIKE", 
    "_rques_consult" => null,
    "_examen_consult" => null,
    //"_traitement_consult" => null,
    "conclusion" => "LIKE",
  ),
  "CSejour" => array(
    "type" => "=", 
    "convalescence" => "LIKE", 
    "_rques_sejour" => null,
    "entree" => null,
    "sortie" => null,
    "libelle" => "LIKE",
  ),
  "COperation" => array(
    "materiel" => "LIKE", 
    "exam_per_op" => "LIKE",
    "examen" => "LIKE",
    //"rques" => "LIKE",
    "_libelle_interv" => null,
    "codes_ccam" => null,
    "_rques_interv" => null,
  ),
);

$one_field_presc = $code_cis || $code_ucd || $libelle_produit || $classes_atc || $composant || $indication || $commentaire;

$codes_cis = array();

// Si la recherche concerne un produit, on recherche les codes cis correpondant
if ($libelle_produit) {
  $medicament = new CMedicamentProduit();
  $produits = $medicament->searchProduitAutocomplete($libelle_produit, "100", null, 0, 0, 1, null);
  $codes_cis = array_unique(CMbArray::pluck($produits, "code_cis"));
}

$one_field = false || $one_field_presc;
$one_field_traitement = false;
$one_field_atcd = false;
$sejour_filled  = false;
$consult_filled = false;
$interv_filled  = false;

$from = null;
$to   = null;
$data = array();

foreach ($fields as $_class => $_fields) {
  $data[$_class] = array_intersect_key($_GET, $_fields);
  $object = new $_class;
  $prefix = $object->_spec->table;
  
  foreach ($data[$_class] as $_field => $_value) {
    CValue::setSession($_field, $_value);
    
    if ( $_value !== "" ) {
      $one_field = true;
    }
    
    if ( $_value === "" || !$_fields[$_field]) {
      continue;
    }
    
    switch ($_fields[$_field]) {
      case "=":
        $where["$prefix.$_field"] = $ds->prepare(" = % ", $_value);
        break;
      default:
        $where["$prefix.$_field"] = $ds->prepareLike("%$_value%");
    }
  }
}

switch ($section) {
  case "consult":
    $consult_data = $data["CConsultation"];
    $sejour_data = $data["CSejour"];

    if (
        empty($consult_data["motif"]) &&
        empty($consult_data["_rques_consult"]) &&
        empty($consult_data["_examen_consult"]) &&
        empty($consult_data["conclusion"]) &&
        empty($sejour_data["entree"]) &&
        empty($sejour_data["sortie"]) &&
        !$one_field_presc
    ) {
      break;
    }

    $consult_filled = true;
    $ljoin["patients"] = "patients.patient_id = consultation.patient_id";
    $ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";
    
    $where["plageconsult.chir_id"] = " = '$user_id'";
    
    // CConsultation ---------------------------
    $consult_data = $data["CConsultation"];
    
    if (!empty($consult_data["_rques_consult"])) {
      $where["consultation.rques"] = $ds->prepareLike("%{$consult_data['_rques_consult']}%");  
    }
    
    if (!empty($consult_data["_examen_consult"])) {
      $where["consultation.examen"] = $ds->prepareLike("%{$consult_data['_examen_consult']}%");
    }
    
    if (!empty($sejour_data["_rques_sejour"])) {
      $where["sejour.rques"] = $ds->prepareLike("%{$sejour_data['_rques_sejour']}%");
    }

    $from = CMbDT::date($sejour_data['entree']);
    $to   = CMbDT::date($sejour_data['sortie']);

    if (!empty($sejour_data["entree"])) {
      // Début et fin
      if (!empty($sejour_data["sortie"])) {
        $where["plageconsult.date"] = "BETWEEN '$from' AND '$to'";
      }
      // Début
      else {
        $where["plageconsult.date"] = ">= '$from'";
      }
    }
    // Fin
    else if (!empty($sejour_data["sortie"])) {
      $where["plageconsult.date"] = "< '$to'";
    }

    $data_patient = $data["CPatient"];
    if (!empty($data_patient["_age_min"])) {
      $where[] = "DATEDIFF(plageconsult.date, patients.naissance)/365 > {$data_patient['_age_min']}";
    }
    if (!empty($data_patient["_age_max"])) {
      $where[] = "DATEDIFF(plageconsult.date, patients.naissance)/365 <= {$data_patient['_age_max']}";
    }

    break;
  case "sejour":
    $sejour_data = $data["CSejour"];
    
    if (
        empty($sejour_data["libelle"]) &&
        empty($sejour_data["type"]) &&
        empty($sejour_data["_rques_sejour"]) &&
        empty($sejour_data["convalescence"]) &&
        empty($sejour_data["entree"]) &&
        empty($sejour_data["sortie"]) &&
        !$one_field_presc
    ) {
      break;
    }
    
    $sejour_filled = true;
    $ljoin["patients"] = "patients.patient_id = sejour.patient_id";

    $where["sejour.praticien_id"] = "= '$user_id'";
    
    // CSejour ----------------------------
    if (!empty($sejour_data["_rques_sejour"])) {
      $where["sejour.rques"] = $ds->prepareLike("%{$sejour_data['_rques_sejour']}%");
    }
    if (!empty($sejour_data["entree"])) {
      $where["sejour.sortie"] = ">  '{$sejour_data['entree']}'";
    }
    if (!empty($sejour_data["sortie"])) {
      $where["sejour.entree"] = "<  '{$sejour_data['sortie']}'";
    }
    $ljoin["dossier_medical"] = "dossier_medical.object_id = sejour.sejour_id";
    $where[] = "dossier_medical.object_class = 'CSejour' OR dossier_medical.dossier_medical_id IS NULL";
    
    $data_patient = $data["CPatient"];
    if (!empty($data_patient["_age_min"])) {
      $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 > {$data_patient['_age_min']}";
    }
    if (!empty($data_patient["_age_max"])) {
      $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 <= {$data_patient['_age_max']}";
    }
    break;
  case "operation":
    // COperations ---------------------------
    $interv_data = $data["COperation"];
    $sejour_data = $data["CSejour"];

    if (
        empty($interv_data["_libelle_interv"]) &&
        empty($interv_data["_rques_interv"]) &&
        empty($interv_data["examen"]) &&
        empty($interv_data["materiel"]) &&
        empty($interv_data["exam_per_op"]) &&
        empty($interv_data["codes_ccam"]) &&
        empty($sejour_data["entree"]) &&
        empty($sejour_data["sortie"]) &&
        !$one_field_presc
    ) {
      break;
    }
    
    $interv_filled = true;
    
    $ljoin["sejour"] = "operations.sejour_id = sejour.sejour_id";
    $ljoin["patients"] = "patients.patient_id = sejour.patient_id";
    
    if (!empty($interv_data["codes_ccam"])) {
      $codes = preg_split("/[\s,]+/", $interv_data["codes_ccam"]);
      
      $where_code = array();
      foreach ($codes as $_code) {
        $where_code[] = "operations.codes_ccam ".$ds->prepareLike("%$_code%");
      }
      
      $where[] = implode(" AND ", $where_code);
    }
    if (!empty($interv_data["_rques_interv"])) {
      $where["operations.rques"] = $ds->prepareLike("%{$interv_data['_rques_interv']}%");
    }
    if (!empty($interv_data["_libelle_interv"])) {
      $where["operations.libelle"] = $ds->prepareLike("%{$interv_data['_libelle_interv']}%");
    }
    $where[] = "operations.chir_id = '$user_id' OR operations.chir_id IS NULL";
    $where[] = "operations.annulee = '0' OR operations.annulee IS NULL";
    

    if (!empty($sejour_data["entree"])) {
      $from = CMbDT::date($sejour_data['entree']);
      $where[] = "operations.date  >= '$from'";
    }
    if (!empty($sejour_data["sortie"])) {
      $to = CMbDT::date($sejour_data['sortie']);
      $where[] = "operations.date  < '$to'";
    }
    
    $data_patient = $data["CPatient"];
    if (!empty($data_patient["_age_min"])) {
      $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 > {$data_patient['_age_min']}";
    }
    if (!empty($data_patient["_age_max"])) {
      $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 <= {$data_patient['_age_max']}";
    }
}

// CPatient ---------------------------
$data_patient = $data["CPatient"];

if (!$one_field_presc && !$sejour_filled && !$consult_filled && !$interv_filled) {
  if (!empty($data_patient["_age_min"])) {
    $where[] = "DATEDIFF('".CMbDT::dateTime() . "', patients.naissance)/365 > {$data_patient['_age_min']}";
  }
  if (!empty($data_patient["_age_max"])) {
    $where[] = "DATEDIFF('".CMbDT::dateTime() . "', patients.naissance)/365 <= {$data_patient['_age_max']}";
  }
}

if (!empty($data_patient["medecin_traitant"])) {
  $one_field = true;
  $medecin_traitant_id = $data_patient["medecin_traitant"];
  if (CValue::get("only_medecin_traitant")) {
    $where[] = "patients.medecin_traitant = '$medecin_traitant_id'";
  }
  else {
    $where[] = "patients.medecin_traitant = '$medecin_traitant_id' OR 
                correspondant.medecin_id = '$medecin_traitant_id'";
    $ljoin["correspondant"] = "patients.patient_id = correspondant.patient_id";
  }
}

// CAntecedent ---------------------------
$dm_data = $data["CAntecedent"];

if (!empty($dm_data["rques"])) {
  $ljoin["dossier_medical"] = "dossier_medical.object_id = patients.patient_id";
  $ljoin["antecedent"] = "antecedent.dossier_medical_id = dossier_medical.dossier_medical_id";
  $where[] = "dossier_medical.object_class = 'CPatient' OR dossier_medical.dossier_medical_id IS NULL";
  $one_field_atcd = true;
}

// CTraitement ---------------------------
$traitement_data = $data["CTraitement"];

if (!empty($traitement_data["traitement"])) {
  $ljoin["dossier_medical"] = "dossier_medical.object_id = patients.patient_id";
  $ljoin["traitement"] = "traitement.dossier_medical_id = dossier_medical.dossier_medical_id";
  $where[] = "dossier_medical.object_class = 'CPatient' OR dossier_medical.dossier_medical_id IS NULL";
  $one_field_traitement = true;
}

$list_patient = array();
$count_patient = 0;

$rjoinMed = array();
$rjoinMix = array();
$whereMed = array();
$whereMix = array();

// CPrescription ----------------------------
if ($one_field_presc) {
  
  $one_field_presc = true;
  $one_field = true;
  switch ($section) {
    case "consult":
      $ljoin["prescription"] = "prescription.object_class = 'CConsultation' AND prescription.object_id = consultation.consultation_id";
      $where["prescription.type"] = "= 'externe'";
      break;
    case "sejour":
    case "operation":
      $ljoin["prescription"] = "prescription.object_class = 'CSejour' AND prescription.object_id = sejour.sejour_id";
      $where["prescription.type"] = "IN ('pre_admission', 'sejour', 'sortie')";
  }
  
  $rjoinMed["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
  
  if (!$commentaire) {
    $rjoinMix["prescription_line_mix"] = "prescription_line_mix.prescription_id = prescription.prescription_id";
    $rjoinMix["prescription_line_mix_item"] =
      "prescription_line_mix_item.prescription_line_mix_id = prescription_line_mix.prescription_line_mix_id";
  }

  $whereMed[] = "prescription_line_medicament.active = '1'";
  $whereMix[] = "prescription_line_mix.active = '1'";

  if ($code_cis) {
    $whereMed[] = "prescription_line_medicament.code_cis = '$code_cis'";
    $whereMix[] = "prescription_line_mix_item.code_cis = '$code_cis'";
  }
  else if ($code_ucd) {
    $whereMed[] = "prescription_line_medicament.code_ucd = '$code_ucd'".
      $whereMix[] = "prescription_line_mix_item.code_ucd = '$code_ucd'";
  }
  elseif ($libelle_produit) {
    $whereMed[] = "prescription_line_medicament.code_cis " . CSQLDataSource::prepareIn($codes_cis);
    $whereMix[] = "prescription_line_mix_item.code_cis " . CSQLDataSource::prepareIn($codes_cis);
  }
  else if ($classes_atc) {
    $whereMed[] = "prescription_line_medicament.atc RLIKE '(^$classes_atc)'";
    $whereMix[] = "prescription_line_mix_item.atc RLIKE '(^$classes_atc)'";
  }
  else if ($composant) {
    $composition_bdm = new CMedicamentComposant();
    $produits = $composition_bdm->loadListProduits($composant);
    switch (CMedicament::getBase()) {
      default:
      case "bcb":
        $codes_cip = @CMbArray::pluck($produits, "code_cip");
        $whereMed[] = "prescription_line_medicament.code_cip " . CSQLDataSource::prepareIn($codes_cip);
        $whereMix[] = "prescription_line_mix_item.code_cip ".CSQLDataSource::prepareIn($codes_cip);
        break;
      case "vidal":
        $codes_cis = @CMbArray::pluck($produits, "code_cis");
        $whereMed[] = "prescription_line_medicament.code_cis " . CSQLDataSource::prepareIn($codes_cis);
        $whereMix[] = "prescription_line_mix_item.code_cis ".CSQLDataSource::prepareIn($codes_cis);
    }
  }
  else if ($indication) {
    switch (CMedicament::getBase()) {
      default:
      case "bcb":
        $bcb_indication = new CBcbIndication();
        $produits = $bcb_indication->searchProduits($indication, $type_indication);
        $codes_cip = CMbArray::pluck($produits, "Code_CIP");
        $whereMed[] = "prescription_line_medicament.code_cip " . CSQLDataSource::prepareIn($codes_cip);
        $whereMix[] = "prescription_line_mix_item.code_cip ".CSQLDataSource::prepareIn($codes_cip);
        break;
      case "vidal":
        $indication_bdm = new CMedicamentIndication();
        $produits = $indication_bdm->loadListProduits($indication);
        $codes_cis = @CMbArray::pluck($produits, "code_cis");
        $whereMed[] = "prescription_line_medicament.code_cis " . CSQLDataSource::prepareIn($codes_cis);
        $whereMix[] = "prescription_line_mix_item.code_cis ".CSQLDataSource::prepareIn($codes_cis);
    }

  }
  else if ($commentaire) {
    $whereMed["prescription_line_medicament.commentaire"] = "LIKE '%".addslashes($commentaire)."%'";
  }
}

$list_objects = array();

if ($one_field) {
  // Pour la recherche sur la prescription :
  // deux passages obligés (une pour les lignes de médicament, l'autre pour les lignes de prescription)
  
  $other_fields = "";
  
  if ($one_field_presc) {
    $other_fields = ", prescription_line_medicament.prescription_line_medicament_id";
  }
  
  if ($one_field_atcd) {
    $other_fields .= ", antecedent.antecedent_id";
  }
  
  if ($one_field_traitement) {
    $other_fields .= ", traitement.traitement_id";
  }
  
  // Première requête (éventuellement pour les lignes de médicament)
  $request = new CRequest();
  
  if ($consult_filled) {
    $request->addSelect("consultation.consultation_id, patients.patient_id" . $other_fields);
    $request->addTable("consultation");
    $request->addOrder("patients.nom ASC, plageconsult.date ASC");
  }
  elseif ($sejour_filled) {
    $request->addSelect("sejour.sejour_id, patients.patient_id" . $other_fields);
    $request->addTable("sejour");
    $request->addOrder("patients.nom ASC, sejour.entree_prevue ASC");
  }
  elseif ($interv_filled) {
    $request->addSelect("operations.operation_id, patients.patient_id" . $other_fields);
    $request->addTable("operations");
    $request->addOrder("patients.nom ASC, operations.date ASC");
  }
  else {
    $request->addSelect("patients.patient_id");
    $request->addTable("patients");
    $request->addOrder("patients.nom ASC");
  }
  $request->addLJoin($ljoin);
  $request->addRJoin($rjoinMed);
  $request->addWhere($where);
  $request->addWhere($whereMed);
  
  if (!$export) {
    $request->setLimit("$start,30");
  }

  $results = $ds->loadList($request->makeSelect());

  // Eventuelle deuxième requête (pour les lines mixes)
  if (!$commentaire && $one_field_presc) {
    $request_b = new CRequest();
    
    if ($one_field_presc) {
      $other_fields = ", prescription_line_mix_item.prescription_line_mix_item_id";
    }
    
    if ($one_field_atcd) {
      $other_fields .= ", antecedent.antecedent_id";
    }
    
    if ($one_field_traitement) {
      $other_fields .= ", traitement.traitement_id";
    }
    
    if ($consult_filled) {
      $request_b->addSelect("consultation.consultation_id, patients.patient_id" . $other_fields);
      $request_b->addTable("consultation");
      $request_b->addOrder("patients.nom ASC, plageconsult.date ASC");
    }
    elseif ($sejour_filled) {
      $request_b->addSelect("sejour.sejour_id, patients.patient_id" . $other_fields);
      $request_b->addTable("sejour");
      $request_b->addOrder("patients.nom ASC, sejour.entree_prevue ASC");
    }
    elseif ($interv_filled) {
      $request_b->addSelect("operations.operation_id, patients.patient_id" . $other_fields);
      $request_b->addTable("operations");
      $request_b->addOrder("patients.nom ASC, operations.date ASC");
    }
    else {
      $request_b->addSelect("patients.patient_id");
      $request_b->addTable("patients");
      $request_b->addOrder("patients.nom ASC");
    }
    
    $request_b->addLJoin($ljoin);
    $request_b->addRJoin($rjoinMix);
    $request_b->addWhere($where);
    $request_b->addWhere($whereMix);
    
    if (!$export) {
      $request_b->setLimit("$start,30");
    }
    
    $results = array_merge($results, $ds->loadList($request_b->makeSelect()));
  }
  
  
  foreach ($results as $_result) {
    $_patient_id = $_result["patient_id"];
    $pat = new CPatient();
    $pat->load($_patient_id);
    
    // Recherche sur un antécédent
    if (isset($_result["antecedent_id"])) {
      $_atcd = new CAntecedent();
      $_atcd->load($_result["antecedent_id"]);
      $pat->_ref_antecedent = $_atcd;
    }
    else {
      // On affiche tous les antécédents du patient
      $dossier_medical = $pat->loadRefDossierMedical(false);
      $pat->_refs_antecedents = $dossier_medical->loadRefsAntecedents();
      $pat->_refs_allergies   = $dossier_medical->loadRefsAllergies();
      $pat->_ext_codes_cim    = $dossier_medical->_ext_codes_cim;

    }
    
    if (isset($_result["prescription_line_medicament_id"])) {
      $line = new CPrescriptionLineMedicament();
      $line->load($_result["prescription_line_medicament_id"]);
      $pat->_distant_line = $line;
    }
    else if (isset($_result["prescription_line_mix_item_id"])) {
      $line = new CPrescriptionLineMixItem();
      $line->load($_result["prescription_line_mix_item_id"]);
      $pat->_distant_line = $line;
    }
    if ($sejour_filled) {
      $sejour = new CSejour();
      $sejour->load($_result["sejour_id"]);
      $pat->_distant_object = $sejour;
      $pat->_age_epoque = intval(CMbDT::daysRelative($pat->naissance, $sejour->entree)/365);
    }
    else if ($consult_filled) {
      $consult = new CConsultation();
      $consult->load($_result["consultation_id"]);
      $pat->_distant_object = $consult;
      $consult->loadRefPlageConsult();
      $pat->_age_epoque = intval(CMbDT::daysRelative($pat->naissance, $consult->_ref_plageconsult->date)/365);
    }
    else if ($interv_filled) {
      $interv = new COperation();
      $interv->load($_result["operation_id"]);
      $interv->loadRefPlageOp();
      $pat->_distant_object = $interv;
      $pat->_age_epoque = intval(CMbDT::daysRelative($pat->naissance, $interv->_datetime_best)/365);
    }
    $list_patient[] = $pat;
  }
  
  // Le count total
  $request->select = array("count(*)");
  $request->limit = null;
  $count_patient = $ds->loadResult($request->makeSelect());
  
  if (!$commentaire && $one_field_presc) {
    $request_b->select = array("count(*)");
    $request_b->limit = null;
    $count_patient += $ds->loadResult($request_b->makeSelect());
  }
}

if ($export) {
  $csv = new CCSVFile();

  $titles = array(
    "Patient",
    "Age à l'époque",
    "Dossier Médical",
    "Evenement",
    "Prescription",
    "DCI",
    "Code ATC",
    "Libellé ATC",
    "Commentaire / Motif"
  );
  $csv->writeLine($titles);
  
  foreach ($list_patient as $_patient) {
    
    $dossier_medical = "";
    
    if (isset($_patient->_ref_antecedent)) {
      $dossier_medical .= "Antécédents :\n $_patient->_ref_traitement->_view";
    }
    elseif (isset($_patient->_refs_antecedents) && count($_patient->_refs_antecedents)) {
      $dossier_medical .= "Antécédents :\n";
      foreach ($_patient->_refs_antecedents as $_antecedent) {
        if ($_antecedent->type == "alle") {
          continue;
        }
        $dossier_medical .= $_antecedent->_view . "\n";
      }
    }
    
    if (isset($_patient->_refs_allergies) && count($_patient->_refs_allergies)) {
      $dossier_medical .= "Allergies :\n";
      foreach ($_patient->_refs_allergies as $_allergie) {
        $dossier_medical .= $_allergie->_view . "\n";
      }
    }

    if (isset($_patient->_ext_codes_cim) && count($_patient->_ext_codes_cim)) {
      $dossier_medical .= "Diagnosctics CIM:\n";
      foreach ($_patient->_ext_codes_cim as $_ext_code_cim) {
        $dossier_medical .= "$_ext_code_cim->code: $_ext_code_cim->libelle \n";
      }
    }
    
    $object_view = "";
    
    if (isset($_patient->_distant_object)) {
      $object = $_patient->_distant_object;
      switch (get_class($object)) {
        case "CConsultation":
          $object_view = "Consultation du " . CMbDT::dateToLocale($object->_ref_plageconsult->date) .
            " à ".CMbDT::format($object->heure, "%Hh:%M");
          break;
        case "CSejour":
          $object_view = "Séjour du " .
            CMbDT::dateToLocale(CMbDT::date($object->entree)) . "au " .
            CMbDT::dateToLocale(CMbDT::date($object->sortie));
          break;
        case "COperation":
          $object_view = "Intervention du " . CMbDT::dateToLocale(CMbDT::date($object->_datetime_best));
      }
    }
    
    $content_line = "";
    
    if (isset($_patient->_distant_line)) {
      $content_line = $_patient->_distant_line;
    }

    $data_line = array(
      $_patient->_view . " (".strtoupper($_patient->sexe).")",
      $_patient->_age_epoque,
      $dossier_medical,
      $object_view,
      $content_line->_view,
      $content_line->_ref_produit->_dci_view,
      $content_line->_ref_produit->_ref_ATC_5_code,
      $content_line->_ref_produit->_ref_ATC_5_libelle,
      $content_line->commentaire
    );

    $csv->writeLine($data_line);
  }
  
  $csv->stream("recherche_dossiers_clinique");
  CApp::rip();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("one_field"      , $one_field);
$smarty->assign("one_field_presc", $one_field_presc);
$smarty->assign("start"          , $start);
$smarty->assign("user_id"        , $user_id);
$smarty->assign("list_patient"   , $list_patient);
$smarty->assign("count_patient"  , $count_patient);
$smarty->assign("from"           , $from);
$smarty->assign("to"             , $to);

$smarty->display("inc_recherche_dossier_clinique_results.tpl");

