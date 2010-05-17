<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("checkauth.php");
require_once($mbpath."includes/compat.php");
require_once($mbpath."classes/mbconfig.class.php");
require_once($mbpath."classes/mbarray.class.php");

if(isset($_POST["username"])){
 unset($_POST["username"]); 
}
if(isset($_POST["password"])){
 unset($_POST["password"]); 
}

$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();

$dPconfig = $mbConfig->values;

showHeader(); 

?>

<h2>Création du fichier de configuration</h2>

<form name="configure" action="configure.php" method="post">

<table class="form">
  <col style="width: 50%" />
  
  <tr>
    <th class="category" colspan="2">Configuration générale</th>
  </tr>

  <tr>
    <th><label for="root_dir" title="Repertoire racine. Pas de slash final. Utiliser les slashs aussi pour MS Windows">Répertoire racine</label></th>
    <td><input type="text" size="40" name="root_dir" value="<?php echo $dPconfig["root_dir"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="base_url" title="Url Racine pour le système">Url racine</label></th>
    <td><input type="text" size="40" name="base_url" value="<?php echo $dPconfig['base_url'] ?>" /></td>
  </tr>
  
  <tr>
    <th><label for="instance_role" title="Instance">Rôle de l'instance</label></th>
    <td>
      <select name="instance_role" size="1">
        <option value="prod"   <?php if ($dPconfig['instance_role'] == 'prod'  ) { echo 'selected="selected"'; } ?> >Production</option>
        <option value="qualif" <?php if ($dPconfig['instance_role'] == 'qualif') { echo 'selected="selected"'; } ?> >Qualification</option>
      </select>
    </td>
  </tr>

  <tr>
    <th><label for="offline" title="Mode maintenance">Mode maintenance</label></th>
    <td><input type="text" size="1" maxlength="1" name="offline" value="<?php echo $dPconfig['offline'] ?>" /></td>
  </tr>
  
  <tr>
    <th><label for="migration[active]" title="Affiche une page avec les nouvelles adresse de Mediboard aux utilisateur">Mode migration</label></th>
    <td><input type="text" size="1" maxlength="1" name="migration[active]" value="<?php echo @$dPconfig['migration']['active']; ?>" /></td>
  </tr>

  <tr>
    <th><label for="shared_memory" title="Choisir quelle extension doit tenter de gérer la mémoire partagée (celle-ci doit être installée)">Mémoire partagée</label></th>
    <td>
      <div style="float: right">
      <?php require_once("empty_shared_memory.php"); ?>
      </div>
      <select name="shared_memory" size="1">
        <option value="none"         <?php if ($dPconfig['shared_memory'] == 'none'        ) { echo 'selected="selected"'; } ?> >Disque</option>
        <option value="eaccelerator" <?php if ($dPconfig['shared_memory'] == 'eaccelerator') { echo 'selected="selected"'; } ?> >eAccelerator</option>
        <option value="apc"          <?php if ($dPconfig['shared_memory'] == 'apc'         ) { echo 'selected="selected"'; } ?> >APC</option>
      </select>
    </td>
  </tr>
  <tr>
    <th class="category" colspan="2">Configuration de la base de données principale</th>
  </tr>

  <tr>
    <th><label for="db[std][dbtype]" title="Type de base de données. Seul mysql est possible pour le moment">Type de base de données :</label></th>
    <td><input type="text" readonly="readonly" size="40" name="db[std][dbtype]" value="<?php echo @$dPconfig["db"]["std"]["dbtype"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbhost]" title="Nom de l'hôte">Nom de l'hôte</label></th>
    <td><input type="text" size="40" name="db[std][dbhost]" value="<?php echo @$dPconfig["db"]["std"]["dbhost"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbname]" title="Nom de la base">Nom de la base</label></th>
    <td><input type="text" size="40" name="db[std][dbname]" value="<?php echo @$dPconfig["db"]["std"]["dbname"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur</label></th>
    <td><input type="text" size="40" name="db[std][dbuser]" value="<?php echo @$dPconfig["db"]["std"]["dbuser"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe</label></th>
    <td><input type="text" size="40" name="db[std][dbpass]" value="<?php echo @$dPconfig["db"]["std"]["dbpass"]; ?>" /></td>
  </tr>

<!--
  <tr>
    <th class="category" colspan="2">Paramètres d'indexation de fichiers</th>
  </tr>

  <tr>
    <th><label for="ft[default]" title="Application par défaut">Application par défaut</label></th>
    <td><input type="text" size="40" name="ft[default]" value="<?php echo $dPconfig['ft']['default'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="ft[application/msword]" title="Application par défaut">Application pour les fichiers MIME <strong>application/msword</strong> :</label></th>
    <td><input type="text" size="40" name="ft[application/msword]" value="<?php echo $dPconfig['ft']['application/msword'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="ft[text/html]" title="Application par défaut">Application pour les fichiers MIME <strong>text/html</strong> :</label></th>
    <td><input type="text" size="40" name="ft[text/html]" value="<?php echo $dPconfig['ft']['text/html'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="ft[application/pdf]" title="Application par défaut">Application pour les fichiers MIME <strong>application/pdf</strong> :</label></th>
    <td><input type="text" size="40" name="ft[application/pdf]" value="<?php echo $dPconfig['ft']['application/pdf'] ?>" /></td>
  </tr>
-->
</table>


<table class="form">

  <tr>
    <th class="category">Validation obligatoire</th>
  </tr>

  <tr>
    <td class="button"><button class="submit" type="submit">Valider la configuration</button></td>
  </tr>
</table>

</form>

<?php showFooter(); ?>
