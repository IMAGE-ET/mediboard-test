<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsAdmin();

$ds = CSQLDataSource::get("std");
global $sourcePath, $targetDir, $regCim10, $regCCAM, $alnum, $alpha;

$type = CValue::get("type");

$sourcePath = "modules/dPpmsi/ghm/ghm.tar.gz";
$targetDir = "tmp/ghm";

//Hack pour les accents sous linux
$alnum = "éèêùàûîôï[:alnum:]";
$alpha = "éèêùàûîôï[:alpha:]";

//Reconnaissance d'un code Cim10
$regCim10 = "[{$alpha}][[:digit:]]{2}[.]?[{$alnum}\+\*-]*";

// Reconnaissance d'un code CCAM
$regCCAM = "[{$alpha}]{4}[[:digit:]]{3}";

switch($type) {
  case "extractFiles": extractFiles(); break;  
  case "AddCM"       : addcm(); break;
  case "AddDiagCM"   : adddiagcm(); break;
  case "AddActes"    : addactes(); break;
  case "AddGHM"      : addghm(); break;
  case "AddCMA"      : addcma(); break;
  case "AddIncomp"   : addincomp(); break;
  case "AddArbre"    : addarbre(); break;
  
  default:
  echo "<div class='error'>L'action '$type' n'existe pas";
}

/** 
 * Extraction des fichiers sources de Ajout des CM, 
 * valide pour la version 1010
 **/
function extractFiles() {
  global $sourcePath, $targetDir;
  if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
    CAppUI::stepAjax("Impossible d'extraire l'archive", UI_MSG_ERROR);
  }
  CAppUI::stepAjax("Extraction de $nbFiles fichiers", UI_MSG_OK);
}

/**
 * Ajout des CM, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/CM.txt
 * Ligne sous la forme "XX Nom du CM" 
 **/
function addcm() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");

  $fileName = "$targetDir/CM.txt";

  // Table des CM
  $sql = "DROP TABLE IF EXISTS `cm`;";
  $ds->exec($sql);
  if ($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  
  $sql = "CREATE TABLE `cm` (
    `CM_id` varchar(2) NOT NULL default '0',
    `nom` varchar(100) default NULL,
    PRIMARY KEY  (`CM_id`)
    ) ENGINE=MyISAM
    COMMENT='Table des catégoris majeurs';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'>Table des CM créée</div>";
  }

  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouvé<br>";
    return;
  }
  
  $nCM = 0;

  // Ajout des lignes
  while (!feof($file)) {
    $id = fgets($file, 3);
    fgets($file, 2);
    $nom = fgets($file, 1024);
    $sql = "INSERT INTO cm values('$id', '".addslashes($nom)."');";
    $ds->exec($sql);
    if($error = $ds->error()) {
      echo "<div class='error'>$error ($sql)</div>";
    } else {
      $nCM++;
      //echo "<div class='message'>$id-$nom</div>";
    }
  }
  echo "<div class='message'> $nCM CM créés</div>";
}

/** Ajout des diagnostics d'entrée dans les CM, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/diagCM.txt */
function adddiagcm() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");
  $fileName = "$targetDir/diagCM.txt";
  $sql = "DROP TABLE IF EXISTS `diagcm`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  $sql = "CREATE TABLE `diagcm` (
  `diag` varchar(10) NOT NULL default '0',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`diag`, `CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des diagnostics d\'entree dans les CM';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table des diagnostics d'entrée créée</div>";
  }
  $file = @fopen( $fileName, 'rw' );
  if(! $file) {
    echo "Fichier non trouvé<br>";
    return;
  }
  $curr_cmd = null;
  $nCM = 0;
  $nDiags = 0;
  
  
  while (!feof($file)) {
    $line = fgets($file, 1024);
    $cmd = null;
    $diag = null;
    if(preg_match("`^Diagnostics d'entrée dans la CMD n° ([[:digit:]]{2})`", $line, $cmd)) {
      $curr_cmd = $cmd[1];
      $nCM++;
    } else if(preg_match("`^($regCim10)`", $line, $diag)) {
      $sql = "INSERT INTO diagcm VALUES('".$diag[1]."', '$curr_cmd')";$ds->exec($sql);
      if($error = $ds->error()) {
        echo "<div class='error'>$error ($sql)</div>";
      } else {
        $nDiags++;
        //echo "<div class='message'> ".$diag[1]." ($curr_cmd)</div>";
      }
    }
  }
  echo "<div class='message'> $nDiags diagnostics créés dans $nCM CM</div>";
}

