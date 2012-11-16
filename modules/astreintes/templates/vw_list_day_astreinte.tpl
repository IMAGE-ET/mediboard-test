{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th colspan="2">{{tr}}CPlageAstreinte.For{{/tr}} {{$date|date_format:$conf.longdate}}</th>
  </tr>
  {{foreach from=$plages_astreinte item=_plage}}
  {{assign var=user value=$_plage->_ref_user}}
    <tr>
      <td style="background:{{if $_plage->_type == "admin"}}#AED0FF{{else}}#ffaeae{{/if}};">
        {{$user->_view}}
      </td>
      <td>
        {{if $_plage->_num_astreinte}}
          {{mb_value object=$_plage field=_num_astreinte}}
        {{else}}
          <div class="warning">{{tr}}CPlageAstreinte.noPhoneNumber{{/tr}}</div>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">{{tr}}CPlageAstreinte.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  {{mb_include module=astreintes template=inc_legend_planning_astreinte}}
</table>