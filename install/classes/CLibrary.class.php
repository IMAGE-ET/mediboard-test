<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$
 * @link       http://www.mediboard.org
 */

/**
 * Vendor library
 */
class CLibrary {
  /** @var self[] */
  static $all = array();

  public $name = "";
  public $url = "";
  public $fileName = "";
  public $extraDir = "";
  public $description = "";
  public $nbFiles = 0;
  public $sourceDir = null;
  public $targetDir = null;
  public $versionFile = "";
  public $versionString = "";

  /** @var CLibraryPatch[] */
  public $patches = array();

  function getRootPath() {
    return __DIR__ . "/../../";
  }

  /**
   * Remove installed libraries
   *
   * @param string $libSel Library to clear
   *
   * @return void
   */
  function clearLibraries($libSel = null) {
    $mbpath = $this->getRootPath();
    $libsDir = $mbpath."lib";

    /// Clear out all libraries
    if (!$libSel) {
      foreach (glob("$libsDir/*") as $libDir) {
        if (strpos($libDir, '.svn') === false) {
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

  /**
   * Get update status of the libraries
   *
   * @return bool|null True if installed and up to date, null otherwise
   */
  function getUpdateState() {
    $mbpath = $this->getRootPath();
    $dir = $mbpath."lib/$this->targetDir";

    if ($this->versionFile && $this->versionString) {
      return (file_exists("$dir/$this->versionFile") &&
              strpos(file_get_contents("$dir/$this->versionFile"), $this->versionString) !== false);
    }
    return null;
  }

  /**
   * Is the library installed
   *
   * @return bool
   */
  function isInstalled() {
    $mbpath = $this->getRootPath();
    return is_dir($mbpath."lib/$this->targetDir");
  }

  /**
   * Count installed libraries
   *
   * @return int
   */
  function countLibraries() {
    $mbpath = $this->getRootPath();
    return count(glob($mbpath."lib/*"));
  }

  /**
   * Install the library
   *
   * @return int The number of extracted files
   */
  function install() {
    $mbpath = $this->getRootPath();
    $pkgsDir = $mbpath."libpkg";
    $libsDir = $mbpath."lib";
    $filePath = "$pkgsDir/$this->fileName";
    
    // For libraries archive not contained in directory
    if ($this->extraDir) {
      $libsDir .= "/$this->extraDir";
    }
    
    return CMbPath::extract($filePath, $libsDir);
  }

  /**
   * Apply the library patch
   *
   * @return bool
   */
  function apply() {
    $mbpath = $this->getRootPath();
    $libsDir = $mbpath."lib";
    $sourceDir = "$libsDir/$this->sourceDir";
    $targetDir = "$libsDir/$this->targetDir";
    assert(is_dir($sourceDir));
    return rename($sourceDir, $targetDir);
  }

  /**
   * Check update status of all the libraries
   *
   * @param bool $strict Use strict checking, not used
   *
   * @return bool
   */
  function checkAll($strict = true) {
    foreach (CLibrary::$all as $library) {
      if (!$library->getUpdateState()) {
        return false;
      }
    }
    
    return true;
  }
}

$library = new CLibrary;
$library->name = "Smarty";
$library->url = "http://www.smarty.net/";
$library->fileName = "Smarty-2.6.18.tar.gz";
$library->description = "Moteur de templates PHP et framework de présentation";
$library->sourceDir = "Smarty-2.6.18";
$library->targetDir = "smarty";
$library->versionFile = "libs/plugins/shared.escape_special_chars.php";
$library->versionString = "iso-8859-1";

$patch = new CLibraryPatch;
$patch->dirName = "smarty";
$patch->sourceName = "libs/plugins/shared.escape_special_chars.php";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "JPGraph";
$library->url = "http://jpgraph.net/";
$library->fileName = "jpgraph-2.1.4.tar.gz";
$library->description = "Composant PHP de génération de graphs aux formats d'image";
$library->sourceDir = "jpgraph-2.1.4";
$library->targetDir = "jpgraph";
$library->versionFile = "VERSION";
$library->versionString = "Revision: r793";
//$library->versionFile = "src/mbjpgraph.php";
//$library->versionString = "2010-12-15";

$patch = new CLibraryPatch;
$patch->dirName = "jpgraph";
$patch->sourceName = "mbjpgraph.php";
$patch->targetDir = "src";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "TCPDF";
$library->url = "http://www.tcpdf.org/";
$library->fileName = "tcpdf_1_53_0_TC034.zip";
$library->description = "Composant de génération de fichiers PDF avec codes barres";
$library->sourceDir = "tcpdf";
$library->targetDir = "tcpdf";
$library->versionFile = "fonts/c39hrp24dhtt.php";
$library->versionString = "TrueTypeUnicode";

$patch = new CLibraryPatch;
$patch->dirName = "tcpdf";
$patch->sourceName = "cmb128bobject.php";
$patch->targetDir = "barcode";
$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "tcpdf";
$patch->sourceName = "tcpdf.php";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "PHPMailer";
$library->url = "http://phpmailer.sourceforge.net/";
$library->fileName = "PHPMailer_v5.1.tar.gz";
$library->description = "Composant PHP d'envoi d'email";
$library->sourceDir = "PHPMailer_v5.1";
$library->targetDir = "phpmailer";
$library->versionFile = "changelog.txt";
$library->versionString = "Version 5.1";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Scriptaculous";
$library->url = "http://script.aculo.us/";
$library->fileName = "scriptaculous-js-1.9.0.zip";
$library->description = "Composant Javascript d'effets spéciaux, accompagné du framework prototype.js";
$library->sourceDir = "scriptaculous-js-1.9.0";
$library->targetDir = "scriptaculous";
$library->versionFile = "lib/prototype.js";
$library->versionString = "(window.pageYOffset !== undefined)";

$patch = new CLibraryPatch;
$patch->dirName = "scriptaculous";
$patch->sourceName = "src/scriptaculous.js";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$patch = new CLibraryPatch;
$patch->dirName = "scriptaculous";
$patch->sourceName = "lib/prototype.js";
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
$library->versionString = "r.2015-01-27";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "phpThumb";
$library->url = "http://phpthumb.sourceforge.net/";
$library->fileName = "phpThumb_1.7.5.tar.gz";
$library->description = "Composant de création de thumbnails";
$library->extraDir = "phpThumb";
$library->sourceDir = "phpThumb";
$library->targetDir = "phpThumb";
$library->versionFile = "phpthumb.functions.php";
$library->versionString = "static function user_function_exists";

$patch = new CLibraryPatch;
$patch->dirName = "phpThumb";
$patch->sourceName = "phpThumb.config.php";
$patch->targetDir = "";
$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName = "phpThumb";
$patch->sourceName = "phpThumb.php";
$patch->targetDir = "";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "CKEditor";
$library->url = "http://ckeditor.com/";
$library->fileName = "ckeditor_4.2.1.zip";
$library->description = "Composant Javascript d'édition de texte au format HTML";
$library->sourceDir = "ckeditor";
$library->targetDir = "ckeditor";
$library->versionFile = "ckeditor.js";
$library->versionString = "afterSource";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "kcfinder";
$library->url = "http://kcfinder.sunhater.com/";
$library->fileName = "kcfinder-2.51.tar.gz";
$library->description = "Gestionnaire de fichier en ajax";
$library->sourceDir = "kcfinder-2.51";
$library->targetDir = "kcfinder";
$library->versionFile = "config.php";
$library->versionString = "%";

$patch = new CLibraryPatch;
$patch->dirName     = "kcfinder";
$patch->sourceName  = "config.php";
$patch->targetDir   = "";
$library->patches[] = $patch;

$patch = new CLibraryPatch;
$patch->dirName     = "kcfinder/js/browser";
$patch->sourceName  = "settings.js";
$patch->targetDir   = "";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Livepipe UI";
$library->url = "http://livepipe.net/";
$library->fileName = "livepipe.tar.gz";
$library->description = "High Quality Controls & Widgets for Prototype";
$library->extraDir = "livepipe";
$library->sourceDir = "livepipe";
$library->targetDir = "livepipe";
$library->versionFile = "window.js";
$library->versionString = "'center', 'center_once'";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Flotr plotting library";
$library->url = "http://solutoire.com/flotr/";
$library->fileName = "flotr.r347.tar.gz";
$library->description = "Création de graphiques en JS";
$library->sourceDir = "flotr";
$library->targetDir = "flotr";
$library->versionFile = "flotr.js";
$library->versionString = 'Revision: 347';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Flot plotting library";
$library->url = "http://code.google.com/p/flot/";
$library->fileName = "flot-0.8.1.zip";
$library->description = "Création de graphiques en JS";
$library->sourceDir = "flot";
$library->targetDir = "flot";
$library->versionFile = "jquery.flot.gantt.js";
$library->versionString = 'pluginVersion = "0.3"';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "GeSHi";
$library->url = "http://qbnz.com/highlighter/";
$library->fileName = "GeSHi-1.0.8.3.tar.gz";
$library->description = "Generic Syntax Highlighter";
$library->sourceDir = "geshi";
$library->targetDir = "geshi";
$library->versionFile = "geshi.php";
$library->versionString = "define('GESHI_VERSION', '1.0.8.3.1')";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "dompdf";
$library->url = "http://code.google.com/p/dompdf";
$library->fileName = "dompdf-19-03-14.tar.gz";
$library->description ="HTML to PDF Converter";
$library->sourceDir = "dompdf";
$library->targetDir = "dompdf";
$library->versionFile = "include/style.cls.php";
$library->versionString = 'dispose() {}';
CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "DBase reader class";
$library->url = "http://www.phpclasses.org/package/1302.html";
$library->fileName = "dbase.tar.gz";
$library->description = "Lecture de fichiers DBF";
$library->sourceDir = "dbase";
$library->targetDir = "dbase";
$library->versionFile = "dbf_class.php";
$library->versionString = 'v0.05 by Nicholas Vrtis';

CLibrary::$all[$library->name] = $library;

/*$library = new CLibrary;
$library->name = "Growler";
$library->url = "http://code.google.com/p/kproto/wiki/Growler";
$library->fileName = "growler1.0.0.tar.gz";
$library->description = "Lecture de fichiers DBF";
$library->sourceDir = "growler";
$library->targetDir = "growler";
$library->versionFile = "src/Growler.js";
$library->versionString = 'k.Growler 1.0.0';

CLibrary::$all[$library->name] = $library;
*/

$library = new CLibrary;
$library->name = "PDFMerger class";
$library->url = "http://pdfmerger.codeplex.com/";
$library->fileName = "PDFMerger-1.0.tar.gz";
$library->description = "Fusion de PDF";
$library->sourceDir = "PDFMerger";
$library->targetDir = "PDFMerger";
$library->versionFile = "fpdi/pdf_parser.php";
$library->versionString = '$this->c = new pdf_context($this->f);';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "jsExpressionEval";
$library->url = "https://github.com/silentmatt/js-expression-eval";
$library->fileName = "jsExpressionEval.tar.gz";
$library->description = "A JavaScript math expression evaluator";
$library->sourceDir = "jsExpressionEval";
$library->targetDir = "jsExpressionEval";
$library->versionFile = "parser.js";
$library->versionString = "updated:2011-01-07";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "wkhtmltoPDF";
$library->url = "http://code.google.com/p/wkhtmltopdf/";
$library->fileName = "wkhtmltopdf.tar.gz";
$library->description = "Html To PDF converter";
$library->sourceDir = "wkhtmltopdf";
$library->targetDir = "wkhtmltopdf";
$library->versionFile = "version.txt";
$library->versionString = "0.11.0_rc1";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "iCalcreator";
$library->url = "http://sourceforge.net/projects/icalcreator/";
$library->fileName = "iCalcreator-2.10.15.zip";
$library->description = "iCal formatted files creator";
$library->sourceDir = "iCalcreator-2.10.15";
$library->targetDir = "iCalcreator";
$library->versionFile = "releaseNotes-2.10.15.txt";
$library->versionString = "2.10.15";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "NuSOAP";
$library->url = "http://sourceforge.net/projects/nusoap/";
$library->fileName = "NuSOAP.tar.gz";
$library->description = "A rewrite of SOAPx4";
$library->sourceDir = "NuSOAP";
$library->targetDir = "NuSOAP";
$library->versionFile = "changelog";
$library->versionString = "0.9.6";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "phpseclib";
$library->url = "https://github.com/phpseclib/phpseclib";
$library->fileName = "phpseclib0.3.5.zip";
$library->description = "PHP Secure Communications Library";
$library->extraDir = "phpseclib";
$library->sourceDir = "phpseclib";
$library->targetDir = "phpseclib";
$library->versionFile = "README.md";
$library->versionString = "Download (0.3.5)";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "HTMLPurifier";
$library->url = "http://htmlpurifier.org/";
$library->fileName = "htmlpurifier-4.5.0-lite.zip";
$library->description = "Standards-Compliant HTML Filtering";
$library->sourceDir = "htmlpurifier-4.5.0-lite";
$library->targetDir = "htmlpurifier";
$library->versionFile = "NEWS";
$library->versionString = "4.5.0, released 2013-02-17";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Yampee Redis client";
$library->url = "https://github.com/yampee/Redis";
$library->fileName = "yampee-redis.zip";
$library->description = "Client Redis compatible PHP 5.2";
$library->sourceDir = "yampee-redis";
$library->targetDir = "yampee-redis";
$library->versionFile = "src/Yampee/Redis/Client.php";
$library->versionString = 'class Yampee_Redis_Client';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "Store.js";
$library->url = "https://github.com/marcuswestin/store.js";
$library->fileName = "store.js-master.zip";
$library->description = "localStorage wrapper for all browsers";
$library->sourceDir = "store.js-master";
$library->targetDir = "store.js";
$library->versionFile = "Changelog";
$library->versionString = 'v1.3.9';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name          = "Parsedown";
$library->url           = "https://github.com/erusev/parsedown";
$library->fileName      = "Parsedown.zip";
$library->description   = "Better Markdown parser for PHP.";
$library->sourceDir     = "Parsedown";
$library->targetDir     = "parsedown";
$library->versionFile   = "Parsedown.php";
$library->versionString = "private function parse_block_elements";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name          = "Elastica";
$library->url           = "https://github.com/ruflin/Elastica";
$library->fileName      = "Elastica.zip";
$library->description   = "Client fulltext search for PHP.";
$library->sourceDir     = "Elastica";
$library->targetDir     = "Elastica";
$library->versionFile   = "changes.txt";
$library->versionString = "1.4.2";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name          = "Fabricjs library";
$library->url           = "http://fabricjs.com/";
$library->fileName      = "fabricjs.r140.tar.gz";
$library->description   = "Canvas javascript library";
$library->sourceDir     = "fabricjs";
$library->targetDir     = "fabricjs";
$library->versionFile   = "fabric.js";
$library->versionString = 'version:"1.4.0"';

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "cronExpression";
$library->url = "https://github.com/mtdowling/cron-expression";
$library->fileName = "cron-expression-1.0.3.tar.gz";
$library->description = "CRON for PHP";
$library->sourceDir = "cron-expression-1.0.3";
$library->targetDir = "cron-expression-1.0.3";
$library->versionFile = "README.md";
$library->versionString = "1.0.3 (2013-11-23)";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "d3";
$library->url = "https://github.com/mbostock/d3";
$library->fileName = "d3-3.4.13.tar.gz";
$library->description = "A JavaScript visualization library for HTML and SVG";
$library->sourceDir = "d3-3.4.13";
$library->targetDir = "d3-3.4.13";
$library->versionFile = "d3.js";
$library->versionString = "3.4.13";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary;
$library->name = "dagre-d3";
$library->url = "https://github.com/cpettitt/dagre-d3";
$library->fileName = "dagre-d3-0.3.2.tar.gz";
$library->description = "A D3-based renderer for Dagre";
$library->sourceDir = "dagre-d3-0.3.2";
$library->targetDir = "dagre-d3-0.3.2";
$library->versionFile = "dagre-d3.js";
$library->versionString = "0.3.2";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary();
$library->name = "visjs";
$library->url = "http://visjs.org/";
$library->fileName = "vis.zip";
$library->description = "A visual interaction system";
$library->sourceDir = "vis";
$library->targetDir = "visjs";
$library->versionFile = "vis.min.js";
$library->versionString = "@version 3.7.2";

CLibrary::$all[$library->name] = $library;

$library = new CLibrary();
$library->name = "favico.js";
$library->url = "http://lab.ejci.net/favico.js/";
$library->fileName = "favico.tar.gz";
$library->description = "Make a use of your favicon with badges, images or videos";
$library->sourceDir = "favico";
$library->targetDir = "favico";
$library->versionFile = "readme.md";
$library->versionString = "Version 0.3.6";

$patch = new CLibraryPatch();
$patch->dirName = "favico";
$patch->sourceName = "favico.js";
$patch->targetDir = "";
$library->patches[] = $patch;

CLibrary::$all[$library->name] = $library;

$library = new CLibrary();
$library->name = "requirejs";
$library->url = "http://requirejs.org/";
$library->fileName = "requirejs.zip";
$library->description = "JavaScript file and module loader";
$library->sourceDir = "requirejs";
$library->targetDir = "requirejs";
$library->versionFile = "require.js";
$library->versionString = "RequireJS 2.1.16";

CLibrary::$all[$library->name] = $library;