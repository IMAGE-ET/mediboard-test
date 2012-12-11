{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPlanningOp
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=onchange value=""}}

<tr>
  <th>
    <label for="__cmu_pat" title="Patient bénéficiant du régime dde couverture maladie universelle">
      Patient sous CMU
    </label>
  </th>
  <td colspan="3">
    {{if $patient && $patient->_id}}
    <input name="__cmu_pat" onclick="setAldCmu(this.form);" type="checkbox" {{if $patient->cmu}}checked="checked"{{/if}}>
    <input name="_cmu_pat" value="{{$patient->cmu}}" type="hidden">
    {{else}}
    <input name="__cmu_pat" onclick="setAldCmu(this.form);" type="checkbox" disabled="disabled">
    <input name="_cmu_pat" value="0" type="hidden">
    {{/if}}
    <label for="__ald_pat" title="Patient bénéficiant du régime d'affection longue durée">
      Patient sous ALD
    </label>
    {{if $patient && $patient->_id}}
    <input name="__ald_pat" onclick="setAldCmu(this.form);" type="checkbox" {{if $patient->ald}}checked="checked"{{/if}}>
    <input name="_ald_pat" value="{{$patient->ald}}" type="hidden">
    {{else}}
    <input name="__ald_pat" onclick="setAld(this.form);" type="checkbox" disabled="disabled">
    <input name="_ald_pat" value="0" type="hidden">
    {{/if}}
    {{mb_label object=$sejour field=ald}}
    {{if $patient && $patient->ald}}
      {{mb_field object=$sejour typeEnum=checkbox field=ald onchange=$onchange|smarty:nodefaults}}
    {{else}}
      {{mb_field object=$sejour typeEnum=checkbox field=ald disabled=disabled onchange=$onchange|smarty:nodefaults}}
    {{/if}}
  </td>
</tr>