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

class CLibrary {
  static $all = array();
  
  var $name = "";
  var $url = "";
  var $fileName = "";
  var $extraDir = "";
  var $description = "";
  var $nbFiles = 0;
  var $sourceDir = null;
  var $targetDir = null;
  var $patches = array();
  var $lib_ = null;
  
  function clearLibraries($libSel) {
    global $mbpath;
    $libsDir = $mbpath."lib";
    
    /// Clear out all libraries
    if (!$libSel){
      foreach (glob("$libsDir/*") as $libDir) {
      	if (strpos($libDir, '-svn') === false) {
          CMbPath::remove($libDir);
      	}
      }
      return;
    } 

    // Clear out selected lib
    $library = self::$all[$libSel];
    if ($targetDir = $library->targetDir) {
      @CMbPath::remove("$libsDir/$targetDir");
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
  
  function apply() {
    global $mbpath;
    $libsDir = $mbpath."lib";
    $sourceDir = "$libsDir/$this->sourceDir";
    $targetDir = "$libsDir/$this->targetDir";
    assert(is_dir($sourceDir));
    return rename($sourceDir, $targetDir);
  }
}

$libSel = mbGetValueFromPost("libSel","");

$library = new CLibrary;
$library->name = "Smarty";
$library->url = "http://smarty.php.net/";
$library->fileName = "Smarty-2.6.18.tar.gz";
$library->description = "Moteur de templates PHP et framework de présentation";
$library->sourceDir = "Smarty-2.6.18";
$library->targetDir = "smarty";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JPGraph";
$library->url = "http://www.aditus.nu/jpgraph/";
$library->fileName = "jpgraph-2.1.4.tar.gz";
$library->description = "Composant PHP de génération de graphs aux formats d'image";
$library->sourceDir = "jpgraph-2.1.4";
$library->targetDir = "jpgraph";

$patch = new CLibraryPatch;
$patch->dirName = "jpgraph";
$patch->sourceName = "mbjpgraph.php";
$patch->targetDir = "src";

$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "FPDF";
$library->url = "http://www.fpdf.org/";
$library->fileName = "fpdf153.tgz";
$library->description = "Composant de génération de fichiers PDF";
$library->sourceDir = "fpdf153";
$library->targetDir = "fpdf";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "TCPDF";
$library->url = "http://sourceforge.net/projects/tcpdf/";
$library->fileName = "tcpdf_1_53_0_TC034.zip";
$library->description = "Composant de génération de fichiers PDF avec codes barres";
$library->sourceDir = "tcpdf";
$library->targetDir = "tcpdf";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "PHPMailer";
$library->url = "http://phpmailer.sourceforge.net/";
$library->fileName = "phpmailer-1.73.tar.gz";
$library->description = "Composant PHP d'envoi d'email";
$library->sourceDir = "phpmailer";
$library->targetDir = "phpmailer";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JSON-PHP";
$library->url = "http://mike.teczno.com/json.html";
$library->fileName = "JSON.tar.gz";
$library->extraDir = "json";
$library->description = "Composant PHP de genération de données JSON. Bientôt en package PEAR";
$library->sourceDir = "json";
$library->targetDir = "json";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;

$library->name = "Scriptaculous";
$library->url = "http://script.aculo.us/";
$library->fileName = "scriptaculous-js-1.8.1.tar.gz";
$library->description = "Composant Javascript d'effets spéciaux, accompagné du framework prototype.js";
$library->sourceDir = "scriptaculous-js-1.8.1";
$library->targetDir = "scriptaculous";

$patch = new CLibraryPatch;
$patch->dirName = "scriptaculous";
$patch->sourceName = "scriptaculous.js";
$patch->targetDir = "src";

$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JSCalendar";
$library->url = "http://www.dynarch.com/projects/calendar/";
$library->fileName = "jscalendar-1.0.zip";
$library->description = "Composant Javascript de sélecteur de date/heure";
$library->sourceDir = "jscalendar-1.0";
$library->targetDir = "jscalendar";

$patch = new CLibraryPatch;
$patch->dirName = "jscalendar";
$patch->sourceName = "calendar-fr.js";
$patch->targetDir = "lang";

$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "phpThumb";
$library->url = "http://phpthumb.sourceforge.net/";
$library->fileName = "phpThumb_1.7.5.zip";
$library->description = "Composant de création de thumbnails";
$library->extraDir = "phpThumb";
$library->sourceDir = "phpThumb";
$library->targetDir = "phpThumb";

$patch = new CLibraryPatch;
$patch->dirName = "phpThumb";
$patch->sourceName = "phpThumb.config.php";
$patch->targetDir = "";

$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "FCKEditor";
$library->url = "http://www.fckeditor.net/";
$library->fileName = "FCKeditor_2.6.3.tar.gz";
$library->description = "Composant Javascript d'édition de texte au format HTML";
$library->sourceDir = "fckeditor";
$library->targetDir = "fckeditor";

$patch = new CLibraryPatch;
$patch->dirName = "fckeditor";
$patch->subDirName = "browser";
$patch->sourceName = "config.php";
$patch->targetDir = "editor/filemanager/connectors/php";

$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "fckeditor";
$patch->sourceName = "fckeditor.html";
$patch->targetDir = "editor";

$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Dojo";
$library->url = "http://www.dojotoolkit.org/";
$library->fileName = "dojo-0.4.1-storage.tar.gz";
$library->description = "Composant Javascript de sauvegarde de données";
$library->sourceDir = "dojo-0.4.1-storage";
$library->targetDir = "dojo";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Livepipe control suite";
$library->url = "http://livepipe.net/projects/control_suite/";
$library->fileName = "control_suite.zip";
$library->description = "Six widgets de controle, utilisant le framework prototype.js";
$library->extraDir = "control_suite";
$library->sourceDir = "control_suite";
$library->targetDir = "control_suite";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Flotr plotting library";
$library->url = "http://solutoire.com/flotr/";
$library->fileName = "flotr.r78.tar.gz";
$library->description = "Création de graphiques en JS";
$library->sourceDir = "flotr";
$library->targetDir = "flotr";

CLibrary::$all[$library->name] = $library;
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
        <?php foreach(CLibrary::$all as $library) { ?>
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
  CLibrary::clearLibraries($libSel);

?>


<table class="tbl">

<tr>
  <th>Nom</th>
  <th>Description</th>
  <th>Site web</th>
  <th>Distribution</th>
  <th>Installation</th>
</tr>

<?php foreach(CLibrary::$all as $library) { 
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

<?php if ($library->sourceDir != $library->targetDir) { ?>
<tr>
  <td />
  

  <td colspan='3'>
	  Renommage de la bibliothèque '<?php echo $library->sourceDir; ?>'
	  en '<?php echo $library->targetDir; ?>'
  </td>
  
  <td>
    <?php if ($library->apply()) { ?>
		<div class='message'>Renommage effectué</div>
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
