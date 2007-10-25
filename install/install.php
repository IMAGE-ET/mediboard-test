<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/

require_once("checkauth.php");

require_once ("../classes/mbpath.class.php");

class CLibraryPatch {
  var $dirName    = "";
  var $subDirName = "";
  var $sourceName = "";
  var $targetDir  = "";
  
  function apply() {
    global $mbpath;
    $pkgsDir = $mbpath."libpkg";
    $libsDir = $mbpath."lib";
    $patchDir = "$pkgsDir/patches";
    $sourcePath = "$patchDir/$this->dirName/";
    if($this->subDirName) {
      $sourcePath .= "$this->subDirName/";
    }
    $sourcePath .= "$this->sourceName";
    $targetPath = "$libsDir/$this->dirName/$this->targetDir/$this->sourceName";
    $oldPath = $targetPath . ".old";
    
    @unlink($oldPath);
    @rename($targetPath, $oldPath);
    return copy($sourcePath, $targetPath);
  }
}

class CLibraryRenamer {
  var $sourceDir = "";
  var $targetDir = "";
  
  function apply() {
    global $mbpath;
    $libsDir = $mbpath."lib";
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
  var $lib_ = null;
  
  function clearLibraries($lib_,$librairies) {
    global $mbpath;
    $libsDir = $mbpath."lib";
    
    if($lib_==""){
      foreach (glob("$libsDir/*") as $libDir) {  
        mbRemovePath($libDir);
      }
    } 
    else {
      foreach (glob("$libsDir/*") as $libDir) {
        if($libDir==$mbpath."lib/".$librairies[$lib_]->renamer->targetDir){
          mbRemovePath($libDir);  		
        }
      }
    }
  } 
    
  function countLibraries() {
    global $mbpath;
    $libsDir = $mbpath."lib";
    $libsCount = 0;
    
    foreach (glob("$libsDir/*") as $libDir) {
      $libsCount++;
    }
    
    return $libsCount;
  }
  
  function install() {
    global $mbpath;
    $pkgsDir = $mbpath."libpkg";
    $libsDir = $mbpath."lib";
    $filePath = "$pkgsDir/$this->fileName";
    
    // For libraries archive not contained in directory
    if ($this->extraDir) {
      $libsDir .= "/$this->extraDir";
    }
    
    return CMbPath::extract($filePath, $libsDir);
  }
}

$libSel = mbGetValueFromPost("libSel","");

$libraries = array();

$library = new CLibrary;
$library->name = "Smarty";
$library->url = "http://smarty.php.net/";
$library->fileName = "Smarty-2.6.18.tar.gz";
$library->description = "Moteur de templates PHP et framework de présentation";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "Smarty-2.6.18";
$renamer->targetDir = "smarty";

$library->renamer = $renamer;

$libraries[$library->name] = $library;

$library = new CLibrary;
$library->name = "JPGraph";
$library->url = "http://www.aditus.nu/jpgraph/";
$library->fileName = "jpgraph-2.1.4.tar.gz";
$library->description = "Composant PHP de génération de graphs aux formats d'image";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "jpgraph-2.1.4";
$renamer->targetDir = "jpgraph";

$library->renamer = $renamer;

$patch = new CLibraryPatch;
$patch->dirName = "jpgraph";
$patch->sourceName = "mbjpgraph.php";
$patch->targetDir = "src";

$library->patches[] = $patch;

$libraries[$library->name] = $library;


$library = new CLibrary;
$library->name = "FPDF";
$library->url = "http://www.fpdf.org/";
$library->fileName = "fpdf153.tgz";
$library->description = "Composant de génération de fichiers PDF";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "fpdf153";
$renamer->targetDir = "fpdf";

$library->renamer = $renamer;

$libraries[$library->name] = $library;


$library = new CLibrary;
$library->name = "TCPDF";
$library->url = "http://sourceforge.net/projects/tcpdf/";
$library->fileName = "tcpdf_1_53_0_TC034.zip";
$library->description = "Composant de génération de fichiers PDF avec codes barres";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "tcpdf";
$renamer->targetDir = "tcpdf";

$library->renamer = $renamer;

$libraries[$library->name] = $library;


$library = new CLibrary;
$library->name = "PHPMailer";
$library->url = "http://phpmailer.sourceforge.net/";
$library->fileName = "phpmailer-1.73.tar.gz";
$library->description = "Composant PHP d'envoi d'email";


$renamer = new CLibraryRenamer;
$renamer->sourceDir = "phpmailer";
$renamer->targetDir = "phpmailer";

$library->renamer = $renamer;


$libraries[$library->name] = $library;

$library = new CLibrary;
$library->name = "JSON-PHP";
$library->url = "http://mike.teczno.com/json.html";
$library->fileName = "JSON.tar.gz";
$library->extraDir = "json";
$library->description = "Composant PHP de genération de données JSON. Bientôt en package PEAR";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "json";
$renamer->targetDir = "json";

$library->renamer = $renamer;


$libraries[$library->name] = $library;

$library = new CLibrary;

$library->name = "Scriptaculous";
$library->url = "http://script.aculo.us/";
//$library->fileName = "scriptaculous-js-1.7.1_beta3.tar.gz";
//$library->fileName = "scriptaculous-js-1.7.0.tar.gz";
$library->fileName = "scriptaculous-js-1.6.0.tar.gz";

$library->description = "Composant Javascript d'effets spéciaux, accompagné du framework prototype.js";

$renamer = new CLibraryRenamer;
//$renamer->sourceDir = "scriptaculous-js-1.7.1_beta3";
//$renamer->sourceDir = "scriptaculous-js-1.7.0";
$renamer->sourceDir = "scriptaculous-js-1.6.0";
$renamer->targetDir = "scriptaculous";

$library->renamer = $renamer;

$libraries[$library->name] = $library;

$library = new CLibrary;

$library->name = "Open Rico";
$library->url = "http://openrico.org/";
$library->fileName = "Rico-1.1.2.tar.gz";
$library->description = "Composant Javascript d'effets spéciaux, utilisant le framework prototype.js";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "Rico-1.1.2";
$renamer->targetDir = "rico";

$library->renamer = $renamer;

$libraries[$library->name] = $library;

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

$libraries[$library->name] = $library;

$library = new CLibrary;
$library->name = "phpThumb";
$library->url = "http://phpthumb.sourceforge.net/";
$library->fileName = "phpThumb_1.7.5.zip";
$library->description = "Composant de création de thumbnails";
$library->extraDir = "phpThumb";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "phpThumb";
$renamer->targetDir = "phpThumb";

$library->renamer = $renamer;


$patch = new CLibraryPatch;
$patch->dirName = "phpThumb";
$patch->sourceName = "phpThumb.config.php";
$patch->targetDir = "";

$library->patches[] = $patch;

$libraries[$library->name] = $library;

$library = new CLibrary;
$library->name = "FCKEditor";
$library->url = "http://www.fckeditor.net/";
$library->fileName = "FCKeditor_2.3.2.tar.gz";
$library->description = "Composant Javascript d'édition de texte au format HTML";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "FCKeditor";
$renamer->targetDir = "fckeditor";

$library->renamer = $renamer;

$patch = new CLibraryPatch;
$patch->dirName = "fckeditor";
$patch->subDirName = "browser";
$patch->sourceName = "config.php";
$patch->targetDir = "editor/filemanager/browser/default/connectors/php";

$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "fckeditor";
$patch->subDirName = "uploader";
$patch->sourceName = "config.php";
$patch->targetDir = "editor/filemanager/upload/php";

$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "fckeditor";
$patch->sourceName = "fck_showtableborders_gecko.css";
$patch->targetDir = "editor/css";

$library->patches[] = $patch;

$libraries[$library->name] = $library;

$library = new CLibrary;
$library->name = "Dojo";
$library->url = "http://www.dojotoolkit.org/";
$library->fileName = "dojo-0.4.1-storage.tar.gz";
$library->description = "Composant Javascript de sauvegarde de données";

$renamer = new CLibraryRenamer;
$renamer->sourceDir = "dojo-0.4.1-storage";
$renamer->targetDir = "dojo";

$library->renamer = $renamer;

$libraries[$library->name] = $library;

$library = new CLibrary;
$library->name = "Livepipe control suite";
$library->url = "http://livepipe.net/projects/control_suite/";
$library->fileName = "control_suite.tar.gz";
$library->description = "Six widgets de controle";

$libraries[$library->name] = $library;

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
      <select name="libSel">
        <option value="">Toutes les bibliothèques</option>     
        <?php foreach($libraries as $library) { ?>
        <option value="<?php echo $library->name ?>"><?php echo $library->name ?></option>
        <?php } ?>
      </select>
      <input type="submit" name="do" value="Installer" />
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
  CLibrary::clearLibraries($libSel,$libraries);

?>


<table class="tbl">

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Site web</th>
  <th>Distribution</th>
  <th>Installation</th>
</tr>

<?php foreach($libraries as $library) { 
        if($libSel == $library->name || $libSel == "") {

?>
<tr>
  <td><strong><?php echo $library->name; ?></strong></td>
  <td class="text"><?php echo nl2br($library->description); ?></td>
  <td>
    <a href="<?php echo $library->url; ?>" title="Site web officiel de <?php echo $library->name; ?>">
    <?php echo $library->url; ?>
    </a>
  <td><?php echo $library->fileName; ?></td>
  <td>
    <?php if ($nbFiles = $library->install()) { ?>
    <div class="message">Ok, <?php echo $nbFiles ?> fichiers extraits</div>
    <?php } else { ?>
    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Erreur, <?php echo $library->nbFiles; ?> fichiers trouvés</div>
    <?php } ?>
  </td>
</tr>

<?php if ($renamer = $library->renamer) { ?>
<tr>
  <td />
  

  <?php 
  
  if($renamer->sourceDir != $renamer->targetDir) {
    echo "<td colspan='3'>";
    echo "Renommage de la bibliothèque $renamer->sourceDir en $renamer->targetDir";
    echo "</td>";
  }
  
  ?>
  
  <td>
    <?php if ($renamer->apply()) { ?>
    <?php 

    if($renamer->sourceDir != $renamer->targetDir) { 
      echo "<div class='message'>Renommage effectué</div>";
    }

    ?>
      
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

<?php }  ?>

<?php }  ?>

</table>

<?php showFooter(); ?>
