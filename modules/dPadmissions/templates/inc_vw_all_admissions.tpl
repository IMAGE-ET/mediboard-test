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
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  </tr>
		
  <tr>
    <th rowspan="2">Date</th>
    <th colspan="3">Admissions</th>
  </tr>

  <tr>
    <th class="text"><a class={{if $selAdmis=='0' && $selSaisis=='0'}}"selected"{{else}}"selectable"{{/if}} title="Toutes les admissions"     href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selSaisis=0">Toutes   </a></th>
    <th class="text"><a class={{if $selAdmis=='0' && $selSaisis=='n'}}"selected"{{else}}"selectable"{{/if}} title="Admissions non préparées"  href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selSaisis=n">Non prép.</a></th>
    <th class="text"><a class={{if $selAdmis=='n' && $selSaisis=='0'}}"selected"{{else}}"selectable"{{/if}} title="Admissions non effectuées" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=n&amp;selSaisis=0">Non eff. </a></th>
  </tr>

  {{foreach from=$list1 item=_list}}
  <tr {{if $_list.date == $date}}class="selected"{{/if}}>
    <td align="right">
      <a href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$_list.date|date_format:"%Y-%m-%d"}}">
        <strong>
	        {{$_list.date|date_format:"%A"|upper|substr:0:1}}
	        {{$_list.date|date_format:"%d"}}
        </strong>
      </a>
    </td>
    <td {{if $selAdmis=='0' && $selSaisis=='0' && $_list.date == $date}}style="font-weight: bold;"{{/if}}>
      {{$_list.num}}
    </td>
    <td {{if $selAdmis=='0' && $selSaisis=='n' && $_list.date == $date}}style="font-weight: bold;"{{/if}}>
      {{$_list.num3}}
    </td>
    <td {{if $selAdmis=='n' && $selSaisis=='0' && $_list.date == $date}}style="font-weight: bold;"{{/if}}>
      {{$_list.num2}}
    </td>
  </tr>
  {{foreachelse}}
	<tr>
		<td colspan="10"><em>Pas d'admission ce mois</em></td>
	</tr>
  {{/foreach}}
</table>