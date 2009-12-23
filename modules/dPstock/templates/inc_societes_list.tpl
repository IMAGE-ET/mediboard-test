{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" total=$list_count current=$start step=30}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CSociete field=name}}</th>
    <th>{{mb_title class=CSociete field=contact_name}}</th>
    <th>{{mb_title class=CSociete field=phone}}</th>
    <th>{{mb_title class=CSociete field=email}}</th>
  </tr>
  
  {{foreach from=$list item=curr}}
  <tr>
    <td>
      <a href="?m=dPstock&amp;tab=vw_idx_societe&amp;societe_id={{$curr->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr->_guid}}')">
          {{$curr}}
        </span>
      </a>
    </td>
    <td>{{mb_value object=$curr field=contact_name}}</td>
    <td>{{mb_value object=$curr field=phone}}</td>
    <td>{{mb_value object=$curr field=email}}</td>
  </tr>
  {{/foreach}}
  
</table>