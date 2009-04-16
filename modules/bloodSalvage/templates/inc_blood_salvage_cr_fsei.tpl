{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table>
  <tr>
  {{if array_key_exists('dPqualite', $modules)}}
    <th>Incident</th>
    <td>
      <form name="fsei" action="?m={{$m}}" method="post">
        <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
        <input type="hidden" name="m" value="bloodSalvage" />
        <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
	      <select name="type_ei_id" onchange="submitFSEI(this.form);">
	        <option value="">&mdash; Aucun incident</option>
	        {{foreach from=$liste_incident key=id item=incident_type}}
	        <option value="{{$incident_type->_id}}" {{if $incident_type->_id == $blood_salvage->type_ei_id}}selected="selected"{{/if}}>{{$incident_type->_view}}</option>
	        {{/foreach}}
	      </select>
  	   </form>
    </td>
    <th>{{tr}}BloodSalvage.quality-protocole{{/tr}}</th>
    <td>
  {{else}}
    <th>{{tr}}BloodSalvage.quality-protocole{{/tr}}</th>
    <td colspan="4">
  {{/if}}    
      <form name="qualite" action="?m={{$m}}" method="post">
        <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
        <input type="hidden" name="m" value="bloodSalvage" />
        <input type="hidden" name="dosql" value="do_bloodSalvage_aed" />
        {{mb_field object=$blood_salvage field="sample" onchange="submitFormAjax(this.form,'systemMsg');"}}
      </form>
    </td>
  </tr>
  <tr>
    <td style="text-align:center;" colspan="4">
      <form name="rapport" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
        <input type="hidden" name="blood_salvage_id" value="{{$blood_salvage->_id}}" />
        <button class="print" type="button" onclick="printRapport()">{{tr}}CBloodSalvage.report{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>