/** Ajout des listes d'actes, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/Actes.txt
 * Ligne sous la forme
 * "CMD XX"
 * "Liste AouD-XXX : nom"
 * "CCAMXXX/Phase Libelle" */
function addactes() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");
  $fileName = "$targetDir/Listes.txt";

  $sql = "DROP TABLE IF EXISTS `liste`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  $sql = "CREATE TABLE `liste` (
  `liste_id` varchar(6) NOT NULL default '0',
  `nom` varchar(100) default NULL,
  PRIMARY KEY  (`liste_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des listes';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table des listes créée</div>";
  }
  $sql = "DROP TABLE IF EXISTS `acte`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  $sql = "CREATE TABLE `acte` (
  `code` varchar(7) NOT NULL default '0',
  `phase` varchar(1) NOT NULL default '0',
  `liste_id` varchar(6) NOT NULL default 'A-001',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`code`, `phase`, `liste_id`, `CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des actes';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table des actes créée</div>";
  }
  $sql = "DROP TABLE IF EXISTS `diag`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  $sql = "CREATE TABLE `diag` (
  `code` varchar(7) NOT NULL default '0',
  `liste_id` varchar(6) NOT NULL default 'D-001',
  `CM_id` varchar(2) NOT NULL default '01',
  PRIMARY KEY  (`code`, `liste_id`, `CM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des diagnostics';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table des diagnostics créée</div>";
  }
  $file = @fopen( $fileName, 'rw' );
  if(! $file) {
    echo "Fichier $fileName non trouvé<br>";
    return;
  }
  $curr_cmd = null;
  $curr_liste = null;
  $nCM = 0;
  $nListes = 0;
  $nActes = 0;
  $nDiags = 0;
  while (!feof($file) ) {
    $line = fgets($file, 1024);
    $cmd = null;
    $liste = null;
    $acte = null;
    $diag = null;
    if(preg_match("`^CMD ([[:digit:]]{2})`", $line, $cmd)) {
      $curr_cmd = $cmd[1];
      $nCM++;
    } else if(preg_match("`^Liste ([AD]-[[:digit:]]*) : ([{$alnum}[:space:][:punct:]]*)`", $line, $liste) && $curr_cmd) {
      $curr_liste = $liste[1];
      $sql = "SELECT `liste_id` FROM `liste` WHERE `liste_id` = '".$liste[1]."'";
      $isListe = $ds->loadResult($sql);
      if(!$isListe) {
        $sql = "INSERT INTO liste VALUES('".$liste[1]."', '".addslashes($liste[2])."')";
        $ds->exec($sql);
      }
      if($error = $ds->error()) {
        echo "<div class='error'>$error ($sql)</div>";
      } else {
        $nListes++;
        //echo "<div class='message'> ".$liste[1]." : ".$liste[2]." (".$curr_cmd.")</div>"; 
      }
    } else if(preg_match("`^($regCCAM)/([[:digit:]])`", $line, $acte) && $curr_liste) {
      $sql = "INSERT INTO acte VALUES('".$acte[1]."', '".$acte[2]."', '$curr_liste', '$curr_cmd')";
      $ds->exec($sql);
      if($error = $ds->error()) {
        echo "<div class='error'>$error ($sql)</div>";
      } else {
        $nActes++;
        //echo "<div class='message'> ".$acte[1]."/".$acte[2]." ($curr_liste, CMD $curr_cmd)</div>";
      }
    } else if(preg_match("`^($regCim10)`", $line, $diag) && $curr_liste) {
      $sql = "INSERT INTO diag VALUES('".$diag[1]."', '$curr_liste', '$curr_cmd')";
      $ds->exec($sql);
      if($error = $ds->error()) {
        //echo "<div class='error'>$error ($sql)</div>";
      } else {
        $nDiags++;
        //echo "<div class='message'> ".$diag[1]." ($curr_liste, CMD $curr_cmd)</div>";
      }
    }
  }
  // Cas de la liste des actes medicaux reclassants dans un GHM médical
  $fileName = "$targetDir/Actes_Med.txt";
  $file = @fopen( $fileName, 'rw' );
  if(! $file) {
    echo "Fichier $fileName non trouvé<br>";
    return;
  }
  $sql = "INSERT INTO liste VALUES('A-med', 'Actes reclassant dans un GHM médical')";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    $nListes++;
  }
  while (!feof($file) ) {
    $line = fgets($file, 1024);
    if(preg_match("`^($regCCAM)/([[:digit:]])`", $line, $acte) && $curr_liste) {
      $sql = "INSERT INTO acte VALUES('".$acte[1]."', '".$acte[2]."', 'A-med', '99')";
      $ds->exec($sql);
      if($error = $ds->error()) {
        echo "<div class='error'>$error ($sql)</div>";
      } else {
        $nActes++;
        //echo "<div class='message'> ".$acte[1]."/".$acte[2]." ($curr_liste, CMD $curr_cmd)</div>";
      }
    }
  }
  echo "<div class='message'> $nCM CM trouvés et $nListes listes, $nActes actes et $nDiags diagnostics créés</div>";
}

/** Ajout des GHM, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/GHM.txt */
function addghm() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");
  
  // Table des GHM
  $fileName = "$targetDir/GHM.txt";
  $sql = "DROP TABLE IF EXISTS `ghm`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  $sql = "CREATE TABLE `ghm` (
  `GHM_id` varchar(6) NOT NULL default '0',
  `nom` text default NULL,
  `groupe` varchar(100) NOT NULL default 'groupes chirurgicaux',
  `CM_id` varchar(2) NOT NULL default '01',
  `GHS` int(2) default NULL,
  `borne_basse` int(1) default NULL,
  `borne_haute` int(1) default NULL,
  `tarif_2006` float default NULL,
  `EXH` float default NULL,
  PRIMARY KEY  (`GHM_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des groupements homogènes de malades';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table des GHM créée</div>";
  }

  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouvé<br>";
    return;
  }
  
  $nGHM = 0;
  $curr_CM = null;
  $curr_group = null;

  // Ajout des lignes
  while (!feof($file)) {
    $line = fgets($file, 1024);
    $cm = null;
    $groupe = null;
    $GHM = null;
    if(preg_match("`^CATÉGORIE MAJEURE DE DIAGNOSTIC : ([[:digit:]]{2})`", $line, $cm)) {
      $curr_CM = $cm[1];
      //echo "<div class='message'> Curr_CM = $curr_CM</div>";
    } else if(preg_match("`^Groupes ([{$alnum}[:space:][:punct:]]*)`", $line, $groupe)) {
      $curr_group = $groupe[1];
      //echo "<div class='message'> Curr_groupe = $curr_groupe</div>";
    } else if(preg_match("`^([[:digit:]]{2}[{$alpha}][[:digit:]]{2}[{$alpha}]) ([{$alnum}[:space:][:punct:]]*)`", $line, $GHM)) {
      $sql = "INSERT INTO ghm" .
          "\nvalues('".addslashes($GHM[1])."', '".addslashes($GHM[2])."'," .
          "\n'".addslashes($curr_group)."', '".addslashes($curr_CM)."'," .
          "\nnull, null, null, null, null);";
      $ds->exec($sql);
      if($error = $ds->error()) {
        echo "<div class='error'>$error ($sql)</div>";
      } else {
        //echo "<div class='message'> ".$id."-".$nom."</div>";
        $nGHM++;
      }
    }
  }
  echo "<div class='message'> $nGHM GHM créés</div>";
  
  // Ajout des tarifs
  $fileName = "$targetDir/tarifsGHS.csv";
  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier des tarifs non trouvé<br>";
    return;
  }
  
  $nPass = 0;
  $nFailed = 0;

  $trans = array(
    "\n" => "",
    "\r" => "",
    ";;" => ";'';");
  $trans2 = array(
    "'" => "",
    "," => ".");
  $line = fgets($file, 1024);
  // Ajout des lignes
  while (!feof($file)) {
    $line = fgets($file, 1024);
    $line = strtr($line, $trans);
    $line = strtr($line, $trans);
    if(substr($line, -1, 1) == ";")
      $line .= "''";
    $result = explode(";", $line);
    $sql = "UPDATE ghm SET" .
        "\nGHS = '".strtr($result[0], $trans2)."'," .
        "\nborne_basse = '".strtr($result[3], $trans2)."'," .
        "\nborne_haute = '".strtr($result[4], $trans2)."'," .
        "\ntarif_2006 = '".strtr($result[5], $trans2)."'," .
        "\nEXH = '".strtr($result[6], $trans2)."'" .
        "\nWHERE GHM_id = '".strtr($result[1], $trans2)."';";
    $ds->exec($sql);
    if($error = $ds->error()) {
      echo "<div class='error'>$error ($sql)</div>";
      $nFailed++;
    } else {
      //echo "<div class='message'> $line</div>";
      $nPass++;
    }
  }
  echo "<div class='message'> $nPass tarifs créés, $nFailed échoués</div>";
}

