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
  {{if $board}}
    <tr>
      <th rowspan="2" class="title narrow">{{mb_title class=CLit field=chambre_id}}</th>
      <th colspan="2" rowspan="2" class="title">{{mb_title class=CPatient field=nom}}<br />({{mb_title class=CPatient field=nom_jeune_fille}})</th>
      <th rowspan="2" class="title">Labo</th>
      <th colspan="2" class="title">Alertes</th>
      <th rowspan="2" class="narrow title">{{mb_title class=CSejour field=entree}}</th>
      <th rowspan="2" class="title">{{mb_title class=CSejour field=libelle}}</th>
      <th rowspan="2" class="title">Prat.</th>
    </tr>
    <tr>
      <th class="title">Allergies</th>
      <th class="title"><label title="Antécédents">Atcd</label></th>
    </tr>
  {{else}}
  <tr>
    <th class="title">Prescriptions ({{$prescriptions|@count}})</th>
  </tr>
  {{/if}}
  {{foreach from=$prescriptions item=_prescription}}
    {{assign var=sejour value=$_prescription->_ref_object}}
    {{assign var=patient value=$_prescription->_ref_patient}}
  {{if $board}}
  <tr>
    {{mb_include module=soins template=inc_vw_sejour lite_view=true prescription=$_prescription service_id="" show_affectation=true}}
    </tr>
  {{else}}
  <tr>
    <td class="text">
     <a href="#{{$_prescription->_id}}" onclick="loadSejour('{{$_prescription->object_id}}'); Prescription.reloadPrescSejour('{{$_prescription->_id}}','','','','','',''); return false;">
        {{$_prescription->_ref_patient->_view}}
     </a> 
    </td>
  </tr>  
  {{/if}}
  
  {{/foreach}}
</table>