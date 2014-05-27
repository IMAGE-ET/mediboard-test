<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

CApp::setTimeLimit(360);

$sourcePath = "modules/dPccam/base/ccam.tar.gz";
$targetDir = "tmp/ccam";

$targetTables    = "tmp/ccam/tables.sql";
$targetBaseDatas = "tmp/ccam/basedata.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
}

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");

// Création des tables
if (null == $lineCount = $ds->queryDump($targetTables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Création de $lineCount tables", UI_MSG_OK);

// Ajout des données de base
if (null == $lineCount = $ds->queryDump($targetBaseDatas, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des données de base - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Import des données de base effectué avec succès ($lineCount lignes)", UI_MSG_OK);

// Ajout des fichiers NX dans les tables
$listTablesOld = array(
  "actes"               => "TB101.txt",
  "activite"            => "TB060.txt",
  "activiteacte"        => "TB201.txt",
  "arborescence"        => "TB091.txt",
  "associabilite"       => "TB220.txt",
  "association"         => "TB002.txt",
  "infotarif"           => "TB110.txt",
  "incompatibilite"     => "TB130.txt",
  "modificateur"        => "TB083.txt",
  "modificateuracte"    => "TBCCAM06.txt",
  "modificateurcompat"  => "TB009.txt",
  "modificateurforfait" => "TB011.txt",
  "modificateurnombre"  => "TB019.txt",
  "notes"               => "TBCCAM11.txt",
  "notesarborescence"   => "TB092.txt",
  "phase"               => "TB066.txt",
  "phaseacte"           => "TB310.txt",
  "procedures"          => "TB120.txt",
  "typenote"            => "TB050.txt"
);

$listTables = array(
  // Tables
  "t_modetraitement"         => "TB001.txt",
  "t_association"            => "TB002.txt",
  "t_regletarifaire"         => "TB003.txt",
  "t_regroupementspecialite" => "TB004.txt",
  "t_prestationforfait"      => "TB005.txt",
  "t_modificateurage"        => "TB006.txt",
  "t_seuilexotm"             => "TB007.txt",
  "t_joursferies"            => "TB008.txt",
  "t_modificateurcompat"     => "TB009.txt",
  "t_modificateurcoherence"  => "TB010.txt",
  "t_modificateurforfait"    => "TB011.txt",
  "t_rembtnonconventionnes"  => "TB012.txt",
  "t_natureprestation"       => "TB013.txt",
  "t_disciplinetarifaire"    => "TB014.txt",
  "t_modificateurinfooc"     => "TB015.txt",
  "t_ccamprestationngap"     => "TB017.txt",
  "t_localisationdents"      => "TB018.txt",
  "t_modificateurnombre"     => "TB019.txt",
  "t_conceptsdivers"         => "TB020.txt",
  // Tables de codification
  "c_typenote"               => "TB050.txt",
  "c_conditionsgenerales"    => "TB051.txt",
  "c_classedmt"              => "TB052.txt",
  "c_exotm"                  => "TB053.txt",
  "c_natureassurance"        => "TB054.txt",
  "c_remboursement"          => "TB055.txt",
  "c_fraisdeplacement"       => "TB056.txt",
  "c_typeacte"               => "TB057.txt",
  "c_typeforfait"            => "TB058.txt",
  "c_extensiondoc"           => "TB059.txt",
  "c_activite"               => "TB060.txt",
  "c_categoriemedicale"      => "TB061.txt",
  "c_coderegroupement"       => "TB062.txt",
  "c_categoriespecialite"    => "TB063.txt",
  "c_paiementseances"        => "TB065.txt",
  "c_phase"                  => "TB066.txt",
  "c_dentsincomp"            => "TB067.txt",
  "c_caisseoutremer"         => "TB068.txt",
  // Libellés de concepts présents en tables paramètres TB01 à TB20
  "l_anp"                    => "TB080.txt",
  "l_regletarifaire"         => "TB081.txt",
  "l_specialite"             => "TB082.txt",
  "l_modificateur"           => "TB083.txt",
  "l_dmt"                    => "TB084.txt",
  // Concepts divers
  "c_compatexotm"            => "TB090.txt",
  "c_arborescence"           => "TB091.txt",
  "c_notesarborescence"      => "TB092.txt",
  "c_uniteoeuvre"            => "TB093.txt",
  // Liste des mots (glossaire)
  "g_listemots"              => "TB099.txt",
  // Tables principales
  // - Niveau ACTE
  "p_acte"                   => "TB101.txt",
  "p_acte_infotarif"         => "TB110.txt",
  "p_acte_procedure"         => "TB120.txt",
  "p_acte_incompatibilite"   => "TB130.txt",
  // - Niveau ACTIVITE
  "p_activite"               => "TB201.txt",
  "p_activite_classif"       => "TB210.txt",
  "p_activite_associabilite" => "TB220.txt",
  // - Niveau PHASE
  "p_phase"                  => "TB301.txt",
  "p_phase_acte"             => "TB310.txt",
  "p_phase_acte_comp"        => "TB350.txt",
  // Compléments
  /*"p_activite_extdoc"        => "TBCCAM03.txt",*/
  "p_activite_modificateur"  => "TBCCAM06.txt",
  /*"p_acte_procedure"         => "TBCCAM9_1.txt",*/
  /*"p_acte_condgen"           => "TBCCAM10.txt",*/
  "p_acte_notes"             => "TBCCAM11.txt",
  /*"p_activite_numrecomed"    => "TBCCAM20.txt",*/
  /*"p_activite_recomed"       => "TBCCAM21.txt",*/
  /*"p_acte_codeexotm"         => "TBCCAM25.txt",*/
  /*"p_acte_prescripteur"      => "TBCCAM31.txt",*/
  /*"p_acte_executant"         => "TBCCAM32.txt",*/
  "p_acte_typeforfait"       => "TBCCAM34.txt",
  /*"p_activite_agrementradio" => "TBCCAM40.txt",*/
  /*"p_acte_classedmt"         => "TBCCAM41_1.txt",*/
  "p_phase_dentsincomp"      => "TBCCAM44.txt",
);

/**
 * Ajout des données dans les tables CCAM correspondantes
 *
 * @param CSQLDataSource $ds      Datasource
 * @param string         $table   Nom de la table Table
 * @param array          $values  Tableau de valeurs
 * @param int            &$echoue Nombre de lignes échouées
 * @param int            &$reussi Nombre de lignes réussies
 *
 * @return void
 */
function insertValues($ds, $table, $values, &$echoue, &$reussi) {
  $values_sql = array();
  foreach ($values as $_line) {
    $values_sql[] = "('".implode("','", $_line)."')";
  }

  $query = "INSERT INTO $table VALUES ".implode(",", $values_sql);

  $ds->exec($query);

  $count = count($values);
  if ($msg = $ds->error()) {
    $echoue += $count;
  }
  else {
    $reussi += $count;
  }
}

function addFileIntoDB($file, $table) {
  $reussi = 0;
  $echoue = 0;
  $ds = CSQLDataSource::get("ccamV2");
  $handle = fopen($file, "r");

  $values = array();
  $batch = 50;

  // Ne pas utiliser fgetcsv, qui refuse de prendre en compte les caractères en majusucules accentués (et d'autres caractères spéciaux)
  while ($line = fgets($handle)) {
    $line = str_replace("'", "\'", $line);
    $values[] = explode("|", $line);

    if (count($values) == $batch) {
      insertValues($ds, $table, $values, $echoue, $reussi);
      $values = array();
    }
  }

  if (count($values)) {
    insertValues($ds, $table, $values, $echoue, $reussi);
  }

  CAppUI::stepAjax("Import du fichier $file dans la table $table : $reussi lignes ajoutée(s), $echoue échouée(s)", UI_MSG_OK);
  fclose($handle);
}

foreach ($listTables as $table => $file) {
  addFileIntoDB("$targetDir/$file", $table);
}

