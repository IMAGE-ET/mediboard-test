<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("header.php");

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
    return @include("$this->name.php");
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
$packages = array();

$ezcPackage = new CPearPackage;
$ezcPackage->name = "ezc/Graph/graph";
$ezcPackage->description = "Package de manipulation des graphiques";
$ezcPackage->mandatory = false;
$ezcPackage->status = "beta";
$ezcPackage->reasons[] = "Utilisation des graphiques dans Mediboard";

$package = new CPearPackage;
$package->name = "Archive/Tar";
$package->description = "Package de manipulation d'archives au format GNU TAR";
$package->mandatory = true;
$package->reasons[] = "Installation de Mediboard";
$package->reasons[] = "Import des fonctions de GHM";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Archive/Zip";
$package->description = "Package de manipulation d'archives au format ZIP";
$package->mandatory = true;
$package->status = "beta";
$package->reasons[] = "Installation de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Config";
$package->description = "Package de manipulation de fichiers de configuration";
$package->mandatory = true;
$package->reasons[] = "Configuration générale de Mediboard";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Date";
$package->description = "Package de manipulation de dates";
$package->mandatory = true;
$package->reasons[] = "Relicats du framework dotProject";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "DB";
$package->description = "Package de manipulation de base de données";
$package->mandatory = true;
$package->reasons[] = "Assistant d'installation de Mediboard";
$package->reasons[] = "A terme, probablement tout le système";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "Auth";
$package->description = "Package d'authentification multi-support";
$package->mandatory = true;
$package->reasons[] = "Assistant d'installation de Mediboard";
$package->reasons[] = "A terme, probablement tout le système";
$packages[] = $package;

$package = new CPearPackage;
$package->name = "phpUnit";
$package->description = "Package de test unitaire";
$package->mandatory = false;
$package->reasons[] = "Tests unitaires et fonctionnels de Mediboard";
$package->reasons[] = "cf. <a href='http://www.phpunit.de/wiki/Documentation' style='display: inline;'>http://www.phpunit.de/wiki/Documentation</a>";
$packages[] = $package;

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
$extension->name = "DOM";
$extension->description = "Extension de manipulation de fichier XML avec l'API DOM";
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
$extension->name = "eAccelerator";
$extension->description = "Extension d'optimsation d'OPCODE et de mémoire partagée";
$extension->reasons[] = "Accélération globale du système";
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

?>

<?php showHeader(); ?>

<h2>Vérification des prérequis</h2>

<h3>Version de PHP</h3>

<p>
  PHP est le langage d'exécution de script côté serveur de Mediboard. Il est 
  nécessaire d'installer une version récente de PHP pour assurer le bon 
  fonctionnement du système.
</p>

<p>
  N'hésitez pas à vous rendre sur le site officiel de <a href="http://www.php.net/">http://www.php.net/</a>
  pour obtenir les dernières versions de PHP.
</p>

<table class="tbl">

<tr>
  <th>Numéro de version</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilité</th>
  <th>Installation ?</th>
</tr>
  
<?php foreach($versions as $prereq) { ?>
<tr>
  <td><strong><?php echo $prereq->name; ?></strong></td>
  <td class="text"><?php echo nl2br($prereq->description); ?></td>
  <td>
    <?php if ($prereq->mandatory) { ?>
    Oui
    <?php } else { ?>
    Recommandée
    <?php } ?>
  </td>
  <td class="text">
    <ul>
      <?php foreach($prereq->reasons as $reason) { ?>
      <li><?php echo $reason; ?></li>
      <?php } ?>
    </ul>
  </td>
  <td>
    <?php if ($prereq->check()) { ?>
    <div class="message">Oui, Version <?php echo phpVersion(); ?></div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Non, Version <?php echo phpVersion(); ?></div>
    <?php } ?>
  </td>
</tr>
<?php } ?>
  
</table>

<h3>Extensions PECL</h3>
<p>
  PECL est une bibliothèque d'extensions binaires de PHP. 
  <br />
  La plupart des  extensions de base de PHP est fournie avec votre 
  distribution de PHP. Si toutefois certaines extensions sont manquantes,
  vérifiez que :
</p>
<ul>
  <li>L'extension est installée sur votre déploiement PHP</li>
  <li>L'extension est bien chargée dans la configuration de PHP (php.ini)</li>
