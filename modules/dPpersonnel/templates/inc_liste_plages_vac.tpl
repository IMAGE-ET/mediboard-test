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
		<th colspan="2"class="title">Plages pour {{mb_value object=$user field="_user_last_name"}} {{mb_value object=$user field="_user_first_name"}}</th>
	</tr>
	{{foreach from=$plages_vac item=_plagevac}}
	<tr>
    <td>
    	<a href="#" onclick="editPlageVac({{$_plagevac->_id}},{{$user_id}})">
    		<span onmouseover="ObjectTooltip.createEx(this, '{{$_plagevac->_guid}}')">
    		{{mb_value object=$_plagevac field="libelle"}} : 
				</span>
      </a>
    </td>
		<td>
			{{assign var=date_deb value=$_plagevac->date_debut}}
      {{assign var=date_f value=$_plagevac->date_fin}}
		  {{mb_include module=system template=inc_interval_date from=$date_deb to=$date_f}}
		</td>
  </tr>
	{{foreachelse}}
	<tr>
		<td>{{tr}}CPlageVacances.none{{/tr}}</td>
	</tr>
	{{/foreach}}
	<tr>
		<td colspan="2">
			<a class="button new" href="#"
			onClick="editPlageVac('',{{$user_id}})">
        {{tr}}Create{{/tr}} {{tr}}CPlageVacances.one{{/tr}}
      </a>
		</td>
	</tr>
</table>