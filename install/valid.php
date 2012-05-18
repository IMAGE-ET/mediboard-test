<?php

// Check the check.php page
if (!(class_exists("CPrerequisite"))) {
  class CPrerequisite {
    var $name = "";
    var $description = "";
    var $mandatory = false;
    var $reason = array();
  
    function check() {
      return false;
    }
  }
  
  class CPearPackage extends CPrerequisite {
    var $status = "stable";
  
    function check() {
      return @include_once("$this->name.php");
    }
  }
  
  class CPHPExtension  extends CPrerequisite {
    function check() {
      return extension_loaded(strtolower($this->name));
    }
  }
  
  class CPHPVersion extends CPrerequisite {
    function check() {
      return phpversion() >= $this->name;
    }
  }
}


// Data from check.php file
$packages = array();

$package = new CPearPackage;
$package->name = "Archive/Tar";
$package->description = "Package de manipulation d'archives au format GNU TAR";
$package->mandatory = true;
$package->reasons[] = "Installation de Mediboard";
$package->reasons[] = "Import des fonctions de GHM";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Config";
$package->description = "Package de manipulation de fichiers de configuration";
$package->mandatory = true;
$package->reasons[] = "Configuration générale de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "DB";
$package->description = "Package de manipulation de base de données";
$package->mandatory = true;
$package->reasons[] = "Assistant d'installation de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Auth";
$package->description = "Package d'authentification multi-support";
$package->mandatory = true;
$package->reasons[] = "Assistant d'installation de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "PHP/CodeSniffer";
$package->description = "Analyseur syntaxique de code source";
$package->status = "beta";
$package->mandatory = false;
$package->reasons[] = "Outil de génie logiciel pour vérifier la qualité du code source de Mediboard";
$packages[] = $package;

/*
$package = new CPearPackage;
$package->name = "phpUnit";
$package->description = "Package de test unitaire";
$package->mandatory = false;
$package->reasons[] = "Tests unitaires et fonctionnels de Mediboard";
$package->reasons[] = "cf. <a href='http://www.phpunit.de/wiki/Documentation' style='display: inline;'>http://www.phpunit.de/wiki/Documentation</a>";
$packages[] = $package;
*/

$extensions = array();

$extension = new CPHPExtension;
$extension->name = "MySQL";
$extension->description = "Extension d'accès aux bases de données MySQL";
$extension->mandatory = true;
$extension->reasons[] = "Accès à la base de donnée de principale Mediboard";
$extension->reasons[] = "Accès aux bases de données de codage CCAM, CIM et GHM";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "MBString";
$extension->description = "Extension de gestion des chaînes de caractères multi-octets";
$extension->mandatory = true;
$extension->reasons[] = "Internationalisation de Mediboard";
$extension->reasons[] = "Interopérabilité Unicode";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "ZLib";
$extension->description = "Extension de compression au format GNU ZIP (gz)";
$extension->mandatory = true;
$extension->reasons[] = "Installation de Mediboard";
$extension->reasons[] = "Accelération substancielle de l'application via une communication web compressée";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "Zip";
$extension->description = "Extension de compression au format zip";
$extension->mandatory = true;
$extension->reasons[] = "Installation de Mediboard";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "JSON";
$extension->description = "Extension de manipulation de données au format JSON. Inclus par défaut avec PHP 5.2+";
$extension->mandatory = true;
$extension->reasons[] = "Passage de données de PHP vers Javascript.";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "DOM";
$extension->description = "Extension de manipulation de fichier XML avec l'API DOM";
$extension->mandatory = true;
$extension->reasons[] = "Import de base de données médecin";
$extension->reasons[] = "Interopérabilité HPRIM XML, notamment pour le PMSI";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "SOAP";
$extension->description = "Extension permettant d'effectuer des requetes";
$extension->reasons[] = "Requetes vers les serveurs de résultats de laboratoire";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "FTP";
$extension->description = "Extension d'accès aux serveur FTP";
$extension->reasons[] = "Envoi HPRIM vers des serveurs de facturation";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "BCMath";
$extension->description = "Extension de calculs sur des nombres de précision arbitraire";
$extension->reasons[] = "Validation des codes INSEE et ADELI";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "CURL";
$extension->description = "Extension permettant de communiquer avec des serveurs distants, grâce à de nombreux protocoles";
$extension->reasons[] = "Connexion au site web du Conseil National l'Ordre des Médecins";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "GD";
$extension->description = "Extension de manipulation d'image. \nGD version 2 est recommandée car elle permet un meilleur rendu, grâce à de nombreux protocoles";
$extension->reasons[] = "Module de statistiques graphiques";
$extension->reasons[] = "Fonction d'audiogrammes";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "PDO";
$extension->description = "Extension de connectivité aux bases de données";
$extension->reasons[] = "Interopérabilité avec des systèmes tiers";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "PDO_ODBC";
$extension->description = "Pilote ODBC pour PDO";
$extension->reasons[] = "Interopérabilité avec des systèmes tiers";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "APC";
$extension->description = "Extension d'optimsation d'OPCODE et de mémoire partagée";
$extension->reasons[] = "Accélération globale du système";
$extensions[] = $extension;