</ul>  
<p>
  N'hésitez pas à vous rendre sur le site officiel de PHP <a href="http://www.php.net/">http://www.php.net/</a>
  et de PECL <a href="http://pecl.php.net/">http://pecl.php.net/</a>  pour 
  obtenir de plus amples informations. 
</p>

<table class="tbl" >

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilité</th>
  <th>Installation ?</th>
</tr>

<?php foreach($extensions as $prereq) { ?>
<tr>
  <td><strong><?php echo $prereq->name; ?></strong></td>
  <td class="text"><?php echo nl2br($prereq->description); ?></td>
  <td>
    <?php if ($prereq->mandatory) { ?>
    Oui
    <?php } else { ?>
    Recommandée
    <?php } ?>
  </td>
  <td class="text">
    <ul>
      <?php foreach($prereq->reasons as $reason) { ?>
      <li><?php echo $reason; ?></li>
      <?php } ?>
    </ul>
  </td>
  <td>
    <?php if ($prereq->check()) { ?>
    <div class="message">Extension chargée</div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Extension absente</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>

</table>

<h3>Packages PEAR</h3>

<p>
  PEAR est un framework de distributions de bibliothèques écrites en PHP.
  <br />
  Si plusieurs ou tous les packages sont manquants, n'hésitez pas à vous rendre 
  sur le site officiel <a href="http://pear.php.net/">http://pear.php.net/</a>
  pour les installer sur votre déploiement de PHP. 
</p>
  
<table class="tbl" >

<tr>
  <th class="category" colspan="6">Packages PEAR</th>
</tr>

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilité</th>
  <th>Statut</th>
  <th>Installation ?</th>
</tr>

<?php foreach($packages as $prereq) { ?>
<tr>
  <td><strong><?php echo $prereq->name; ?></strong></td>
  <td class="text"><?php echo nl2br($prereq->description); ?></td>
  <td>
    <?php if ($prereq->mandatory) { ?>
    Oui
    <?php } else { ?>
    Recommandé
    <?php } ?>
  </td>
  <td class="text">
    <ul>
      <?php foreach($prereq->reasons as $reason) { ?>
      <li><?php echo $reason; ?></li>
      <?php } ?>
    </ul>
  </td>
  <td><?php echo $prereq->status; ?></td>
  <td>
    <?php if ($prereq->check()) { ?>
    <div class="message">Package installé</div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Package manquant</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>

</table>


<div class="big-info">
  Certains packages Pear ne sont pas publiés dans un status <strong>stable</strong>, 
  bien que suffisemment fonctionnels pour Mediboard. 
  <br />Pour pouvoir installer les packages en statut <strong>beta</strong>, il peut être
  néccessaire de configurer PEAR avec la commande :
  
  <pre>pear config-set preferred_state beta</pre>
   
</div>

<h3>Packages ezComponent</h3>

<p>
  ezComponent permet d'accélèrer l'implémentation et réduit les risques des projets de développement d'applications sur la technologie PHP.
  <br />
  Si le package est manquant, n'hésitez pas à vous rendre 
  sur le site officiel <a href="http://ez.no/fr/ezcomponents">http://ez.no/fr/ezcomponents</a>
  pour l'installer sur votre déploiement de PHP. 
</p>
  
<table class="tbl" >

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Obligatoire ?</th>
  <th>Utilité</th>
  <th>Statut</th>
  <th>Installation ?</th>
</tr>

<tr>
  <td><strong><?php echo $ezcPackage->name; ?></strong></td>
  <td class="text"><?php echo nl2br($ezcPackage->description); ?></td>
  <td>
    <?php if ($ezcPackage->mandatory) { ?>
    Oui
    <?php } else { ?>
    Recommandé
    <?php } ?>
  </td>
  <td class="text">
    <ul>
      <?php foreach($ezcPackage->reasons as $reason) { ?>
      <li><?php echo $reason; ?></li>
      <?php } ?>
    </ul>
  </td>
  <td><?php echo $ezcPackage->status; ?></td>
  <td>
    <?php if ($ezcPackage->check()) { ?>
    <div class="message">Package installé</div>
    <?php } else { ?>
    <div class="<?php echo $ezcPackage->mandatory ? "error" : "warning"; ?>">Package manquant</div>
    <?php } ?>
  </td>
</tr>
</table>

<?php showFooter(); ?>
