<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  var $versionFile = "";
  var $versionString = "";
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
      return @CMbPath::remove("$libsDir/$targetDir");
    }
  }
  
  function getUpdateState() {
  	global $mbpath;
    $dir = $mbpath."lib/$this->targetDir";
    
  	if ($this->versionFile && $this->versionString) {
  		return (file_exists("$dir/$this->versionFile") &&
  		        strpos(file_get_contents("$dir/$this->versionFile"), $this->versionString) !== false);
  	}
  	return null;
  }
  
  function isInstalled() {
    global $mbpath;
    return is_dir($mbpath."lib/$this->targetDir");
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
$library->versionFile = "NEWS";
$library->versionString = "Version 2.6.18";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JPGraph";
$library->url = "http://www.aditus.nu/jpgraph/";
$library->fileName = "jpgraph-2.1.4.tar.gz";
$library->description = "Composant PHP de génération de graphs aux formats d'image";
$library->sourceDir = "jpgraph-2.1.4";
$library->targetDir = "jpgraph";
$library->versionFile = "VERSION";
$library->versionString = "Revision: r793";

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
$library->versionFile = "histo.htm";
$library->versionString = "v1.53";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "TCPDF";
$library->url = "http://sourceforge.net/projects/tcpdf/";
$library->fileName = "tcpdf_1_53_0_TC034.zip";
$library->description = "Composant de génération de fichiers PDF avec codes barres";
$library->sourceDir = "tcpdf";
$library->targetDir = "tcpdf";
$library->versionFile = "README.TXT";
$library->versionString = "1.53.0.TC034";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "PHPMailer";
$library->url = "http://phpmailer.sourceforge.net/";
$library->fileName = "phpmailer-1.73.tar.gz";
$library->description = "Composant PHP d'envoi d'email";
$library->sourceDir = "phpmailer";
$library->targetDir = "phpmailer";
$library->versionFile = "ChangeLog.txt";
$library->versionString = "Version 1.73";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JSON-PHP";
$library->url = "http://mike.teczno.com/json.html";
$library->fileName = "JSON.tar.gz";
$library->extraDir = "json";
$library->description = "Composant PHP de genération de données JSON. Bientôt en package PEAR";
$library->sourceDir = "json";
$library->targetDir = "json";
$library->versionFile = "JSON.php";
$library->versionString = "JSON.php,v 1.30";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;

$library->name = "Scriptaculous";
$library->url = "http://script.aculo.us/";
$library->fileName = "scriptaculous-js-1.8.1.tar.gz";
$library->description = "Composant Javascript d'effets spéciaux, accompagné du framework prototype.js";
$library->sourceDir = "scriptaculous-js-1.8.1";
$library->targetDir = "scriptaculous";
$library->versionFile = "CHANGELOG";
$library->versionString = "*V1.8.1*";

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
$library->versionFile = "ChangeLog";
$library->versionString = "2005-03-07";

$patch = new CLibraryPatch;
$patch->dirName = "jscalendar";
$patch->sourceName = "calendar-fr.js";
$patch->targetDir = "lang";

$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "DatePicker";
$library->url = "http://home.jongsma.org/software/js/datepicker";
$library->fileName = "datepicker.tar.gz";
$library->description = "Composant Javascript de sélecteur de date/heure";
$library->sourceDir = "datepicker";
$library->targetDir = "datepicker";
$library->versionFile = "datepicker.js";
$library->versionString = "2009-06-23";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "phpThumb";
$library->url = "http://phpthumb.sourceforge.net/";
$library->fileName = "phpThumb_1.7.5.tar.gz";
$library->description = "Composant de création de thumbnails";
$library->extraDir = "phpThumb";
$library->sourceDir = "phpThumb";
$library->targetDir = "phpThumb";
$library->versionFile = "docs/phpthumb.changelog.txt";
$library->versionString = "v1.7.5";

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
$library->versionFile = "_whatsnew.html";
$library->versionString = "Version 2.6.3";

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
$library->versionFile = "dojo.js.uncompressed.js";
$library->versionString = "bootstrap1.js 6824";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Livepipe UI";
$library->url = "http://livepipe.net/";
$library->fileName = "livepipe.tar.gz";
$library->description = "High Quality Controls & Widgets for Prototype";
$library->extraDir = "livepipe";
$library->sourceDir = "livepipe";
$library->targetDir = "livepipe";
$library->versionFile = "livepipe.js";
$library->versionString = "@copyright 2008 PersonalGrid";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Flotr plotting library";
$library->url = "http://solutoire.com/flotr/";
$library->fileName = "flotr.r115.tar.gz";
$library->description = "Création de graphiques en JS";
$library->sourceDir = "flotr";
$library->targetDir = "flotr";
$library->versionFile = "flotr.js";
$library->versionString = '$Id: flotr.js 115';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "GeSHi";
$library->url = "http://qbnz.com/highlighter/";
$library->fileName = "GeSHi-1.0.8.3.tar.gz";
$library->description = "Generic Syntax Highlighter";
$library->sourceDir = "geshi";
$library->targetDir = "geshi";
$library->versionFile = "geshi.php";
$library->versionString = "define('GESHI_VERSION', '1.0.8.3')";

CLibrary::$all[$library->name] = $library;

$install = @$_POST['install'];

?>

<?php showHeader(); ?>

<h2>Installation des bibliothèques externes</h2>

<p>
  Mediboard utilise de nombreuses bibliothèques externes non publiées via PEAR.
</p>

<p>
  Celles-ci sont fournies dans leur distribution standard puis extraites. 
  N'hésitez pas à consulter les sites web correspondant pour obtenir de plus amples
  informations.
</p>

<form action="install.php" name="InstallLibs" method="post" style="display: block; text-align: center; margin: 1em;">  
  <input type="hidden" name="do" />
  <?php foreach(CLibrary::$all as $library) { ?>
  <input type="hidden" name="install[<?php echo $library->name; ?>]" value="true" />
  <?php } ?>
  <button type="submit" class="edit">Installer tout</button>
</form>

<form action="install.php" name="InstallLibs" method="post">  
<input type="hidden" name="do" />
<table class="tbl">
	<tr>
	  <th>Nom</th>
	  <th>Description</th>
	  <th>Site web</th>
	  <th>Distribution</th>
	  <th>Etat</th>
	  <th>Installation</th>
	</tr>
	
	<?php foreach(CLibrary::$all as $library) { 
		if (isset($install[$library->name])) {
  	  $library->clearLibraries($library->name); ?>
  <tr>
    <th rowspan="2"><?php echo $library->name; ?></th>
	  <td colspan="5">
		  <table style="border: none;">
		  <tr>
		    <td style="width: 100%; text-align: right;">Extraction des fichiers :</td>
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
		    <td style="width: 100%; text-align: right;">Renommage de la bibliothèque <strong>'<?php echo $library->sourceDir; ?>'</strong> en <strong>'<?php echo $library->targetDir; ?>'</strong> : </td>
		    <td>
		      <?php if ($library->apply()) { ?>
			    <div class='message'>Ok</div>
			    <?php } else { ?>
			    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Erreur</div>
			    <?php } ?>
		    </td>
      </tr>
	    <?php } ?>
		      
			<?php foreach($library->patches as $patch) { ?>
      <tr>
			  <td style="width: 100%; text-align: right;">Patch <strong>'<?php echo $patch->sourceName; ?>'</strong> dans <strong>'<?php echo $patch->targetDir; ?>'</strong> :</td>
        <td>
			    <?php if ($patch->apply()) { ?>
			    <div class="message">Patch appliqué</div>
			    <?php } else { ?>
			    <div class="<?php echo $prereq->mandatory ? "error" : "warning"; ?>">Erreur</div>
			    <?php } ?>
        </td>
			</tr>
			<?php } ?>
    </table>
	  </td>
	</tr>
  <?php } ?>
  <tr>
    <?php if (!isset($install[$library->name])) { ?><th><?php echo $library->name; ?></th><?php } ?>
    <td class="text"><?php echo nl2br($library->description); ?></td>
    <td>
      <a href="<?php echo $library->url; ?>" title="Site web officiel de <?php echo $library->name; ?>" target="_blank">
      <?php echo $library->url; ?>
      </a>
    </td>
    <td><?php echo $library->fileName; ?></td>
    <td>
      <?php if (!$library->isInstalled()) { ?>
      <div class="error">Non installée</div>
      <?php } else if ($library->getUpdateState() === null) { ?>
      <div class="message">Inconnu</div>
      <?php } else if ($library->getUpdateState())  { ?>
      <div class="message">A jour</div>
      <?php } else { ?>
      <div class="warning">Obsolète</div>
      <?php } ?>
    </td>
    <td>
      <button type="submit" name="install[<?php echo $library->name; ?>]" value="true" class="edit">Installer</button>
    </td>
  </tr>
	<?php } ?>
</table>

<?php showFooter(); ?>
