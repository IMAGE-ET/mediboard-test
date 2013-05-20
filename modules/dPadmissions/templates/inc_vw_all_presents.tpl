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
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_present&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_present&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
		
  <tr>
    <th>Date</th>
    <th>Présents</th>
  </tr>

  {{foreach from=$days key=day item=counts}}
  <tr {{if $day == $date}}class="selected"{{/if}}>
    {{assign var=day_number value=$day|date_format:"%w"}}
    <td align="right"
      {{if array_key_exists($day, $bank_holidays)}}
        style="background-color: #fc0"
      {{elseif $day_number == '0' || $day_number == '6'}}
        style="background-color: #ccc;"
      {{/if}}>
      <a href="?m={{$m}}&amp;tab=vw_idx_present&amp;date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
        <strong>
	        {{$day|date_format:"%a"|upper|substr:0:1}}
	        {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td>{{if $counts}}{{$counts}}{{else}}-{{/if}}</td>
  </tr>
  {{foreachelse}}
	<tr>
		<td colspan="10" class="empty">Pas d'admission ce mois</td>
	</tr>
  {{/foreach}}
</table>