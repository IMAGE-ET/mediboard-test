{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" style="text-align: center;">
  <tr>
    <th class="title" colspan="4">
      <a style="display: inline" href="#1" onclick="$V(getForm('selType').date, '{{$lastmonth}}'); reloadFullAdmissions()">&lt;&lt;&lt;</a>
      {{$date|date_format:"%b %Y"}}
      <a style="display: inline" href="#1" onclick="$V(getForm('selType').date, '{{$nextmonth}}'); reloadFullAdmissions()">&gt;&gt;&gt;</a>
    </th>
  </tr>
    
  <tr>
    <th rowspan="2">Date</th>
  </tr>

  <tr>
    <th class="text">
      <a class="{{if $selAdmis=='0' && $selSaisis=='0'}}selected{{else}}selectable{{/if}}" title="Toutes les admissions"
        href="#1" onclick="filterAdm(0, 0)">
        Adm.
      </a>
    </th>
    <th class="text">
      <a class="{{if $selAdmis=='0' && $selSaisis=='n'}}selected{{else}}selectable{{/if}}" title="Admissions non préparées"
         href="#1" onclick="filterAdm(0, 'n')">
        Non prép.
      </a>
    </th>
    <th class="text">
      <a class="{{if $selAdmis=='n' && $selSaisis=='0'}}selected{{else}}selectable{{/if}}" title="Admissions non effectuées"
         href="#1" onclick="filterAdm('n', 0)">
        Non eff.
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
      <a href="#1" onclick="reloadAdmissionDate(this, '{{$day|iso_date}}');" title="{{$day|date_format:$conf.longdate}}">
        <strong>
          {{$day|date_format:"%a"|upper|substr:0:1}}
          {{$day|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $selAdmis=='0' && $selSaisis=='0' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if isset($counts.num1|smarty:nodefaults) && $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
    </td>
    <td {{if $selAdmis=='0' && $selSaisis=='n' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if isset($counts.num3|smarty:nodefaults) && $counts.num3}}{{$counts.num3}}{{else}}-{{/if}}
    </td>
    <td {{if $selAdmis=='n' && $selSaisis=='0' && $day == $date}}style="font-weight: bold;"{{/if}}>
      {{if isset($counts.num2|smarty:nodefaults) && $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10" class="empty">Pas d'admission ce mois</td>
  </tr>
  {{/foreach}}

  <tr>
    <td><strong>Total</strong></td>
    <td><strong>{{$totaux.num1|smarty:nodefaults}}</strong></td>
    <td><strong>{{$totaux.num3|smarty:nodefaults}}</strong></td>
    <td><strong>{{$totaux.num2|smarty:nodefaults}}</strong></td>
  </tr>
</table>