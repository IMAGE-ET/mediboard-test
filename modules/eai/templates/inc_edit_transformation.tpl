{{*
 * Edit transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

<style>
  .EAITransformation-select {
    width: 100%;
    height: 120px;
    font-size: 10px;
  }
</style>

{{assign var=actor value=$transformation->_ref_actor}}

<form name="edit-{{$transformation->_guid}}" method="post" onsubmit="return EAITransformation.onSubmit(this)">
  {{mb_key object=$transformation}}
  {{mb_class object=$transformation}}
  <input type="hidden" name="del" value="0" />

  <input type="hidden" name="eai_transformation_ruleset_id" value="{{$transformation->eai_transformation_ruleset_id}}" />
  <input type="hidden" name="callback"
         value="EAITransformation.refreshList.curry('{{$message_class}}', '{{$event_class}}', '{{$actor->_guid}}')" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$transformation}}

    <tr>
      <th>{{mb_label object=$transformation field="standard"}}</th>
      <td>
        {{mb_field object=$transformation field="standard" disabled="true"}}
        {{if $transformation->standard}}
          <button type="button" onclick="$V(this.form.standard, '');" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transformation field="domain"}}</th>
      <td>
        {{mb_field object=$transformation field="domain" disabled="true"}}
        {{if $transformation->standard}}
          <button type="button" onclick="$V(this.form.domain, '');" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transformation field="profil"}}</th>
      <td>
        {{mb_field object=$transformation field="profil" disabled="true"}}
        {{if $transformation->profil}}
          <button type="button" onclick="$V(this.form.profil, '');" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transformation field="transaction"}}</th>
      <td>
        {{mb_field object=$transformation field="transaction" disabled="true"}}
        {{if $transformation->transaction}}
          <button type="button" onclick="$V(this.form.transaction, '');" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transformation field="message"}}</th>
      <td>
        {{mb_field object=$transformation field="message" disabled="true"}}
        {{if $transformation->message}}
          <button type="button" onclick="$V(this.form.message, '');" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transformation field="version"}}</th>
      <td>
        {{mb_field object=$transformation field="version" disabled="true"}}
        {{if $transformation->version}}
          <button type="button" onclick="$V(this.form.version, '');" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transformation field="active"}}</th>
      <td>{{mb_field object=$transformation field="active"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $transformation->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form, {
            ajax:1,
            typeName:&quot;{{tr}}{{$transformation->_class}}.one{{/tr}}&quot;,
            objName:&quot;{{$transformation->_view|smarty:nodefaults|JSAttribute}}&quot;},
            { onComplete: function() {
              Control.Modal.close();
              EAITransformation.refreshList('{{$message_class}}', '{{$event_class}}', '{{$actor->_guid}}');
            }})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>