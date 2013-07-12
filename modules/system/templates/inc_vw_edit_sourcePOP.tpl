{{*
  * Edit a pop source
  *  
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<form name="editSourcePOP-{{$source->name}}" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_source_pop_aed" />
  <input type="hidden" name="source_pop_id" value="{{$source->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="Control.Modal.close();exchangeSources.refreshListPOP();"/>
  <input type="hidden" name="object_class" value="{{$source->object_class}}" />
  <input type="hidden" name="object_id" value="{{$source->object_id}}" />

  <table class="form">
    <tr>
      <th class="category" colspan="2">
      {{tr}}config-source-pop{{/tr}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$source field="libelle"}}</th>
      <td>{{mb_field object=$source field="libelle"}}</td>
    </tr>
    {{mb_include template="CExchangeSource_inc"}}
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
      <th>{{mb_label object=$source field="user"}}</th>
      <td>{{mb_field object=$source field="user"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$source field="password"}}</th>
      {{assign var=placeholder value="Pas de mot de passe"}}
      {{if $source->password}}
        {{assign var=placeholder value="Mot de passe enregistré"}}
      {{/if}}
      <td>{{mb_field object=$source field="password" placeholder=$placeholder}}</td>
    </tr>
    <tr {{if !$can->admin}}style="display:none;"{{/if}}>
      <th>{{mb_label object=$source field="cron_update"}}</th>
      <td>{{mb_field object=$source field="cron_update"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$source field="extension"}}</th>
      <td>{{mb_field object=$source field="extension"}}</td>
    </tr>
    <tr {{if !$can->admin}}style="display:none;"{{/if}}>
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