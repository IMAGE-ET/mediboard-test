<?php
/**
 * Ending installation step
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkconfig.php";
require_once "includes/checkstructure.php";
require_once "includes/checkauth.php";

showHeader(); 

?>

<script type="text/javascript">

function checkForm(oForm) {
  if (oForm.password1.value != oForm.password2.value) {
    alert("Les deux mots de passe ne sont pas identiques");
    oForm.reset();
    oForm.password1.focus();
    return false;
  }
  
  if (oForm.password1.value.length < 5) {
    alert("Le mot de passe est trop court (moins de 5 caractères)");
    oForm.reset();
    oForm.password1.focus();
    return false;
  }
  
  return true;
}

</script>
  
<h2>Finalisation de l'installation</h2>

<h3>Changement du mot de passe administrateur</h3>

<p>
  L'assistant d'installation du framework général est fonctionnel mais nécessite d'être
  sécurisé. Il est obligatoire de fournir un mot de passe administrateur sûr.
</p>

<div class="small-warning">
  L'administrateur a pour login admin. Attention, toute la sécurité du système se résume à la sécurité de ce mot de passe. 
  Il est recommandé d'utiliser une séquence de plus de 4 caractères composée de lettres, 
  minuscules et majuscules, de chiffres et d'autres symboles comme @$%^, etc.
</div>

<form name="changePassword" action="07_finish.php" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="changePassword" value="true" />
<table class="form">

  <tr>
    <th class="category" colspan="2">Changer le mot de passe administrateur</th>
  </tr>

  <tr>
    <th><label for="password1" title="Saisir un mot de passe fiable">Saisir le mot de passe :</label></th>
    <td><input type="password" size="20" id="password1" name="password1" value="" autofocus /></td>
  </tr>

  <tr>
    <th><label for="password2" title="Re-saisir le mot de passe pour vérification">Re-saisir le mot de passe :</label></th>
    <td><input type="password" size="20" id="password2" name="password2" value="" /></td>
  </tr>


  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">Valider le mot de passe</button>
    </td>
  </tr>

</table>

</form>

<?php
if (@$_POST["changePassword"] && $_POST["password1"] === $_POST["password2"]) {
  $password = $_POST["password1"];
  
  // TODO salt will never be ready for now, fix it
  $db->query("UPDATE $table SET $passCol = ? WHERE $userCol = 'admin'", array(md5($password)));
?>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Statut</th>
</tr>

<tr>
  <td>Modification du mot de passe 'admin'</td>
  <td>
    <?php if (!$res instanceof PEAR_Error) { ?>
    <div class="info">Mot de passe modifié</div>
    <?php } else { ?>
    <div class="error">
      Erreur lors du changement de mot de passe
      <br />
      <?php echo $res->getMessage(); ?>
    </div>
    <?php } ?>
  </td>
</tr>

</table>

<?php } ?>

<h3>Installer et configurer les modules</h3>

<?php
if (@$_POST["changePassword"] && $password != "admin") {
?>
<div class="small-success">
  Félicitations !
  <br />Le framework de Mediboard est maintenant opérationnel.
</div>
<?php } ?>

<p>
  A cette étape le framework général de Mediboard est fonctionnel. Il est maintenant 
  nécessaire d'installer et paramétrer un par un les modules que vous souhaitez utiliser.
</p>

<div class="small-info">
  A l'heure actuelle, les couplages inter-modules sont encore assez importants, 
  c'est pourquoi il est recommandé sinon obligatoire de tous les installer, quitte à les
  désactiver ou les masquer du menu principal (<em>cf.</em> Administration des modules).
</div>

<p>  
  A partir de maintenant, il est nécessaire de s'authentifier auprès du système en tant
  qu'administatreur pour pouvoir configurer les modules.
</p>

<div class="navigation">
  <a class="button tick" href="../?m=system&amp;a=domodsql&amp;cmd=upgrade-core">
    Me rendre à la page d'administration des modules
  </a>
</div>

<?php showFooter(); ?>