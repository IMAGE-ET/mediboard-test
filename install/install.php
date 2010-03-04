<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("checkauth.php");
require_once("../classes/mbpath.class.php");
require_once("libs.php");

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
</form>

<?php showFooter(); ?>