$extension = new CPHPExtension;
$extension->name = "GnuPG";
$extension->description = "GNU Privacy Guard (GPG ou GnuPG)";
$extension->reasons[] = "Transmettre des messages signés et/ou chiffrés";
$extensions[] = $extension;

$versions = array();

// Do not use $version which is a Mediboard global
$php = new CPHPVersion;
$php->name = "5.1";
$php->mandatory = true;
$php->description = "Version de PHP5 récente";
$php->reasons[] = "Intégration du support XML natif : utilisation pour l'intéropérabilité HPRIM XML'";
$php->reasons[] = "Intégration de PDO : accès universel et sécurisé aux base de données";
$php->reasons[] = "Conception objet plus évoluée";
$versions[] = $php;

// Start of checking
$i = 0;
$validCheck = array();

foreach ($versions as $prereq) {
  $a = 0;
  
  if ($prereq->mandatory) {
    $a = 1;
  }
  
  if ($a) {
    $validCheck[$i] = $prereq->check();
    $i++;
  }
}

foreach ($extensions as $prereq) {
  $a = 0;
  
  if ($prereq->mandatory) {
    $a = 1;
  }
  
  if ($a) {
    $validCheck[$i] = $prereq->check();
    $i++;
  }
}

foreach ($packages as $prereq) {
  $a = 0;
  
  if ($prereq->mandatory) {
    $a = 1;
  }
  
  if ($a) {
    $validCheck[$i] = $prereq->check();
    $i++;
  }
}

$valid['check'] = (array_sum($validCheck) == count($validCheck));

unset($validCheck);
unset($packages);
unset($extensions);
unset($versions);
// End of checking

// Check the fileaccess.php page
if (!(class_exists("CPathAccess"))) {
  class CPathAccess {
    var $path = "";
    var $description = "";
    var $reason = array();
  
    function check() {
      global $mbpath;
      return is_writable($mbpath.$this->path);
    }
  }  
}

$pathAccesses = array();

$pathAccess = new CPathAccess;
$pathAccess->path = "tmp/";
$pathAccess->description = "Répertoire des fichiers temporaires";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "files/";
$pathAccess->description = "Répertoire de tous les fichiers attachés";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "lib/";
$pathAccess->description = "Répertoire d'installation des bibliothèques tierces";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "includes/";
$pathAccess->description = "Répertoire du fichier de configuration du système";

$pathAccesses[] = $pathAccess;

$pathAccess = new CPathAccess;
$pathAccess->path = "modules/hprimxml/xsd";
$pathAccess->description = "Répertoire des schemas HPRIM";

$pathAccesses[] = $pathAccess;

// Start of checking
$i = 0;
$validFileAccess = array();

foreach($pathAccesses as $pathAccess) {
  $validFileAccess[$i] = $pathAccess->check();
  $i++;
}

$valid['fileaccess'] = (array_sum($validFileAccess) == count($validFileAccess));

unset($validFileAccess);
unset($pathAccesses);
// End of checking

// Check the install.php page

$mbpath = "../";
if (!file_exists($mbpath."classes/CMbPath.class.php")){
  $mbpath = "./";
}

require_once($mbpath."classes/CMbPath.class.php");
require_once("libs.php");

// Start of checking
$i=0;
$validInstall = array();
foreach(CLibrary::$all as $library) {
  $validInstall[$i] = $library->getUpdateState();
  $i++;
}

$valid['install'] = (array_sum($validInstall) == count($validInstall));

unset($validInstall);
// End of checking

$valid['configure'] = null;
$valid['initialize'] = null;
$valid['feed'] = null;
$valid['finish'] = null;
$valid['phpinfo'] = null;
$valid['errorlog'] = null;
$valid['update'] = null;

showToc($valid);

?>
