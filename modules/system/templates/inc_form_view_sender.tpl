{{* $Id: inc_edit_messages.tpl 10391 2010-10-14 14:34:09Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10391 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-{{$sender->_guid}}" action="?m={{$m}}" method="post" onsubmit="return ViewSender.onSubmit(this);">

<input type="hidden" name="@class" value="{{$sender->_class_name}}" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$sender}}

<table class="form">

<tr>
  {{if $sender->_id}}
  <th class="title modify" colspan="2">
    {{mb_include module=system template=inc_object_notes      object=$sender}}
    {{mb_include module=system template=inc_object_idsante400 object=$sender}}
    {{mb_include module=system template=inc_object_history    object=$sender}}

    {{tr}}CViewSender-title-modify{{/tr}} '{{$sender}}'
  {{else}}
  <th class="title" colspan="2">
    {{tr}}CViewSender-title-create{{/tr}}
  {{/if}}
  </th>
</tr>

<tr>
  <th class="narrow">{{mb_label object=$sender field=name}}</th>
  <td>{{mb_field object=$sender field=name}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sender field=description}}</th>
  <td>{{mb_field object=$sender field=description}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sender field=params}}</th>
  <td>{{mb_field object=$sender field=params}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sender field=period}}</th>
  <td>{{mb_field object=$sender field=period}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sender field=offset}}</th>
  <td>{{mb_field object=$sender field=offset}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sender field=active}}</th>
  <td>{{mb_field object=$sender field=active}}</td>
</tr>

<tr>
  <td class="button" colspan="2">
    {{if $sender->_id}}
    <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    <button class="trash" type="button" onclick="ViewSender.confirmDeletion(this.form);">
      {{tr}}Delete{{/tr}}
    </button>
    {{else}}
    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
</tr>

</table>

</form>
