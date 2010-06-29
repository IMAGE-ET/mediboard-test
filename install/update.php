<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

require_once("checkauth.php");
require_once("header.php");
showHeader();

?>

<script type="text/javascript">
function checkPassword(form) {
  if(form.elements.passwd.value == '') {
    alert("Pas de mot de passe entré !");
    return false;
  }
  else {
    loadRequest('real_res');
    return true;
  }
}

function loadRequest(name) {
  var eIframe = document.getElementsByName(name)[0];
  eIframe.contentWindow.document.documentElement.innerHTML =
    '<div style="margin: 10px;">Chargement en cours</div>';
  eIframe.style.background = "#fff";
}

window.onload = function() {
  if(document.getElementsByName("passwd")[0].value == "") {
    document.getElementById("update").disabled = "disabled";
  }
}
</script>

<h2>Mise à jour du système</h2>

<div class="big-info">
  Cette étape permet de :
  <ul>
    <li>Connaître la révision actuelle du système, et affiche s'il existe une révision plus récente</li>
    <li>Mettre à jour le système à la révision la plus récente, ou à une révision spécifiée</li>
  </ul>
  <br/>
  Concernant la mise à jour, les actions suivantes sont à faire au préalable dans un terminal (sous Linux ou MacOS X) :<br/>
  <ul>
    <li>Tapez la commande suivante :
      <pre>sudo visudo</pre>
      Dans le fichier qui s'est ouvert, rajouter les lignes suivantes à la fin :
      <pre>[nom de l'utilisateur Apache] ALL=(ALL) PASSWD: /bin/sh
Defaults:[nom de l'utilisateur Apache] timestamp_timeout=0
Defaults:[nom de l'utilisateur Apache] passwd_timeout=0</pre>
      Sauvez et quittez.
    </li>
    <li>
      Entrez les commandes suivantes :
      <pre>sudo su</pre>
      <pre>passwd [nom de l'utilisateur Apache]</pre>
      Entrez le nouveau mot de passe pour l'utilisateur Apache, appuyez sur Entrée puis confirmez en l'écrivant une nouvelle fois (et en validant de nouveau avec Entrée).<br/>
      Ce mot de passe va permettre d'effectuer la mise à jour du système.
    </li>
  </ul>
</div>

<form name="info" action="updatescript.php" target="info_res" method="post">
  <input type="hidden" name="action" value="info" />
    
  <fieldset>
    <legend>Informations de mise à jour</legend>
    <button class="change" name="button_info" onclick="loadRequest('info_res');">Infos</button>
  
    <iframe name="info_res" src="updatescript.php"></iframe>
  </fieldset>
</form>

<form name="real" target="real_res" action="updatescript.php" method="post" onsubmit="return checkPassword(this)">
  <input type="hidden" name="action" value="real" />
  
  <fieldset>
    <legend>Mise à jour</legend>
    
    <table>
      <tr>
        <th>Révision</th>
        <td>
          <input type="text" name="rev" />
        </td>
        <td rowspan="2">
          <div class="small-warning">
            La mise à jour requiert le mot de passe de l'utilisateur Apache.  Le numéro de révision est optionnel.
          </div>
        </td>
      </tr>
      <tr>
        <th>Mot de passe</th>
        <td>
          <input type="password" name="passwd" onkeyup="var bupdate = document.getElementById('update'); if(this.value.length == 0) bupdate.disabled = 'disabled'; else bupdate.disabled = '';"/>
        </td>
      </tr>
    </table>
    
    <button class="change" id="update" name="button_real">Mise à jour</button>
    
    <iframe name="real_res" src="updatescript.php"></iframe>
  </fieldset>
</form>

<?php showFooter(); ?>