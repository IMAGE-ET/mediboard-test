{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=light value=""}}

<table class="main">
  <tr>
    <td>
      <form name="editSourcePOP-{{$source->name}}" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, { onComplete : (function() {
              if (this.up('.modal')) {
                Control.Modal.close();
              } else {
                ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
              }}).bind(this)})">

        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_pop_aed" />
        <input type="hidden" name="source_pop_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" />

        <input type="hidden" name="object_class" value="{{$source->object_class}}" />
        <input type="hidden" name="object_id" value="{{$source->object_id}}" />

        <fieldset>
          <legend>{{tr}}CSourcePOP{{/tr}}</legend>

          <table class="form">
            {{mb_include module=system template=CExchangeSource_inc}}

            <tr>
              <th>{{mb_label object=$source field="port"}}</th>
              <td>{{mb_field object=$source field="port"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="type"}}</th>
              <td>{{mb_field object=$source field="type" typeEnum="radio"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="auth_ssl"}}</th>
              <td>{{mb_field object=$source field="auth_ssl" typeEnum="radio"}}</td>
            </tr>
            {{if $source->_id}}
            <tr>
              <th>{{mb_label object=$source field="object_id"}}</th>
              <td>
                {{if $source->_ref_mediuser}}
                  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$source->_ref_mediuser}}
                {{else}}
                  <input type="text" readonly="readonly" name="_object_view"
                         value="{{$source->_ref_metaobject->_view}}" size="50"/>
                {{/if}}
              </td>
            </tr>
            {{/if}}
            <tr>
              <th>{{mb_label object=$source field="user"}}</th>
              <td>{{mb_field object=$source field="user" size="50"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="password"}}</th>
              {{assign var=placeholder value="Pas de mot de passe"}}
              {{if $source->password}}
                {{assign var=placeholder value="Mot de passe enregistré"}}
              {{/if}}
              <td>{{mb_field object=$source field="password" placeholder=$placeholder size="30"}}</td>
            </tr>

            <tr {{if !$app->_ref_user->isAdmin()}}style="display:none;"{{/if}}>
              <th>{{mb_label object=$source field="timeout"}}</th>
              <td>{{mb_field object=$source field="timeout" register=true increment=true form="editSourcePOP-`$source->name`" size=3 step=1 min=0}}</td>
            </tr>

            <tr>
              <td class="button" colspan="2">
              {{if $source->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>

                <button class="trash" type="button" onclick="confirmDeletion(this.form,
                  { ajax: 1, typeName: '', objName: '{{$source->_view}}'},
                  { onComplete: (function() {
                  if (this.up('.modal')) {
                    Control.Modal.close();
                  } else {
                    ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
                  }}).bind(this.form)})">

                  {{tr}}Delete{{/tr}}
                </button>
                {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
              </td>
            </tr>
          </table>
        </fieldset>
      </form>
    </td>
  </tr>

  {{if !$light}}
  <tr>
    <td>
      <fieldset>
        <legend>{{tr}}utilities-source-pop{{/tr}}</legend>

        <table class="main tbl">
          <!-- Test de connexion pop -->
          <tr>
            <td class="button">
              {{mb_include module=system template=CSourcePOP_tools_inc _source=$source}}
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  {{/if}}
</table>