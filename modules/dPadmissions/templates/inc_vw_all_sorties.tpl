{{* $Id: inc_vw_all_admissions.tpl 10988 2010-12-30 15:00:26Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 10988 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="4">
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
		
  <tr>
    <th rowspan="2">Date</th>
  </tr>

  <tr>
    <th class="text">
      <a class={{if $type_sejour == 'ambu'}}"selected"{{else}}"selectable"{{/if}} title="ambu" href="?m={{$m}}&amp;tab=vw_idx_sortie&amp;type_sejour=ambu">
        Ambu
      </a>
    </th>
    <th class="text">
      <a class={{if $type_sejour == 'comp'}}"selected"{{else}}"selectable"{{/if}} title="comp" href="?m={{$m}}&amp;tab=vw_idx_sortie&amp;type_sejour=comp">
        Hospi
      </a>
    </th>
    <th class="text">
      <a class={{if $type_sejour == 'exte'}}"selected"{{else}}"selectable"{{/if}} title="exte" href="?m={{$m}}&amp;tab=vw_idx_sortie&amp;type_sejour=exte">
        Ext
      </a>
    </th>
  </tr>

  {{foreach from=$days key=day item=counts}}
  <tr {{if $day == $date}}class="selected"{{/if}}>
    {{assign var=day_number value=$day|date_format:"%w"}}
    <td align="right"
      {{if in_array($day, $bank_holidays)}}
        style="background-color: #fc0"
      {{elseif $day_number == '0' || $day_number == '6'}}
        style="background-color: #ccc;"
      {{/if}}>
      <a href="?m={{$m}}&amp;tab=vw_idx_sortie&amp;date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
        <strong>
	        {{$day|date_format:"%a"|upper|substr:0:1}}
	        {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $type_sejour == 'ambu' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.ambu}}{{$counts.ambu}}{{else}}-{{/if}}
    </td>
    <td {{if $type_sejour == 'comp' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.comp}}{{$counts.comp}}{{else}}-{{/if}}
    </td>
    <td {{if $type_sejour == 'exte' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.exte}}{{$counts.exte}}{{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
	<tr>
		<td colspan="10"><em>Pas d'admission ce mois</em></td>
	</tr>
  {{/foreach}}
</table>