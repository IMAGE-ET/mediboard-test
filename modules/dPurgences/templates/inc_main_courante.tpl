{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function(){
    var count = {{$sejours_veille}};
    var counter = $$("button .count")[0];
    counter.update(count);
    counter.up().setVisible(count != 0);

    $$("a[href=#holder_main_courante] small")[0].update("({{$listSejours|@count}})");
    
    $V($("filter-patient-name"), "");
  });
</script>

<table class="tbl">
  <tr>
    <th>{{mb_colonne class=CRPU field="ccmu"        order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class=CRPU field="_patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class=CRPU field="_entree"     order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
    <th>{{mb_title class=CRPU field="_responsable_id"}}</th>
    {{/if}}
    <th>{{mb_title class=CRPU field=_attente}} / {{mb_title class=CRPU field=_presence}}</th>
    {{if $medicalView}}
			{{if $dPconfig.dPurgences.diag_prat_view}}
	      <th>{{tr}}CRPU-diag_infirmier{{/tr}} / médical</th>
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
  {{if $consult && $consult->_id}}{{assign var=background value="#ccf"}}{{/if}}
  
  {{* Param to create/edit a RPU *}}
  {{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
  {{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}
  
  <tr {{if !$curr_sejour->sortie_reelle && $curr_sejour->_veille}}style="display: none;" class="veille"{{/if}}>
  	{{if $curr_sejour->annule}}
    <td class="cancelled">
      {{tr}}Cancelled{{/tr}}
    </td>
	  {{else}}

    <td class="ccmu-{{$rpu->ccmu}}">
      <a href="{{$rpu_link}}">
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
      {{mb_include template=inc_rpu_patient}}
    </td>

  	{{if $curr_sejour->annule}}
    <td class="cancelled" colspan=" {{if $dPconfig.dPurgences.responsable_rpu_view}}4{{else}}3{{/if}}">
      {{if $rpu->mutation_sejour_id}}
      Hospitalisation
      <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
        dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$rpu->_ref_sejour_mutation->_num_dossier}}
     	</a> 
      {{else}}
      {{tr}}Cancelled{{/tr}}
      {{/if}}
    </td>
		<td class="cancelled">
      {{include file="inc_pec_praticien.tpl"}}
    </td>
		
	  {{else}}
    <td class="text" style="background-color: {{$background}};" onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
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
        {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$curr_sejour->_num_dossier}}
      </a>
			
      {{if ($rpu->radio_debut || $rpu->bio_depart || $rpu->specia_att)}}
        <div style="clear: both; font-weight: bold;">
        {{if $rpu->radio_debut}}
        	<div {{if $rpu->radio_fin}}style="opacity: 0.6"{{/if}}>
        		{{tr}}CRPU-{{mb_ternary test=$rpu->radio_fin value=radio_fin other=radio_debut}}{{/tr}}
					</div>
        {{/if}}
        {{if $rpu->bio_depart}}
        	<div {{if $rpu->bio_retour}}style="opacity: 0.6"{{/if}}>
        		{{tr}}CRPU-{{mb_ternary test=$rpu->bio_retour value=bio_retour other=bio_depart}}{{/tr}}
					</div>
        {{/if}}
        {{if $rpu->specia_att}}
          <div {{if $rpu->specia_arr}}style="opacity: 0.6"{{/if}}>
          	{{tr}}CRPU-{{mb_ternary test=$rpu->specia_arr value=specia_arr other=specia_att}}{{/tr}}
					</div>
        {{/if}}
      	</div>
      {{/if}}
      
    </td>
    
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
    <td class="text" style="background-color: {{$background}};">
      <a href="{{$rpu_link}}">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_sejour->_ref_praticien}}
      </a>
    </td>
    {{/if}}

    {{if $rpu->_id}}
		  <td id="attente-{{$sejour_id}}" style="background-color: {{$background}}; text-align: center">
		    {{if $consult && $consult->_id}}
			    <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
			      Consultation à {{$consult->heure|date_format:$dPconfig.time}}
			      {{if $date != $consult->_ref_plageconsult->date}}
			      <br/>le {{$consult->_ref_plageconsult->date|date_format:$dPconfig.date}}
			      {{/if}}
			    </a>
			    {{if !$curr_sejour->sortie_reelle}}
			      ({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})
			    {{else}}
			      (sortie à {{$curr_sejour->sortie_reelle|date_format:$dPconfig.time}})
			    {{/if}}
		
		    {{else}}
		      {{include file="inc_attente.tpl" sejour=$curr_sejour}}
	      {{/if}}
	    </td>
    
	    {{if $medicalView}}
	    <td class="text" style="background-color: {{$background}};">
				{{if $rpu->motif && $dPconfig.dPurgences.diag_prat_view}}
				<strong>{{mb_title class=$rpu field=motif}}</strong> :
	       {{$rpu->motif|nl2br}}
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
			<!-- Pas de RPU pour ce séjour d'urgence -->
			<td colspan="5">
			  <div class="small-warning">
			  	Ce séjour d'urgence n'est pas associé à un RPU.
			  	<br />
			  	Merci de <strong>cliquer sur le lien suivant</strong> :
			  	<a class="button action new" href="{{$rpu_link}}">{{tr}}CRPU-title-create{{/tr}}</a>
			  </div>
			</td>
		{{/if}}
    {{/if}}
  </tr>
  
  {{foreachelse}}
  <tr><td colspan="10"><em>Aucun séjour dans la main courante</em></td></tr>
  {{/foreach}}
</table>