{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$dialog}}
{{include file=../../system/templates/filter_history.tpl}}
{{/if}}

<table class="tbl">
  {{if $dialog && $object->_id}}
  <tr>
    <th colspan="5" class="title">
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
    {{/if}}
    <th>{{mb_title class=CUserLog field=user_id}}</th>
    <th colspan="2">{{mb_title class=CUserLog field=date}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
    {{if !$dialog}}
      <th>{{mb_title class=CUserLog field=ip_address}}</th>
    {{/if}}
  </tr>
  
  {{include file=../../system/templates/inc_history_line.tpl logs=$list}}
</table>