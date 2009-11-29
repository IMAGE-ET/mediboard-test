{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{mb_colonne class=CRPU field="ccmu"        order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class=CRPU field="_patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class=CRPU field="_entree"     order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
    {{/if}}
    <th>{{mb_title class=CRPU field=_attente}} / {{mb_title class=CRPU field=_presence}}</th>
    {{if $medicalView}}
			{{if $dPconfig.dPurgences.diag_prat_view}}
	      <th>{{tr}}CRPU-diag_infirmier{{/tr}} / m�dical</th>
			{{else}}
			 <th>{{tr}}CRPU-diag_infirmier{{/tr}}</th>
			{{/if}}
    {{/if}}
    <th>Prise en charge</th>
  </tr>

  {{foreach from=$listSejours item=curr_sejour key=sejour_id}}
  {{assign var=rpu value=$curr_sejour->_ref_rpu}}
  {{assign var=rpu_id value=$rpu->_id}}
  {{assign var=patient value=$curr_sejour->_ref_patient}}
  {{assign var=consult value=$rpu->_ref_consult}}

  {{assign var=background value=none}}
  {{if $consult && $consult->_id}}{{assign var=background value=#ccf}}{{/if}}
  
  {{* Param to create/edit a RPU *}}
  {{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
  {{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}
  
  <tr>
  	{{if $curr_sejour->annule}}
    <td class="cancelled">
      {{tr}}Cancelled{{/tr}}
    </td>
	  {{else}}

    <td class="ccmu-{{$rpu->ccmu}}">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;{{$rpu_link_param}}">
        {{if $rpu->ccmu}}
				  {{mb_value object=$rpu field=ccmu}}
        {{/if}}
      </a>
      {{if $rpu->box_id}}

      {{assign var=rpu_box_id value=$rpu->box_id}}
      <strong>{{$boxes.$rpu_box_id->_view}}</strong>
      {{/if}}
    </td>
    {{/if}}

  	{{if $curr_sejour->annule}}
  	<td class="cancelled">
	  {{else}}
    <td class="text" style="background-color: {{$background}};">
    {{/if}}
      <a href="{{$rpu_link}}">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}};')">
          <big>{{$patient}}</big> 
        </strong>
      </a>
      
      {{if $patient->_IPP}}
      [{{$patient->_IPP}}]
      {{/if}}
      {{if $dPconfig.dPurgences.age_patient_rpu_view}}{{$patient->_age}} ans{{/if}}
      
    </td>

  	{{if $curr_sejour->annule}}
    <td class="cancelled" colspan=" {{if $dPconfig.dPurgences.responsable_rpu_view}}4{{else}}3{{/if}}">
      {{if $rpu->mutation_sejour_id}}
      Hospitalisation
      <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
        dossier [{{$rpu->_ref_sejour_mutation->_num_dossier}}]
     	</a> 
      {{else}}
      {{tr}}Cancelled{{/tr}}
      {{/if}}
    </td>
		<td class="cancelled">
      {{include file="inc_pec_praticien.tpl"}}
    </td>
	  {{else}}
    <td class="text" style="background-color: {{$background}};">
    	{{mb_include module=system template=inc_get_notes_image object=$curr_sejour mode=view float=right}}
    
      {{if $modules.dPhospi->canEdit()}}
      <a style="float: right" title="{{tr}}CSejour.modify{{/tr}}" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour_id}}">
        <img src="images/icons/planning.png" alt="Planifier"/>
      </a>
      {{/if}}

      <a href="{{$rpu_link}}">
        {{if $curr_sejour->_date_entree_prevue == $date}}
        {{$curr_sejour->_entree|date_format:$dPconfig.time}}
        {{else}}
        {{$curr_sejour->_entree|date_format:$dPconfig.datetime}}
        {{/if}}
        {{if $curr_sejour->_num_dossier}}
          [{{$curr_sejour->_num_dossier}}]
        {{/if}}
      </a>
      {{if ($rpu->radio_debut || $rpu->bio_depart)}}
        <div style="clear: both; font-weight: bold;" onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
        {{if $rpu->radio_debut}}
        	<div>{{tr}}CRPU-{{mb_ternary test=$rpu->radio_fin value=radio_fin other=radio_debut}}{{/tr}}</div>
        {{/if}}
        {{if $rpu->bio_depart}}
        	<div>{{tr}}CRPU-{{mb_ternary test=$rpu->bio_retour value=bio_retour other=bio_depart}}{{/tr}}</div>
        {{/if}}
      	</div>
      {{/if}}
      
    </td>
    
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
    <td class="text" style="background-color: {{$background}};">
      <a href="{{$rpu_link}}">
        {{$curr_sejour->_ref_praticien->_view}}
      </a>
    </td>
    {{/if}}

    {{if $rpu->_id}}
		  <td id="attente-{{$sejour_id}}" style="background-color: {{$background}}; text-align: center">
		    {{if $consult && $consult->_id}}
			    <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
			      Consultation � {{$consult->heure|date_format:$dPconfig.time}}
			      {{if $date != $consult->_ref_plageconsult->date}}
			      <br/>le {{$consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
			      {{/if}}
			    </a>
			    {{if !$curr_sejour->sortie_reelle}}
			      ({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})
			    {{else}}
			      (sortie � {{$curr_sejour->sortie_reelle|date_format:$dPconfig.time}})
			    {{/if}}
		
		    {{else}}
		      {{include file="inc_vw_attente.tpl" sejour=$curr_sejour}}
	      {{/if}}
	    </td>
    
	    {{if $medicalView}}
	    <td class="text" style="background-color: {{$background}};">
	      {{if $rpu->_ref_consult->motif && $dPconfig.dPurgences.diag_prat_view}}
	       {{$rpu->_ref_consult->motif|nl2br}}
	      {{else}}
	      <a href="{{$rpu_link}}">
	        {{$rpu->diag_infirmier|nl2br}}
	      </a>
	      {{/if}}
	    </td>
	    {{/if}}
	
	    <td class="button" style="background-color: {{$background}};">
			  {{include file="inc_pec_praticien.tpl"}}
	    </td>

		{{else}}
			<!-- Pas de RPU pour ce s�jour d'urgence -->
			<td colspan="5">
			  <div class="big-warning">
			  	Ce s�jour d'urgence n'est pas associ� � un RPU.
			  	<br />
			  	Merci de cr�er un RPU en <strong>cliquant sur le lien suivant</strong> :
			  	<ul>
			  		<li><a href="{{$rpu_link}}">Cr�ation d'un RPU pour ce s�jour</a></li>
			  	</ul>
			  </div>
			</td>
		{{/if}}
    {{/if}}
  </tr>
  
  
  {{/foreach}}
</table>