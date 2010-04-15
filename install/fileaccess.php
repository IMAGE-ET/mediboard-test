<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("checkauth.php");

class CPathAccess {
  var $path = "";
  var $description = "";
  var $reason = array();

  function check() {
    global $mbpath;
    return is_writable($mbpath.$this->path);
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

showHeader(); 

?>

<h2>Vérification des accès en écriture</h2>

<p>
  Le système a besoin de pouvoir écrire un certain nombre de fichiers pour son 
  fonctionnement.
</p>

<p>
  La présente page vérifie que les permissions en écriture de PHP sur ces 
  différents chemins.
</p>

<div class="big-warning">
  Il est très vivement déconseillé de s'affranchir des problèmes de permissions en rendant 
  toute l'arborescence du système accessible en écriture. Cette méthode engendrerait
  potentiellement une grande faille de sécurité.
  <br />
  Mediboard propose un script shell permettant d'établir ses permissions de façon 
  automatique. C'est le bon moment pour exécuter ce script si ce n'est pas déjà fait !
  <pre>sh [racine/de/mediboard/]shell/setup.sh www-data</pre>
</div>

<table class="tbl">

<tr>
  <th>Chemin</th>
  <th>Description</th>
  <th>Vérification ?</th>
</tr>
  
<?php foreach($pathAccesses as $pathAccess) { ?>
<tr>
  <td><strong><?php echo $pathAccess->path; ?></strong></td>
  <td class="text"><?php echo nl2br($pathAccess->description); ?></td>
  <td>
    <?php if ($pathAccess->check()) { ?>
    <div class="message">Ok</div>
    <?php } else { ?>
    <div class="error">Erreur</div>
    <?php } ?>
  </td>
</tr>
<?php } ?>
  
</table>

<?php showFooter(); ?>
