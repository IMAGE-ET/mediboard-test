{{* $Id: configure.tpl 8820 2010-05-03 13:18:20Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8820 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
<tr>
  <td class="text halfPane" colspan="2">
    <div id="cim-{{$sejour->_id}}">
    {{assign var="sejour" value=$sejour}}
    {{mb_include module=dPpmsi template=inc_diagnostic}}
    </div>
  </td>
  <td class="text halfPane" colspan="2">
    <div id="GHM-{{$sejour->_id}}">
      {{mb_include module=dPpmsi template=inc_vw_GHM}}
    </div>
  </td>
</tr>
<tr>
  <td colspan="4" id="hprim_export_sej{{$sejour->_id}}">
  </td>
</tr>
<tr>
  <th class="category" colspan="2">{{tr}}CDossierMedical-codes_cim{{/tr}}</th>
  <th class="category" colspan="2">{{tr}}CAntecedent.more{{/tr}}</th>
</tr>
<tr>
  <td class="text" colspan="2">
    <div id="cim-list-{{$sejour->_id}}">
      {{mb_include module=dPpmsi template=inc_list_diags}}
    </div>
  </td>
  <td class="text" colspan="2" {{if is_array($patient->_ref_dossier_medical->_ref_traitements)}}rowspan="3"{{/if}}>
    {{mb_include module=dPpmsi template=inc_list_antecedents}}
  </td>
</tr>

<tr>
  <th class="category" colspan="2">{{tr}}CTraitement.more{{/tr}}</th>
</tr>
{{if is_array($patient->_ref_dossier_medical->_ref_traitements)}}
  {{mb_include module=dPpmsi template=inc_list_traitements}}
{{/if}}
</table>