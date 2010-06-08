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
    <th colspan="10"class="title">Plages pour {{mb_value object=$user field="_user_last_name"}} {{mb_value object=$user field="_user_first_name"}}</th>
  </tr>
  <tr>
    <th>{{mb_title class=CPlageConge field=libelle}}</th>
    <th>{{tr}}Dates{{/tr}}</th>
    <th>{{mb_title class=CPlageConge field=replacer_id}}</th>
  </tr>
  {{foreach from=$plages_conge item=_plageconge}}
    <tr id="p{{$_plageconge->_id}}" {{if $plage_id == $_plageconge->_id}} class="selected" {{/if}}>
      <td>
        <a href="#Edit-{{$_plageconge->_guid}}"
           onclick="editPlageConge({{$_plageconge->_id}},{{$user->_id}})">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_plageconge->_guid}}')">
          {{mb_value object=$_plageconge field="libelle"}}
          </span>
        </a>
      </td>
      <td>
        {{mb_include module=system template=inc_interval_date from=$_plageconge->date_debut to=$_plageconge->date_fin}}
      </td>
			<td>
				{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_plageconge->_fwd.replacer_id}}
			</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3">{{tr}}CPlageConge.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>