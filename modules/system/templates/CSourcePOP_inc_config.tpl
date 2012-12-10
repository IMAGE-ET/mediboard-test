{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="main">
  <tr>
    <td>
      <form name="editSourcePOP-{{$source->name}}" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, {
              onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_pop_aed" />
        <input type="hidden" name="source_pop_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" />

        <table class="form">
          <tr>
            <th class="category" colspan="2">
            {{tr}}config-source-pop{{/tr}}
            </th>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="name"}}</th>
            <td><input type="text" readonly="readonly" name="name" value="{{$source->name}}" /></td>
          </tr>
          <tr {{if !$app->_ref_user->isAdmin()}}style="display:none;"{{/if}}>
            <th>{{mb_label object=$source field="role"}}</th>
            <td>{{mb_field object=$source field="role"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="host"}}</th>
            <td>{{mb_field object=$source field="host"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="port"}}</th>
            <td>{{mb_field object=$source field="port"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="type"}}</th>
            <td>{{mb_field object=$source field="type"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="auth_ssl"}}</th>
            <td>{{mb_field object=$source field="auth_ssl"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="libelle"}}</th>
            <td>{{mb_field object=$source field="libelle"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="user"}}</th>
            <td>{{mb_field object=$source field="user"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="password"}}</th>
            <td>{{mb_field object=$source field="password"}}</td>
          </tr>
          <tr {{if !$app->_ref_user->isAdmin()}}style="display:none;"{{/if}}>
            <th>{{mb_label object=$source field="timeout"}}</th>
            <td>{{mb_field object=$source field="timeout"}}</td>
          </tr>

          <tr>
            <td class="button" colspan="2">
            {{if $source->_id}}
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              <button type="button" class="trash" onclick="confirmDeletion(this.form, {ajax:1, typeName:'',
                objName:'{{$source->_view|smarty:nodefaults|JSAttribute}}'},
                {onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}')})">
                {{tr}}Delete{{/tr}}
              </button>
              {{else}}
              <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="greedyPane">
      <script type="text/javascript">
        POP = {
          connexion: function (exchange_source_name) {
            var url = new Url("system", "ajax_connexion_pop");
            url.addParam("exchange_source_name", exchange_source_name);
            url.addParam("type_action", "connexion");
            url.requestUpdate("utilities-source-pop-connexion-" + exchange_source_name);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
          {{tr}}utilities-source-pop{{/tr}}
          </th>
        </tr>

        <!-- Test de connexion pop -->
        <tr>
          <td>
            <button type="button" class="search" onclick="POP.connexion('{{$source->name}}');">
            {{tr}}utilities-source-pop-connexion{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td id="utilities-source-pop-connexion-{{$source->name}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>