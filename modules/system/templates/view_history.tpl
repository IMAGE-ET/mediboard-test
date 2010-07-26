{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$dialog}}
{{mb_include module=system template=filter_history}}
{{/if}}

<table class="tbl">
  {{if $dialog && $object->_id}}
  <tr>
    <th colspan="7" class="title">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
        Historique de {{$object->_view}}
      </span>
    </th>
  </tr>
  {{/if}}
  
  <tr>
    {{if !$dialog}}
    <th>{{mb_title class=CUserLog field=object_class}}</th>
    <th>{{mb_title class=CUserLog field=object_id}}</th>
    <th>{{mb_title class=CUserLog field=ip_address}}</th>
    {{/if}}
    <th>{{mb_title class=CUserLog field=user_id}}</th>
    <th colspan="2">{{mb_title class=CUserLog field=date}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
    {{if $object->_id}}
    <th>{{tr}}CUserLog-values_before{{/tr}}</th>
    <th>{{tr}}CUserLog-values_after{{/tr}}</th>
		{{/if}}
  </tr>
  
{{mb_include module=system template=inc_history_line logs=$list}}
</table>