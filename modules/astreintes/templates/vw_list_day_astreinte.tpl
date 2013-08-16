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
      <td style="background:#{{$_plage->_color}};">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_plage->_ref_user}}
      </td>
      <td>
        <img src="style/mediboard/images/buttons/phone.png" alt=""/> {{mb_value object=$_plage field=phone_astreinte}} {{if $_plage->_ref_user->_user_astreinte}}({{mb_value object=$_plage->_ref_user field=_user_astreinte}}){{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty" style="height: 40px;">{{tr}}CPlageAstreinte.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  {{mb_include module=astreintes template=inc_legend_planning_astreinte}}
</table>