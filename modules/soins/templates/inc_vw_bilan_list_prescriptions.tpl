{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $board}}
<script>
  updateNbPrescriptions({{$prescriptions|@count}});
</script>
{{/if}}

<table class="tbl">
  {{if !$board}}
  <tr>
    <th class="title">Prescriptions ({{$prescriptions|@count}})</th>
  </tr>
  {{/if}}
  {{foreach from=$prescriptions item=_prescription}}
  <tr>
    <td class="text">
      {{if $board}}
        <a href="#{{$_prescription->_id}}"
          onclick="showDossierSoins('{{$_prescription->object_id}}', '', 'prescription_sejour'); return false;">
      {{else}}
        <a href="#{{$_prescription->_id}}"
          onclick="loadSejour('{{$_prescription->object_id}}'); Prescription.reloadPrescSejour('{{$_prescription->_id}}','','','','','',''); return false;">
      {{/if}}
        {{$_prescription->_ref_patient->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>