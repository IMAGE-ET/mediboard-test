<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("checkauth.php");

require_once ("Archive/Tar.php");
require_once ("Archive/Zip.php");

class CLibraryPatch {
  var $dirName = "";
  var $sourceName = "";
  var $targetDir = "";
  
  function apply() {
    global $mbpath;
    $pkgsDir = "$mbpath/libpkg";
    $libsDir = "$mbpath/lib";
    $patchDir = "$pkgsDir/patches";
    $sourcePath = "$patchDir/$this->dirName/$this->sourceName";
    $targetPath = "$libsDir/$this->dirName/$this->targetDir/$this->sourceName";
    $oldPath = $targetPath . ".old";
    assert("Source is not existing" | is_file($targetPath));
    @unlink($oldPath);
    rename($targetPath, $oldPath);
    return copy($sourcePath, $targetPath);
  }
}

class CLibraryRenamer {
  var $sourceDir = "";
  var $targetDir = "";
  
  function apply() {
    global $mbpath;
    $libsDir = "$mbpath/lib";
    $sourceDir = "$libsDir/$this->sourceDir";
    $targetDir = "$libsDir/$this->targetDir";
    assert(is_dir($sourceDir));
    return rename($sourceDir, $targetDir);
  }
}

class CLibrary {
  var $name = "";
  var $url = "";
  var $fileName = "";
  var $extraDir = "";
  var $description = "";
  var $nbFiles = 0;
  var $renamer = null;
  var $patches = array();
  
  function clearLibraries() {
    global $mbpath;
    $libsDir = "$mbpath/lib";

    foreach (glob("$libsDir/*") as $libDir) {
      mbRemovePath($libDir);
    }
  }
  
  function countLibraries() {
    global $mbpath;
    $libsDir = "$mbpath/lib";
    $libsCount = 0;
    
    foreach (glob("$libsDir/*") as $libDir) {
      $libsCount++;
    }
    
    return $libsCount;
  }
  
  function install() {
    global $mbpath;
    $pkgsDir = "$mbpath/libpkg";
    $libsDir = "$mbpath/lib";
    $filePath = "$pkgsDir/$this->fileName";
    
    // For libraries archive non contained in directory
    if ($this->extraDir) {
      $libsDir .= "/$this->extraDir";
    }
    
    $archive = null;
    
    switch (mbGetFileExtension($filePath)) {
      case "gz"  :
      case "tgz" : 
      $archive = new Archive_Tar($filePath);
      $this->nbFiles = count($archive->listContent());
      $return = $archive->extract($libsDir);
      break;
      
      case "zip" : 
      $archive = new Archive_Zip($filePath);
      $this->nbFiles = count($archive->listContent());
      $return = $archive->extract(array("add_path" => $libsDir));
      break;
      
      default : 
      $return = false;
      break;
    }
    
    return $return;
  }
}

$libraries = array();

$library = new CLibrary;
$library->name = "Smarty";
$library->url = "http://smarty.php.net/";
$library->fileName = "Smarty-2.6.13.tar.gz";
$library->description = "Moteur de templates PHP et framework de présentation";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "Smarty-2.6.13";
$renamer->targetDir = "smarty";

$library->renamer = $renamer;

$libraries[] = $library;

$library = new CLibrary;
$library->name = "JPGraph";
$library->url = "http://www.aditus.nu/jpgraph/";
$library->fileName = "jpgraph-1.19.tar.gz";
$library->description = "Composant PHP de génération de graphs aux formats d'image";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "jpgraph-1.19";
$renamer->targetDir = "jpgraph";

$library->renamer = $renamer;

$libraries[] = $library;

$library = new CLibrary;
$library->name = "FPDF";
$library->url = "http://www.fpdf.org/";
$library->fileName = "fpdf153.tgz";
$library->description = "Composant de génération de fichiers PDF";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "fpdf153";
$renamer->targetDir = "fpdf";

$library->renamer = $renamer;

$libraries[] = $library;

$library = new CLibrary;
$library->name = "PHPMailer";
$library->url = "http://phpmailer.sourceforge.net/";
$library->fileName = "phpmailer-1.73.tar.gz";
$library->description = "Composant PHP d'envoi d'email";

$libraries[] = $library;

$library = new CLibrary;
$library->name = "JSON-PHP";
$library->url = "http://mike.teczno.com/json.html";
$library->fileName = "JSON.tar.gz";
$library->extraDir = "json";
$library->description = "Composant PHP de genération de données JSON. Bientôt en package PEAR";

