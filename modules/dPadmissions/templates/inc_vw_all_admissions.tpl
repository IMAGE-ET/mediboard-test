{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th colspan="4">
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  <tr>
    <th class="text">Date</th>
    <th class="text"><a class={{if $selAdmis=='0' && $selSaisis=='0'}}"selected"{{else}}"selectable"{{/if}} href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selSaisis=0">Toutes les admissions</a></th>
    <th class="text"><a class={{if $selAdmis=='0' && $selSaisis=='n'}}"selected"{{else}}"selectable"{{/if}} href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=0&amp;selSaisis=n">Dossiers non préparés</a></th>
    <th class="text"><a class={{if $selAdmis=='n' && $selSaisis=='0'}}"selected"{{else}}"selectable"{{/if}} href="?m={{$m}}&amp;tab=vw_idx_admission&amp;selAdmis=n&amp;selSaisis=0">Admissions non effectuées</a></th>
  </tr>
  {{foreach from=$list1 item=curr_list}}
  <tr {{if $curr_list.date == $date}}class="selected"{{/if}}>
    <td align="right">
      <a href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$curr_list.date|date_format:"%Y-%m-%d"}}">
      {{$curr_list.date|date_format:"%A %d"}}
      </a>
    </td>
    <td align="center">
      {{$curr_list.num}}
    </td>
    <td align="center">
      {{$curr_list.num3}}
    </td>
    <td align="center">
      {{$curr_list.num2}}
    </td>
  </tr>
  {{/foreach}}
</table>