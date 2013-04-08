{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-{{$message->_guid}}" action="?m={{$m}}" method="post" onsubmit="return Message.onSubmit(this);">

<input type="hidden" name="@class" value="{{$message->_class}}" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$message}}

<table class="form">

{{mb_include module=system template=inc_form_table_header object=$message}}

<tr>
  <th class="narrow">{{mb_label object=$message field="deb"}}</th>
  <td>{{mb_field object=$message field="deb" form="Edit-`$message->_guid`" register=true}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="fin"}}</th>
  <td>{{mb_field object=$message field="fin" form="Edit-`$message->_guid`" register=true}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="module_id"}}</th>
  <td>
    <select name="module_id">
      <option value="">&mdash; {{tr}}All{{/tr}}</option>
      {{foreach from=$modules item=_module}}
      <option value="{{$_module->_id}}" {{if $_module->_id == $message->module_id}} selected="selected" {{/if}}>{{$_module}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$message field="group_id"}}</th>
  <td>
    <select name="group_id">
      <option value="">&mdash; {{tr}}All{{/tr}}</option>
      {{foreach from=$groups item=_group}}
      <option value="{{$_group->_id}}" {{if $_group->_id == $message->group_id}} selected="selected" {{/if}}>{{$_group}}</option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$message field="urgence"}}</th>
  <td>{{mb_field object=$message field="urgence" typeEnum="radio"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="titre"}}</th>
  <td>{{mb_field object=$message field="titre"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="corps"}}</th>
  <td>{{mb_field object=$message field="corps"}}</td>
</tr>

<tr>
  <td class="button" colspan="2">
    {{if $message->_id}}
    <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    <button class="duplicate oneclick" type="submit" onclick="Message.duplicate(this.form);">
      {{tr}}Duplicate{{/tr}}
    </button>
    <button class="trash singleclick" type="button" onclick="Message.confirmDeletion(this.form);">
      {{tr}}Delete{{/tr}}
    </button>
    {{else}}
    <button class="submit oneclick" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
</tr>

<tr>
  <td colspan="2"><hr/></td>
</tr>

{{if !$message_smtp->_id}}
<tr>
  <td colspan="2">
    <div class="small-info">
      {{tr}}CMessage-info-smtp_not_config{{/tr}}
    </div>
  </td>
</tr>
{{else}}

<tr>
  <th>{{mb_label object=$message field="_email_from"}}</th>
  <td>{{mb_field object=$message field="_email_from"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="_email_to"}}</th>
  <td>{{mb_field object=$message field="_email_to"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$message field="_email_details"}}</th>
  <td>{{mb_field object=$message field="_email_details" rows="6"}}</td>
</tr>
{{/if}}

<tr>
  <td class="button" colspan="2">
    <input name="_email_send" type="hidden" />
    {{if $message->_id}}
    <button class="modify oneclick" type="submit" onclick="$V(this.form._email_send, '1');">
      {{tr}}Save{{/tr}} &amp; {{tr}}Send-email{{/tr}}
    </button>
    {{else}}
    <button class="submit oneclick" type="submit" onclick="$V(this.form._email_send, '1');">
      {{tr}}Create{{/tr}} &amp; {{tr}}Send-email{{/tr}}
    </button>
    {{/if}}
  </td>
</tr>

</table>

</form>
