<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
require_once("checkauth.php");
require_once("header.php");
showHeader();

?>

<script type="text/javascript">
  function checkmdp(form) {
    if(form.elements.passwd.value == '') {
      alert("Pas de mot de passe entré !");
      return false;
    }
    else {
      requestloading('real_res');
      return true;
    }
  }

  function requestloading(name) {
    var eIframe = document.getElementsByName(name)[0];
    eIframe.contentWindow.document.getElementsByTagName("body")[0].style.background = "#777 url(../style/mediboard/images/icons/ajax-loading.gif) no-repeat center";
    eIframe.contentWindow.document.getElementsByTagName("body")[0].style.height = "130px";
  }
</script>

<h2>Mise à jour du système</h2>

<div class="big-info">
  Cette étape permet de :
  <ul>
    <li>Connaître la révision actuelle du système, et affiche s'il existe une révision plus récente.</li>
    <li>Mettre à jour le système à la révision la plus récente, ou à une révision spécifiée.</li>
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
      Sauvez et quittez.</pre>
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


<h3>Infos sur le système</h3>

<form name="info" action="updatescript.php" target="info_res" method="post">
  <input type="hidden" name="action" value="info"></input>
  <h3><button class="change" name="button_info" onclick="requestloading('info_res');">Infos</button></h3>
</form>
<h2><iframe name="info_res" src='about:blank' style="display: block; width: 100%; background: #fff; border: 1px solid #000;" ></iframe></h2>

<br/>

<div class="small-warning">
  La mise à jour requiert le mot de passe de l'utilisateur Apache.  Le numéro de révision est optionnel.
</div>

<h3>Mise à jour</h3>

<form name="real" target="real_res" action="updatescript.php" method="post" onsubmit="return checkmdp(this);">
  <input type="hidden" name="action" value="real"></input>
  <h3><button class="change" name="button_real">Mise à jour</button></h3>
  <table>
    <tr>
      <td>
        <h3>Révision : </h3>
      </td>
      <td>
        <input type="text" name="rev"></input>
      </td>
    </tr>
    <tr>
      <td>
        <h3>Mot de passe :</h3>
      </td>
      <td>
        <input type="password" name="passwd"></input>
      </td>
    </tr>
  </table>
</form>
<iframe name="real_res" style="display: block; width: 100%; background: #fff; border: 1px solid #000;"></iframe>

<?php showFooter(); ?>