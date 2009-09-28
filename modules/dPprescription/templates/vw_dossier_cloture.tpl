{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$sejour->_ref_patient}}
    
<table class="tbl">
	<tr>
	  <th class="title" colspan="2">
	    <div style="float: right">{{$dateTime|date_format:"%d/%m/%Y %Hh%M"}}</div>
	    <a href="#" onclick="window.print();">
	      Dossier cloturé
	    </a>
	  </th>
	</tr>
	<tr>
	  <th colspan="2">Patient</th>
	</tr>
	<tr>
	  <td>
	    <strong>Patient</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=_view}}
	  </td>
	</tr>
	<tr>
	  <td style="width: 20%">
	    <strong>{{mb_title object=$patient->_ref_constantes_medicales field=poids}}</strong>
	  </td>
	  <td colspan="2">
	   {{if $patient->_ref_constantes_medicales->poids}}
	     {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
	   {{else}}??{{/if}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$patient field=adresse}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=adresse}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$patient field=naissance}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=naissance}} ({{$patient->_age}} ans)
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$patient field=tel}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$patient field=tel}}
	  </td>
	</tr>
	<tr>
	  <th colspan="2">Séjour</th>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=_entree}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=_entree}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=_sortie}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=_sortie}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=praticien_id}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=praticien_id}}
	  </td>
	</tr>
	<tr>
	  <td>
	    <strong>{{mb_label object=$sejour field=type}}</strong>
	  </td>
	  <td>
	    {{mb_value object=$sejour field=type}}
	  </td>
	</tr>
</table>

<table class="tbl">
<tr>
  <th>Libelle</th>
  <th>Quantite: administration</th>
</tr>
{{foreach from=$dossier key=date item=lines_by_cat}}
  <tr>
    <th colspan="2">{{$date|date_format:"%d/%m/%Y"}}</th>
  </tr>
  {{foreach from=$lines_by_cat key=chap item=lines}}
  <tr>
    <td colspan="2">
      <strong>
        {{if $chap == "medicament"}}
          Médicament
        {{elseif $chap == "perfusion"}}
          Perfusions
        {{else}}
          {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
        {{/if}}
      </strong>
    </td>
  </tr>
  {{foreach from=$lines key=line_id item=administrations}}
	  {{assign var=line value=$list_lines.$chap.$line_id}}     
     <tr>
       <td>
         {{if $line->_class_name == "CPrescriptionLineMedicament"}}
           {{$line->_ucd_view}} 
           <span style="opacity: 0.7; font-size: 0.8em;">
           {{if $line->_forme_galenique}}({{$line->_forme_galenique}}){{/if}}
           </span>
         {{elseif $line->_class_name == "CPerfusionLine"}}
           {{$line->_ucd_view}}           
           {{if $line->_class_name == "CPerfusionLine"}}
             (Perfusion: {{$line->_ref_perfusion->_view}})
           {{/if}}
         {{else}}
           {{$line->_view}}
         {{/if}}
       </td>
       <td>  
         {{foreach from=$administrations key=quantite item=_administrations_by_quantite}}
           {{$quantite}} 
           {{if $line->_class_name == "CPrescriptionLineMedicament"}}
					   {{if $line->_ref_produit_prescription->_id}}
						   {{$line->_ref_produit_prescription->unite_prise}}
						 {{else}}
               {{$line->_ref_produit->libelle_unite_presentation}}
						 {{/if}}
           {{elseif $line->_class_name != "CPerfusionLine"}}
             {{$line->_unite_prise}}
           {{else}}
             {{$line->_unite_administration}}
           {{/if}}: 
           {{foreach from=$_administrations_by_quantite item=_administration}}
           {{$_administration->dateTime|date_format:$dPconfig.time}}
           {{/foreach}}
           <br />
         {{/foreach}}
       </td>  
       </tr>
    {{/foreach}}
  {{/foreach}}
{{/foreach}}
</table>
<table class="tbl">
  <tr>
    <th colspan="6">Observations et Transmissions</th>
  </tr>
  <tr>
    <th>{{mb_label class=CTransmissionMedicale field="type"}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="user_id"}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="date"}}</th>
    <th>{{tr}}Hour{{/tr}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="object_id"}}</th>
    <th>{{mb_label class=CTransmissionMedicale field="text"}}</th>
  </tr>
  {{foreach from=$sejour->_ref_suivi_medical item=_suivi}}
 	  {{mb_include module=dPhospi template=inc_line_suivi _suivi=$_suivi show_patient=false nodebug=true without_del_form=true}}
  {{/foreach}}
</table>