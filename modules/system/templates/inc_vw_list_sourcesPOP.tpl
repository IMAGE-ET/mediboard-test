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
    connexion: function (exchange_source_name) {
      var url = new Url("system", "ajax_connexion_pop");
      url.addParam("exchange_source_name", exchange_source_name);
      url.addParam("type_action", "connexion");
      url.requestUpdate("test_result_" + exchange_source_name);
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
    <td class="button"><button class="lookup" onclick="POP.connexion('{{$source_pop->name}}')">test</button></td><td colspan="3" id="test_result_{{$source_pop->name}}"></td>
  </tr>
  {{foreachelse}}
  <tr><td colspan="3" class="empty">{{tr}}CSourcePOP.none{{/tr}}</td></tr>
{{/foreach}}
