<?php
/**
 * External libraries installer
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkauth.php";

$mbpath = "../";
if (!file_exists($mbpath."classes/CMbPath.class.php")){
  $mbpath = "./";
}

require_once $mbpath."classes/CMbPath.class.php";

$install = @$_POST['install'];

showHeader();

?>

<h2>Installation des bibliothèques externes</h2>

<p>
  Mediboard utilise de nombreuses bibliothèques externes non publiées via PEAR.
</p>

<p>
  Celles-ci sont fournies dans leur distribution standard puis extraites. 
  N'hésitez pas à consulter les sites web correspondant pour obtenir de plus amples
  informations.
</p>

<form action="03_install.php" name="InstallAllLibs" method="post" style="display: block; text-align: center; margin: 1em;">
  <input type="hidden" name="do" />
  <?php foreach (CLibrary::$all as $library) { ?>
  <input type="hidden" name="install[<?php echo $library->name; ?>]" value="true" />
  <?php } ?>
  <button type="submit" class="edit">Installer tout</button>
</form>

<form action="03_install.php" name="InstallLibs" method="post">  
<input type="hidden" name="do" />
<table class="main tbl">
  <tr>
    <th>Nom</th>
    <th>Description</th>
    <th>Site web</th>
    <th>Distribution</th>
    <th>Etat</th>
    <th>Installation</th>
  </tr>
  
  <?php foreach (CLibrary::$all as $library) {
    if (isset($install[$library->name])) {
      $library->clearLibraries($library->name); ?>
  <tbody class="hoverable">
    <tr>
      <th rowspan="2"><?php echo $library->name; ?></th>
      <td colspan="5">
        <table class="main layout" style="float: right;">
          <col />
          <col style="width: 20em;" />
          
          <tr>
            <td style="text-align: right;">Extraction des fichiers :</td>
            <td>
              <?php if ($nbFiles = $library->install()) { ?>
              <div class="info">Ok, <?php echo $nbFiles ?> fichiers extraits</div>
              <?php } else { ?>
              <div class="error">Erreur, <?php echo $library->nbFiles; ?> fichiers trouvés</div>
              <?php } ?>
            </td>
          </tr>
          
          <?php if ($library->sourceDir != $library->targetDir) { ?>
          <tr>
            <td style="text-align: right;">Renommage de la bibliothèque <strong>'<?php echo $library->sourceDir; ?>'</strong> en <strong>'<?php echo $library->targetDir; ?>'</strong> : </td>
            <td>
              <?php if ($library->apply()) { ?>
              <div class='info'>Ok</div>
              <?php } else { ?>
              <div class="error">Erreur</div>
              <?php } ?>
            </td>
          </tr>
          <?php } ?>
              
          <?php foreach ($library->patches as $patch) { ?>
          <tr>
            <td style="text-align: right;">Patch <strong>'<?php echo $patch->sourceName; ?>'</strong> dans <strong>'<?php echo $patch->targetDir; ?>'</strong> :</td>
            <td>
              <?php if ($patch->apply()) { ?>
              <div class="info">Patch appliqué</div>
              <?php } else { ?>
              <div class="error">Erreur</div>
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
        <div class="info">Inconnu</div>
        <?php } else if ($library->getUpdateState())  { ?>
        <div class="info">A jour</div>
        <?php } else { ?>
        <div class="warning">Obsolète</div>
        <?php } ?>
      </td>
      <td>
        <button type="submit" name="install[<?php echo $library->name; ?>]" value="true" class="edit">Installer</button>
      </td>
    </tr>
  </tbody>
  <?php flush(); } ?>
</table>
</form>

<?php showFooter(); ?>