$libraries[] = $library;

$library = new CLibrary;

$library->name = "Scriptaculous";
$library->url = "http://script.aculo.us/";
$library->fileName = "scriptaculous-js-1.6.0.tar.gz";
$library->description = "Composant Javascript d'effets spéciaux, accompagné du framework prototype.js";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "scriptaculous-js-1.6.0";
$renamer->targetDir = "scriptaculous";

$library->renamer = $renamer;

$libraries[] = $library;

$library = new CLibrary;
$library->name = "FCKEditor";
$library->url = "http://www.fckeditor.net/";
$library->fileName = "FCKeditor_2.2.tar.gz";
$library->description = "Composant Javascript d'édition de texte au format HTML";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "FCKeditor";
$renamer->targetDir = "fckeditor";

$library->renamer = $renamer;

$patch = new CLibraryPatch;
$patch->dirName = "fckeditor";
$patch->sourceName = "config.php";
$patch->targetDir = "editor/filemanager/browser/default/connectors/php";

$library->patches[] = $patch;

$libraries[] = $library;

$library = new CLibrary;
$library->name = "JSCalendar";
$library->url = "http://www.dynarch.com/projects/calendar/";
$library->fileName = "jscalendar-1.0.zip";
$library->description = "Composant Javascript de sélecteur de date/heure";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "jscalendar-1.0";
$renamer->targetDir = "jscalendar";

$library->renamer = $renamer;

$patch = new CLibraryPatch;
$patch->dirName = "jscalendar";
$patch->sourceName = "calendar-fr.js";
$patch->targetDir = "lang";

$library->patches[] = $patch;

$libraries[] = $library;

?>

<?php showHeader(); ?>

<h2>Installation des bibliothèques externes</h2>

<p>
  Mediboard utilise de nombreuses bibliothèques externes non publiées via PEAR.
</p>

<p>
  Celles-ci sont fournies dans leur distributions standards puis extraites. 
  N'hésitez pas à consulter les sites web correspondant pour obtenir de plus amples
  informations.
</p>

<form action="install.php" name="InstallLibs" method="post">  

<table class="form">
  <tr>
    <th class="category">Installation des bibliothèques</th>
  </tr>
  <tr>
    <td class="button">
      <input type="submit" name="do" value="Installer les bibliothèques" />
    </td>
  </tr>
</table>

</form>

<?php if ($n = CLibrary::countLibraries()) { ?>
<div class="big-warning">
  Les bibliothèques de Mediboard sont actuellement installées.
  <br />Vous pouvez décider de les ré-installer pour les mettre à jour, sachant que les
  anciennes seront supprimées. 
</div>
<?php } ?>

<?php 
if (@$_POST["do"]) {
  CLibrary::clearLibraries();
?>


<table class="tbl">

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Site web</th>
  <th>Distribution</th>
  <th>Installation</th>
</tr>

<?php foreach($libraries as $library) { ?>
<tr>
  <td><strong><?php echo $library->name; ?></strong></td>
  <td class="text"><?php echo nl2br($library->description); ?></td>
  <td>
    <a href="<?php echo $library->url; ?>" title="Site web officiel de <?php echo $library->name; ?>">
    <?php echo $library->url; ?>
    </a>
  <td><?php echo $library->fileName; ?></td>
  <td>
    <?php if ($library->install()) { ?>
    <div class="message">Ok, <?php echo $library->nbFiles; ?> fichiers extraits</div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Erreur, <?php echo $library->nbFiles; ?> fichiers trouvés</div>
    <?php } ?>
  </td>
</tr>

<?php if ($renamer = $library->renamer) { ?>
<tr>
  <td />
  <td colspan="3">
    Renommage de la bibliothèque <?php echo $renamer->sourceDir; ?>/ 
    en <?php echo $renamer->targetDir; ?>/
  </td>
  <td>
    <?php if ($renamer->apply()) { ?>
    <div class="message">Renommage effectué</div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Erreur</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>

<?php foreach($library->patches as $patch) { ?>
<tr>
  <td />
  <td colspan="3">
    Patch <?php echo $patch->sourceName; ?> dans <?php echo $patch->targetDir; ?>
  </td>
  <td>
    <?php if ($patch->apply()) { ?>
    <div class="message">Patch appliqué</div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Erreur</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>

<?php } ?>

<?php } ?>

</table>

<?php showFooter(); ?>