/** Ajout des CMA, valide pour la version 1010
 * Fichiers texte :
 * ./modules/dPpmsi/ghm/cma.txt
 * ./modules/dPpmsi/ghm/cmas.txt
 * ./modules/dPpmsi/ghm/cmasnt.txt */
function addcma() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");
  
  // Table des Complications et Morbidités Associées, CMA Sévères et CMAS Non Traumatiques
  $listCM = array("cma", "cmas", "cmasnt");
  foreach($listCM as $typeCM) {
    //$typeCM = "cma";
    $sql = "DROP TABLE IF EXISTS `$typeCM`;";
    $ds->exec($sql);
    if($error = $ds->error()) {
      echo "<div class='error'>$error ($sql)</div>";
    }
    $sql = "CREATE TABLE `$typeCM` (
    `".$typeCM."_id` varchar(10) NOT NULL default '0',
    PRIMARY KEY  (`".$typeCM."_id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des $typeCM';";
    $ds->exec($sql);
    if($error = $ds->error()) {
      echo "<div class='error'>$error ($sql)</div>";
    } else {
      echo "<div class='message'> Table des $typeCM créée</div>";
    }
  
    $fileName = "$targetDir/$typeCM.txt";
    // Lecture du fichier
    $file = @fopen($fileName, 'rw');
    if(!$file) {
      echo "Fichier non trouvé<br>";
      return;
    }
    
    $nombre = 0;
  
    // Ajout des lignes
    while (!feof($file)) {
      $line = fgets($file, 1024);
      $CMA = null;
      if(preg_match("`^($regCim10)`", $line, $CMA)) {
        $sql = "INSERT INTO $typeCM values('$CMA[1]');";
        $ds->exec($sql);
        if($error = $ds->error()) {
          echo "<div class='error'>$error ($sql)</div>";
        } else {
          $nombre++;
        }
      }
    }
    echo "<div class='message'> $nombre $typeCM créés</div>";
  }
}

/** Ajout des incompatibilités entre DP - CMA, valide pour la version 1010
 * Fichier texte : ./modules/dPpmsi/ghm/incomp.txt */
function addincomp() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");
  
  // Table des incompatibilités
  $sql = "DROP TABLE IF EXISTS `incomp`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }
  $sql = "CREATE TABLE `incomp` (
  `CIM1` varchar(10) NOT NULL default '0',
  `CIM2` varchar(10) NOT NULL default '0',
  PRIMARY KEY  (`CIM1`, `CIM2`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des incompatibilités DP - CMA';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table des incompatibilités créée</div>";
  }

  $fileName = "$targetDir/incomp.txt";
  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouvé<br>";
    return;
  }
  
  $nIncomp = 0;
  $baseCode = null;
  $n = 0;

  // Ajout des lignes
  $tabIncomp = array();
  while (!feof($file)) {
    $line = fgets($file, 1024);
    $incomp = null;
    $duplicata = null;
    // A t'on au moins un code au début
    if(preg_match_all("`$regCim10`", $line, $incomp)) {
      $listIncomp = $incomp[0];
      // A t'on plus d'un code ?
      if(count($listIncomp) > 1) {
        // Sommes nous en début de liste ?
        if($listIncomp[0] >= $listIncomp[1]) {
          $baseCode = $listIncomp[0];
          foreach($listIncomp as $place => $code) {
            if($place > 0) {
              $tabIncomp[$baseCode][] = $code;
            }
          }
        } else {
          foreach($listIncomp as $place => $code) {
            $tabIncomp[$baseCode][] = $code;
          }
        }
      // A t'on une liste dupliquée ?
      } else if(preg_match("`même liste que (".$regCim10.")`", $line, $duplicata)){
        $baseCode = $listIncomp[0];
        $copy = $duplicata[1];
        $tabIncomp[$baseCode] = $tabIncomp[$copy];
      } else {
        $tabIncomp[$baseCode][] = $listIncomp[0];
      }
    }
    $n++;
  }
  //Remplissage de la base
  foreach($tabIncomp as $baseCode => $liste) {
    foreach($liste as $code) {
      $sql = "INSERT INTO incomp VALUES('$baseCode', '$code');";
      $ds->exec($sql);
      if($error = $ds->error()) {
        echo "<div class='error'>$error ($sql)</div>";
      } else {
        $nIncomp++;
      }
    }
  }
  echo "<div class='message'> $nIncomp incompatibilités créées</div>";
}

/** Création de l'arbre de décision pour l'orientation vers les GHM
 * valide pour la version 1010
 * Fichier CSV : ./modules/dPpmsi/ghm/arbreGHM.csv
 * première ligne      : nom des colonnes
 * séparateur          : ',' (virgule)
 * séparateur du texte : ''' (simple guillemet)
 */

function addarbre() {
  global $regCim10, $regCCAM, $alnum, $alpha, $targetDir;
  $ds = CSQLDataSource::get("GHS1010");
  
  // Table des incompatibilités
  $sql = "DROP TABLE IF EXISTS `arbre`;";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  }

  $fileName = "$targetDir/arbreGHM.csv";
  // Lecture du fichier
  $file = @fopen($fileName, 'rw');
  if(!$file) {
    echo "Fichier non trouvé<br>";
    return;
  }
  
  $line = fgets($file, 1024);
  $trans = array(
      "'" => "",
      "\n" => "",
      "\r" => "");
  $line = strtr($line, $trans);
  $columns = explode(",", $line);
  
  $sql = "CREATE TABLE `arbre` (" .
      "\n`arbre_id` INT(11) NOT NULL auto_increment,";
  foreach($columns as $column) {
    $sql .= "\n `$column` VARCHAR(25) DEFAULT NULL,";
  }
  $sql .= "\nPRIMARY KEY (`arbre_id`)," .
      "\nKEY `CM_id` (`CM_id`)" .
      ") ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de l\'arbre de décision pour les GHM';";
  $ds->exec($sql);
  if($error = $ds->error()) {
    echo "<div class='error'>$error ($sql)</div>";
  } else {
    echo "<div class='message'> Table de l'arbre de décision créée</div>";
  }
  
  $nPass = 0;
  $nFailed = 0;

  $trans = array(
    "\n" => "",
    "\r" => "",
    ",," => ",'',");
  // Ajout des lignes
  while (!feof($file)) {
    $line = fgets($file, 1024);
    $line = strtr($line, $trans);
    $line = strtr($line, $trans);
    if(substr($line, -1, 1) == ",")
      $line .= "''";
    $sql = "INSERT INTO arbre" .
        "\nvalues('', $line);";
    $ds->exec($sql);
    if($error = $ds->error()) {
      echo "<div class='error'>$error ($sql)</div>";
      $nFailed++;
    } else {
      //echo "<div class='message'> $line</div>";
      $nPass++;
    }
  }
  echo "<div class='message'> $nPass lignes créés, $nFailed lignes échouées</div>";
}
?>