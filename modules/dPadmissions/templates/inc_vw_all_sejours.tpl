{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPadmissions
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="4">
      <a style="display: inline;" href="?m={{$current_m}}&amp;tab=vw_sejours_validation&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline;" href="?m={{$current_m}}&amp;tab=vw_sejours_validation&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
    
  <tr>
    <th rowspan="2">Date</th>
  </tr>

  <tr>
    <th class="text">
      <a class={{if $recuse=='-1'}}"selected"{{else}}"selectable"{{/if}} title="Séjours en attente" href="?m={{$current_m}}&amp;tab=vw_sejours_validation&amp;recuse=-1">
        Att.
      </a>
    </th>
    <th class="text">
      <a class={{if $recuse=='0'}}"selected"{{else}}"selectable"{{/if}} title="Séjours validés" href="?m={{$current_m}}&amp;tab=vw_sejours_validation&amp;recuse=0">
        Val.
      </a>
    </th>
    <th class="text">
      <a class={{if $recuse=='1'}}"selected"{{else}}"selectable"{{/if}} title="Séjours récusés" href="?m={{$current_m}}&amp;tab=vw_sejours_validation&amp;recuse=1">
        Rec.
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
      <a href="?m={{$current_m}}&tab=vw_sejour_validation&date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
        <strong>
          {{$day|date_format:"%a"|upper|substr:0:1}}
          {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $recuse=='-1' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
    </td>
    <td {{if $recuse=='0' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
    </td>
    <td {{if $recuse=='1' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if $counts.num3}}{{$counts.num3}}{{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">Pas d'admission ce mois</td>
  </tr>
  {{/foreach}}
</table>