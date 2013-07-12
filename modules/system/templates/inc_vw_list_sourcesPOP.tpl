{{*
  * list of source pop for one mediuser
  *  
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

</table>
<script>
  POP = {
    connexion: function (exchange_source_name, action) {
      var url = new Url("system", "ajax_connexion_pop");
      url.addParam("exchange_source_name", exchange_source_name);
      url.addParam("type_action", action);
      url.requestUpdate("test_result_" + exchange_source_name);
    },

    getOldEmail: function (account_id, account_name) {
      var url = new Url("messagerie", "cron_update_pop");
      url.addParam("account_id", account_id);
      url.addParam("import", 1);
      url.requestUpdate("test_result_" + account_name, function() {
        if($('messagerie-auto').checked) {
          POP.getOldEmail.curry(account_id, account_name).delay(2);
        }
      });
    }
  }
</script>

<table class="tbl">
  <tr>
    <th>{{tr}}CSourcePOP-libelle{{/tr}}</th>
    <th>{{tr}}CSourcePOP-user{{/tr}}</th>
    <th>{{tr}}CSourcePOP-type{{/tr}}</th>
    <th>{{tr}}CSourcePOP-active{{/tr}}</th>
  </tr>
{{foreach from=$pop_source item=source_pop}}
  <tr>
    <td>
      <button class="edit notext" onclick="exchangeSources.popModal('{{$source_pop->_id}}')">{{tr}}Edit{{/tr}}</button>{{mb_value object=$source_pop field=libelle}}
    </td>
    <td>{{mb_value object=$source_pop field=user}}</td>
    <td>{{mb_value object=$source_pop field=type}}</td>
    <td>{{mb_value object=$source_pop field=active}}</td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="lookup notext" onclick="POP.connexion('{{$source_pop->name}}','connexion')">Connexion</button>
      <button class="search notext" onclick="POP.connexion('{{$source_pop->name}}', 'listBox')">Liste des boites filles</button>
      {{if $can->admin}}<input type="checkbox" id="messagerie-auto" name="messagerie-auto" value="1"/><button class="change notext" onclick="POP.getOldEmail('{{$source_pop->_id}}','{{$source_pop->name}}')">Récupérer les anciens mails</button>{{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="4" id="test_result_{{$source_pop->name}}"></td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="3" class="empty">{{tr}}CSourcePOP.none{{/tr}}</td></tr>
{{/foreach}}
