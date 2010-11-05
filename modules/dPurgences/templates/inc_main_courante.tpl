{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="show_statut" value=$dPconfig.dPurgences.show_statut}}

<script type="text/javascript">
Main.add(function() {
  Veille.refresh();
  Missing.refresh();
  $$("a[href=#holder_main_courante] small")[0].update("({{$listSejours|@count}})");
	{{if $isImedsInstalled}}
    ImedsResultsWatcher.loadResults();
  {{/if}}
});
</script>

<table class="tbl">
  <tr>
    <th style="width: 8em;">
		  {{mb_colonne class=CRPU field="ccmu"        order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}
		</th>
    <th style="width: 16em;">
    	{{mb_colonne class=CRPU field="_patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}
		</th>
		<th class="narrow">
      <input type="text" size="6" onkeyup="MainCourante.filter(this)" id="filter-patient-name" />
		</th>
    <th style="width: 10em;">
		  {{mb_colonne class=CRPU field="_entree"     order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}
		</th>
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
    <th class="narrow">{{mb_title class=CRPU field="_responsable_id"}}</th>
    {{/if}}
    <th style="width: 10em;">{{mb_title class=CRPU field=_attente}} / {{mb_title class=CRPU field=_presence}}</th>
    {{if $medicalView}}
			<th style="width: 16em;">
			{{if $dPconfig.dPurgences.diag_prat_view}}
	      {{tr}}CRPU-diag_infirmier{{/tr}} / {{tr}}Medical{{/tr}}
			{{else}}
			  {{tr}}CRPU-diag_infirmier{{/tr}}
			{{/if}}
		  </th>
    {{/if}}
    <th style="width: 0em;">{{tr}}CRPU.pec{{/tr}}</th>
  </tr>

  {{foreach from=$listSejours item=_sejour key=sejour_id}}
  {{assign var=rpu value=$_sejour->_ref_rpu}}
  {{assign var=rpu_id value=$rpu->_id}}
  {{assign var=patient value=$_sejour->_ref_patient}}
  {{assign var=consult value=$rpu->_ref_consult}}

  {{assign var=background value=none}}
  {{if $consult && $consult->_id}}{{assign var=background value="#ccf"}}{{/if}}
  
  {{* Param to create/edit a RPU *}}
  {{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
  {{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}
  
  <tr class="
	 {{if !$_sejour->sortie_reelle && $_sejour->_veille}}veille{{/if}}
   {{if !$rpu_id}}missing{{/if}}
  ">
  	{{if $_sejour->annule}}
    <td class="cancelled">
      {{tr}}Cancelled{{/tr}}
    </td>
	  {{else}}

    <td class="ccmu-{{$rpu->ccmu}} text" {{if $_sejour->sortie_reelle || $rpu->mutation_sejour_id}}style="border-right: 5px solid black"{{/if}}>
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

  	{{if $_sejour->annule}}
  	<td colspan="2" class="text cancelled">
	  {{else}}
    <td colspan="2" class="text" style="background-color: {{$background}};">
    {{/if}}
      {{mb_include template=inc_rpu_patient}}
    </td>

  	{{if $_sejour->annule}}
    <td class="cancelled" colspan="{{if $dPconfig.dPurgences.responsable_rpu_view}}4{{else}}3{{/if}}">
      {{tr}}Cancelled{{/tr}}
    </td>
		<td class="cancelled">
      {{include file="inc_pec_praticien.tpl"}}
    </td>

	  {{else}}

    <td class="text" style="background-color: {{$background}}; text-align: center;">
			{{mb_include module=system template=inc_get_notes_image object=$_sejour mode=view float=right}}
      
			{{if $isImedsInstalled}}
			  {{mb_include module=dPImeds template=inc_sejour_labo sejour=$_sejour link="$rpu_link#Imeds"}}
      {{/if}}

      <a href="{{$rpu_link}}">
      	<span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
          {{mb_value object=$_sejour field=_entree date=$date}}
         {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$_sejour->_num_dossier}}
        </span>
      </a>
								
      {{if $show_statut == 1}}
        <div style="clear: both; font-weight: bold; padding-top: 3px;">
           
 
        {{if $rpu->radio_debut}}
          <img src="modules/soins/images/radio{{if !$rpu->radio_fin}}_grey{{/if}}.png"
            {{if !$rpu->radio_fin}}
              title="{{tr}}CRPU-radio_debut{{/tr}} à {{$rpu->radio_debut|date_format:$dPconfig.time}}"
            {{else}}
              title="{{tr}}CRPU-radio_fin{{/tr}} à {{$rpu->radio_fin|date_format:$dPconfig.time}}"
            {{/if}}/>
        {{elseif !$rpu->radio_fin}}
          <img src="images/icons/placeholder.png"/>
        {{/if}}
        {{if $rpu->bio_depart}}
          <img src="images/icons/labo{{if !$rpu->bio_retour}}_grey{{/if}}.png"
            {{if !$rpu->bio_retour}}
              title="{{tr}}CRPU-bio_depart{{/tr}} à {{$rpu->bio_depart|date_format:$dPconfig.time}}"
            {{else}}
              title="{{tr}}CRPU-bio_retour{{/tr}} à {{$rpu->bio_retour|date_format:$dPconfig.time}}"
            {{/if}}/>
        {{elseif !$rpu->bio_retour}}
          <img src="images/icons/placeholder.png"/>
        {{/if}}
        {{if $rpu->specia_att}}
          <img src="modules/soins/images/stethoscope{{if !$rpu->specia_arr}}_grey{{/if}}.png"
            {{if !$rpu->specia_arr}}
              title="{{tr}}CRPU-specia_att{{/tr}} à {{$rpu->specia_att|date_format:$dPconfig.time}}"
            {{else}}
              title="{{tr}}CRPU-specia_arr{{/tr}} à {{$rpu->specia_arr|date_format:$dPconfig.time}}"
            {{/if}}/>
        {{elseif !$rpu->specia_arr}}
          <img src="images/icons/placeholder.png"/>
        {{/if}}
        {{if $_sejour->_nb_files_docs > 0}}
          <img src="images/icons/docitem.png" title="{{$_sejour->_nb_files|default:0}} {{tr}}CMbObject-back-files{{/tr}} / {{$_sejour->_nb_docs|default:0}} {{tr}}CMbObject-back-documents{{/tr}}"/>
        {{else}}
          <img src="images/icons/placeholder.png"/>
        {{/if}}
				{{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
        {{if $prescription->_id}}
				  {{if $prescription->_count_recent_modif_presc}}
            <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
	        {{else}}
	          <img src="images/icons/ampoule_grey.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
	        {{/if}}
				{{else}}
          <img src="images/icons/placeholder.png"/>
			  {{/if}}
      	</div>
      {{/if}}
    </td>
    
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
    <td class="text" style="background-color: {{$background}};">
      <a href="{{$rpu_link}}">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </a>
    </td>
    {{/if}}

    {{if $rpu->_id}}
      {{if $rpu->mutation_sejour_id}}
			  {{mb_include template=inc_dossier_mutation colspan=1}}
      {{else}} 
  		  <td style="background-color: {{$background}}; text-align: center">
  		    {{if $consult && $consult->_id}}
    		    {{if !$_sejour->sortie_reelle && $show_statut}}
              {{mb_include template=inc_icone_attente}}
            {{/if}}
  			    <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
  			      Consult. {{$consult->heure|date_format:$dPconfig.time}}
  			      {{if $date != $consult->_ref_plageconsult->date}}
  			      <br/>le {{$consult->_ref_plageconsult->date|date_format:$dPconfig.date}}
  			      {{/if}}
  			    </a>
  			    {{if !$_sejour->sortie_reelle}}
  			      ({{mb_value object=$rpu field=_attente}} / {{mb_value object=$rpu field=_presence}})
  			    {{elseif $_sejour->sortie_reelle}}
  			      (sortie à {{$_sejour->sortie_reelle|date_format:$dPconfig.time}})
  			    {{/if}}
  		    {{else}}
  		      {{include file="inc_attente.tpl" sejour=$_sejour}}
  	      {{/if}}
  	    </td>
      {{/if}} 
    
	    {{if $medicalView}}
  	    <td class="text" style="background-color: {{$background}};">
  				{{if $rpu->motif && $dPconfig.dPurgences.diag_prat_view}}
  				  <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
  				  	<strong>{{mb_title class=$rpu field=motif}}</strong> : {{$rpu->motif|nl2br}}
  				  </span>
  	      {{else}}
  				  <span onmouseover="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
              {{$rpu->diag_infirmier|nl2br}}
            </span>
  	      {{/if}}
  	    </td>
	    {{/if}}
	
	    <td class="button {{if $_sejour->type != "urg"}}arretee{{/if}}" style="background-color: {{$background}};">
			  {{include file="inc_pec_praticien.tpl"}}
	    </td>

		{{else}}
			<!-- Pas de RPU pour ce séjour d'urgence -->
			<td colspan="{{$medicalView|ternary:3:2}}">
			  <div class="small-warning">
			  	{{tr}}CRPU.no_assoc{{/tr}}
			  	<br />
			  	{{tr}}CRPU.no_assoc_clic{{/tr}}
			  	<a class="button action new" href="{{$rpu_link}}">{{tr}}CRPU-title-create{{/tr}}</a>
			  </div>
			</td>
		{{/if}}
    {{/if}}
  </tr>
  
  {{foreachelse}}
  <tr><td colspan="10"><em>{{tr}}CSejour.none_main_courante{{/tr}}</em></td></tr>
  {{/foreach}}
</table>