{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$line->_protocole}}
{{assign var=patient value=$line->_ref_prescription->_ref_object->_ref_patient}}

<table class="main">
   <tr>
     <th class="title">
     	{{if $can->admin}}
     	  {{mb_include module=system object=$line template=inc_object_history}}
			{{/if}}
			
       {{if $line->_ref_prescription->type == "sejour" && $line->_ref_prescription->_ref_object->_ref_curr_affectation->_id}}
			   <span style="float: right; font-size: 0.8em;">
           {{$line->_ref_prescription->_ref_object->_ref_curr_affectation->_ref_lit->_view}}
         </span>
       {{/if}}
			
       <span style="float: left">
         {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$patient size=32}}
       </span>
      
			 <h2 style="color: #fff; font-weight: bold; display: inline;">
         {{$patient->_view}}
       </h2>
      
			 {{if $line->_ref_prescription->type == "sejour"}}
         - {{$line->_ref_prescription->_ref_object->_shortview}}
         {{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
         {{assign var=antecedents value=$dossier_medical->_ref_antecedents}}
          {{assign var=sejour_id value=$prescription->object_id}}
         {{include file="../../dPprescription/templates/inc_vw_antecedent_allergie.tpl" nodebug=true}}    
       {{/if}}
    </th>
  </tr> 
  <tr>
    <td>
      <table class="main layout">
      {{mb_include module=dPprescription template=inc_infos_patients_soins}}
      </table>
      <hr />
    </td>
  </tr>
</table>  
{{/if}}