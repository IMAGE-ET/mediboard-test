{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;message_id=0">
  Créer un message
</a>

<form name="Filter" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
    
<table class="form">
  <tr>
    <th>{{mb_label object=$filter field=_status}}</th>
    <td>{{mb_field object=$filter field=_status defaultOption="&mdash; Tous"}}</td>
  </tr>
</table>

</form>

<table class="tbl">

<tr>
  <th colspan="10">
    {{$messages|@count}}
  	{{tr}}CMessage{{/tr}}s
  	{{tr}}found{{/tr}}
  </th>
</tr>

<tr>
  <th>{{mb_title class=CMessage field=titre}}</th>
  <th>{{mb_title class=CMessage field=module_id}}</th>
  <th style="width: 1%">{{mb_title class=CMessage field=deb}}</th>
  <th style="width: 1%">{{mb_title class=CMessage field=fin}}</th>
  <th style="width: 1%">{{mb_title class=CMessage field=group_id}}</th>
</tr>

{{foreach from=$messages item=_message}}
<tbody class="hoverable">

<tr {{if $_message->_id == $message->_id}}class="selected"{{/if}}>
  {{assign var="message_id" value=$_message->message_id}}
  {{assign var="href" value="?m=$m&tab=$tab&message_id=$message_id"}}
  <td {{if $_message->urgence == "urgent"}}class="highlight"{{/if}}>
    <strong><a href="{{$href}}">{{mb_value object=$_message field=titre}}</a></strong>
  </td>
  
  <td>
  {{if $_message->module_id}}
    {{$_message->_ref_module}}
  {{else}}
  	{{tr}}All{{/tr}}
  {{/if}}
  </td>
  
  <td>{{mb_value object=$_message field=deb}}</td>
  <td>{{mb_value object=$_message field=fin}}</td>
  
  <td>
  {{if $_message->group_id}}
    {{$_message->_ref_group}}
  {{else}}
    {{tr}}All{{/tr}}
  {{/if}}
  </td>
</tr>

<tr {{if $_message->_id == $message->_id}}class="selected"{{/if}}>
  <td class="text" style="padding: 0 2em;" colspan="10">
  	{{mb_value object=$_message field=corps}}
  </td>
</tr>

</tbody>
{{/foreach}}
  
</table>
