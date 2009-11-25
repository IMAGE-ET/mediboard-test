{{* $Id: inc_vw_all_admissions.tpl 6147 2009-04-21 14:41:09Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 6147 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th colspan="4">
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_preadmission&amp;date={{$lastmonth}}">&lt;&lt;&lt;</a>
      {{$date|date_format:"%B %Y"}}
      <a style="display: inline;" href="?m={{$m}}&amp;tab=vw_idx_preadmission&amp;date={{$nextmonth}}">&gt;&gt;&gt;</a>
    </th>
  <tr>
    <th class="text">Date</th>
    <th class="text">Pré-admissions</th>
  </tr>
  {{foreach from=$listMonth item=curr_day}}
  <tr {{if $curr_day.date == $date}}class="selected"{{/if}}>
    <td align="right">
      <a href="?m={{$m}}&amp;tab=vw_idx_preadmission&amp;date={{$curr_day.date|date_format:"%Y-%m-%d"}}">
      {{$curr_day.date|date_format:"%A %d"}}
      </a>
    </td>
    <td align="center">
      {{$curr_day.total}}
    </td>
  </tr>
  {{/foreach}}
</table>