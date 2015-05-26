{{* $Id: $ *}}
 
{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !preg_match("/-[0-9]+/", $object_guid)}}
  <h2>{{tr}}CCompteRendu-store_to_send{{/tr}}</h2>
  {{mb_return}}
{{/if}}

<script>
  Main.add(function() {
    var tabMail = $("tabMail");
    destinataires_courrier.each(function(item) {
      var disabled = '';
      if (item.email == null || item.email.indexOf("@") == -1) {
        item.email = '';
        disabled = "disabled = 'disabled'";
      }
      tabMail.insert(
        "<tr>\
           <td>\
             <input type='radio' name='destinataire' " + disabled + "/>\
           </td>\
           <td>" + item.tag + "\
           </td>\
           <td>" + item.nom + "\
           </td>\
           <td>" + item.email + "\
           </td>\
         </tr>");
    });
    tabMail.insert(
      "<tr>\
         <td>\
           <input type='radio' name='destinataire'/>\
         </td>\
         <td>\
         Autre\
         </td>\
         <td colspan='2'><input type='text' name='dest' onfocus='this.up().previous().previous().down().checked = \"true\"' value=''>\
         </td>\
       </tr>");
  });

  sendMail = function() {
    var destinataire = document.body.select("input[name=destinataire]:checked");
  
    // S'il n'y a pas de destinataire cochés ou si ce nombre est différent de 1
    if (destinataire.length != 1) {
      alert("Veuillez choisir un destinataire.");
      return;
    } else {
      // Sinon on peut préparer l'envoi du mail
      destinataire = destinataire[0].up().next();
      var nom = destinataire.next().innerHTML;
      var email = '';
      
      if (destinataire.innerHTML.indexOf("Autre") != -1) {
        nom = '';
        email = destinataire.next().down().value;
      }
      else {
        email = destinataire.next().next().innerHTML;
      }
      // Test d'intégrité de l'adresse mail
      if (email.indexOf('@') == -1) {
        alert('L\'adresse n\'est pas valide');
        return;
      }

      var form = getForm("formSendMail");

      var url = new Url("compteRendu", "ajax_send_mail");
      url.addParam("nom", nom);
      url.addParam("email", email);
      url.addParam("subject", $V(form.subject));
      url.addParam("body", $V(form.body));
      url.addParam("object_guid", '{{$object_guid}}');

      document.body.down("#systemMsg").style.display="block";
      url.requestUpdate(document.body.down("#systemMsg"), {method: 'post', getParameters: {m: 'compteRendu', a: 'ajax_send_mail'}});
      Control.Modal.close();
    }
  }    
</script>

<form name="formSendMail" method="get">
  <p>
    <label>{{tr}}CCompteRendu.mail_subject{{/tr}} :
      <input type="text" name="subject" value="{{tr}}CCompteRendu.default_mail_subject{{/tr}}" style="width: 500px;"/>
    </label>
  </p>
  <p>
    <label>{{tr}}CCompteRendu.mail_body{{/tr}} :
      <textarea name="body">{{tr}}CCompteRendu.default_mail_body{{/tr}}</textarea>
    </label>
  </p>
  <div style="overflow: auto;">
    <table style="width: 100%;" id="tabMail" class="tbl">
      <tr>
        <th>
        </th>
        <th>
          Qualité
        </th>
        <th>
          Nom
        </th>
        <th>
          Adresse mail
        </th>
      </tr>
    </table>
  </div>
  <div style="padding-top: 10px; width: 100%; text-align: center;">
    <button class="cancel" type="button" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
    <button class="tick" type="button" onclick="sendMail();">{{tr}}CCompteRendu.send_mail{{/tr}}</button>
  </div>
</form>