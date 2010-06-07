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
    <th>{{mb_title class=CPlageVacances field=libelle}}</th>
    <th>{{tr}}Dates{{/tr}}</th>
    <th>{{mb_title class=CPlageVacances field=replacer_id}}</th>
  </tr>
  {{foreach from=$plages_vac item=_plagevac}}
    <tr id="p{{$_plagevac->_id}}" {{if $plage_id == $_plagevac->_id}} class="selected" {{/if}}>
      <td>
        <a href="#Edit-{{$_plagevac->_guid}}"
           onclick="editPlageVac({{$_plagevac->_id}},{{$user->_id}})">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_plagevac->_guid}}')">
          {{mb_value object=$_plagevac field="libelle"}}
          </span>
        </a>
      </td>
      <td>
        {{mb_include module=system template=inc_interval_date from=$_plagevac->date_debut to=$_plagevac->date_fin}}
      </td>
			<td>
				{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_plagevac->_fwd.replacer_id}}
			</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3">{{tr}}CPlageVacances.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>