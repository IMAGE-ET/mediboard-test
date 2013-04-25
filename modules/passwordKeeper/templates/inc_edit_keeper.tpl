{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

<script type="text/javascript">
  Main.add(function() {
    {{if $deletion}}
      confirmDeletion(
        getForm('editKeeper'),
        { ajax:true, typeName:'le trousseau',objName:"{{$keeper->_view|smarty:nodefaults|JSAttribute}}" },
        {
          onComplete : function() { Keeper.showListKeeper(); },
          check: function() { return true; }
        }
    );
    {{/if}}

    {{if $keeper->_id && !$_passphrase}}
      Keeper.promptPassphrase("{{$keeper->_id}}");
    {{/if}}

    {{if $_passphrase}}
      Keeper.showListCategory("{{$keeper->_id}}");
    {{/if}}
  })
</script>

{{if !$keeper->_id || $_passphrase}}
  <form name="editKeeper" action="?m={{$m}}" method="post" onsubmit="return Keeper.submitKeeper(this, '{{$keeper->_id}}')">
    <input type="hidden" name="callback" value="Keeper.showKeeper" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$keeper}}
    {{mb_class object=$keeper}}
    <input type="hidden" name="user_id" value="{{$user->_id}}" />
    <table class="form">
      {{mb_include module=system template=inc_form_table_header object=$keeper colspan=3}}
      <tr>
        <th>{{mb_label object=$keeper field="keeper_name"}}</th>
        <td>{{mb_field object=$keeper field="keeper_name"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$keeper field="is_public"}}</th>
        <td>{{mb_field object=$keeper field="is_public"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$keeper field="_passphrase"}}</th>
        <td>{{mb_field object=$keeper field="_passphrase"}}</td>
      </tr>
      <tr>
        <td class="button" colspan="3">
          <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
          <button class="trash" type="button" onclick="Keeper.promptPassphrase('{{$keeper->_id}}', true)">{{tr}}Delete{{/tr}}</button>
          {{if $keeper->_id}}
            <button class="hslip" type="button" onclick="Keeper.promptPassphrase('{{$keeper->_id}}', null, true)">{{tr}}CPasswordKeeper-export{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
  </form>

  {{if $keeper->_id}}
    <div id="vw_list_category"></div>
  {{/if}}
{{/if}}