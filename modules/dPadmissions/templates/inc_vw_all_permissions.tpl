{{* $Id: inc_vw_all_admissions.tpl 11726 2011-04-03 14:06:56Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11726 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="4">
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_permissions&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_permissions&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
		
  <tr>
    <th rowspan="2">Date</th>
  </tr>

  <tr>
    <th class="text">
      <a class={{if $type_externe == 'depart'}}"selected"{{else}}"selectable"{{/if}} title="Départs" href="?m={{$m}}&amp;tab=vw_idx_permissions&amp;type_externe=depart">
        Départ
      </a>
    </th>
    <th class="text">
      <a class={{if $type_externe == 'retour'}}"selected"{{else}}"selectable"{{/if}} title="Retours" href="?m={{$m}}&amp;tab=vw_idx_permissions&amp;type_externe=retour">
        Retour
      </a>
    </th>
  </tr>

  {{foreach from=$days key=day item=counts}}
  <tr {{if $day == $date}}class="selected"{{/if}}>
    {{assign var=day_number value=$day|date_format:"%w"}}
    <td style="text-align: right;
      {{if array_key_exists($day, $bank_holidays)}}
        background-color: #fc0;
      {{elseif $day_number == '0' || $day_number == '6'}}
        background-color: #ccc;
      {{/if}}">
      <a href="?m={{$m}}&amp;tab=vw_idx_permissions&amp;date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
        <strong>
	        {{$day|date_format:"%a"|upper|substr:0:1}}
	        {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $type_externe == "depart" && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
    </td>
    <td {{if $type_externe == "retour"  && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
	<tr>
		<td colspan="10" class="empty">Pas de permissions ce mois</td>
	</tr>
  {{/foreach}}
</table>