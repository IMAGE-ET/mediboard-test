<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision$
* @author Thomas Despoix
*/


require_once("checkauth.php");
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

?>

<?php showHeader(); ?>

<h2>Création du fichier de configuration</h2>

<form name="configure" action="configure.php" method="post">

<table class="form">

  <tr>
    <th class="category" colspan="2">Configuration générale</th>
  </tr>

  <tr>
    <th><label for="root_dir" title="Repertoire racine. Pas de slash final. Utiliser les slashs aussi pour MS Windows">Répertoire racine :</label></th>
    <td><input type="text" size="40" name="root_dir" value="<?php echo $dPconfig["root_dir"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="company_name" title="Nom de la société">Nom de la société :</label></th>
    <td><input type="text" size="40" name="company_name" value="<?php echo $dPconfig['company_name'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="page_title" title="Titre de la page web dans la barre de navigation">Titre de la page web :</label></th>
    <td><input type="text" size="40" name="page_title" value="<?php echo $dPconfig['page_title'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="base_url" title="Url Racine pour le système">Url racine :</label></th>
    <td><input type="text" size="40" name="base_url" value="<?php echo $dPconfig['base_url'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="site_domain" title="Nom de domaine de premier du système">Nom de domaine :</label></th>
    <td><input type="text" size="40" name="site_domain" value="<?php echo $dPconfig['site_domain'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="offline" title="Nom de domaine de premier du système">Mode maintenance :</label></th>
    <td><input type="text" size="40" name="offline" value="<?php echo $dPconfig['offline'] ?>" /></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Configuration de la base de données principale</th>
  </tr>

  <tr>
    <th><label for="dbtype" title="Type de base de données. Seul mysql est possible pour le moment">Type de base de données :</label></th>
    <td class="readonly"><input type="text" readonly="readonly" size="20" name="dbtype" value="<?php echo @$dPconfig["dbtype"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbhost]" title="Nom de l'hôte">Nom de l'hôte :</label></th>
    <td><input type="text" size="40" name="db[std][dbhost]" value="<?php echo @$dPconfig["db"]["std"]["dbhost"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbname]" title="Nom de la base">Nom de la base :</label></th>
    <td><input type="text" size="40" name="db[std][dbname]" value="<?php echo @$dPconfig["db"]["std"]["dbname"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbuser]" title="Nom de l'utilisateur">Nom de l'utilisateur :</label></th>
    <td><input type="text" size="40" name="db[std][dbuser]" value="<?php echo @$dPconfig["db"]["std"]["dbuser"]; ?>" /></td>
  </tr>

  <tr>
    <th><label for="db[std][dbpass]" title="Mot de passe de l'utililisateur'">Mot de passe :</label></th>
    <td><input type="text" size="40" name="db[std][dbpass]" value="<?php echo @$dPconfig["db"]["std"]["dbpass"]; ?>" /></td>
  </tr>

  <tr>
    <th class="category" colspan="2">Paramètres d'IHM</th>
  </tr>

  <tr>
    <th><label for="currency_symbol" title="Symbole monétaire. Entités HTML acceptées">Symbole monétaire :</label></th>
    <td><input type="text" size="40" name="currency_symbol" value="<?php echo $dPconfig['currency_symbol'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="hide_confidential" title="Brouiller les données confidentielles. Utiles pour le monde de démonstration">Brouiller les données confidentielles ?</label></th>
    <td><input type="text" size="40" name="hide_confidential" value="<?php echo $dPconfig['hide_confidential'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="locale_warn" title="Alerter les absence de traduction. En ajoutant une marque autour">Alerter les absence de traduction ? </label></th>
    <td><input type="text" size="40" name="locale_warn" value="<?php echo $dPconfig['locale_warn'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="debug" title="Version de débogage. Affiche toutes les alertes PHP et la console Smarty">Version de débogage ?</label></th>
    <td><input type="text" size="40" name="debug" value="<?php echo $dPconfig['debug'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="readonly" title="Version en lecture seule. Empeche toute modification des données de Mediboard">Version readonly ?</label></th>
    <td><input type="text" size="40" name="readonly" value="<?php echo $dPconfig['readonly'] ?>" /></td>
  </tr>

  <tr>
    <th><label for="shared_memory" title="Choisir quelle extension doit tenter de gérer la mémoire partagée (celle-ci doit être installée)">Mémoire partagée ?</label></th>
    <td>
      <select name="shared_memory" size="1">
        <option value="none"         <?php if ($dPconfig['shared_memory'] == 'none'        ) { echo 'selected="selected"'; } ?> >Disque</option>
        <option value="eaccelerator" <?php if ($dPconfig['shared_memory'] == 'eaccelerator') { echo 'selected="selected"'; } ?> >eAccelerator</option>
        <option value="apc"          <?php if ($dPconfig['shared_memory'] == 'apc'         ) { echo 'selected="selected"'; } ?> >APC</option>
      </select>
      
      <div>
      <?php
        $rootName = basename($dPconfig["root_dir"]);
        require_once("../classes/sharedmemory.class.php");
        require_once("../modules/system/httpreq_do_empty_shared_memory.php");
      ?>
      </div>
    </td>
  </tr>

  <tr>
    <th class="category" colspan="2">Paramètres d'indexation de fichiers</th>
  </tr>

  <tr>
    <th><label for="ft[default]" title="Application par défaut">Application par défaut :</label></th>
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

  <tr>
    <th class="category" colspan="2">Graphiques</th>
  </tr>

  <tr>
    <th><label for="graph_engine" title="Graphique">Selection du type de graphique :</label></th>
    <td>
      <select name="graph_engine">
        <option value="jpgraph" <?php if($dPconfig['graph_engine'] == 'jpgraph'){echo 'selected="selected"';} ?> >jpgraph</option>
        <option value="eZgraph" <?php if($dPconfig['graph_engine'] == 'eZgraph'){echo 'selected="selected"';} ?> >eZgraph</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="graph_svg" title="Graphique">Choix du mode SVG :</label></th>
    <td>
      <select name="graph_svg">
        <option value="oui" <?php if($dPconfig['graph_svg'] == 'oui'){echo 'selected="selected"';} ?> >oui</option>
        <option value="non" <?php if($dPconfig['graph_svg'] == 'non'){echo 'selected="selected"';} ?> >non</option>
      </select>
    </td>
  </tr>
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
