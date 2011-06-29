{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=transmissions value=$prescription->_transmissions}}
{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
	
<td {{if $conf.dPprescription.CPrescription.show_categories_plan_soins}}colspan="2"{{/if}} class="text">
  {{if $move_dossier_soin}}
	  <script type="text/javascript">
	    Main.add(function () {
	      $("line_{{$_prescription_line_mix->_guid}}").show();
	    });
	  </script>
  {{/if}}
  
  {{if $_prescription_line_mix->_recent_modification}}
    {{if @$conf.object_handlers.CPrescriptionAlerteHandler && $_prescription_line_mix->_ref_alerte->_id}}
        <div id="alert_manuelle_{{$_prescription_line_mix->_ref_alerte->_id}}">
          <img style="float: right" src="images/icons/ampoule.png" onclick="alerte_prescription = ObjectTooltip.createDOM(this, 'editAlerte-{{$_prescription_line_mix->_ref_alerte->_id}}', { duration: 0}); "/>
           
          <div id="editAlerte-{{$_prescription_line_mix->_ref_alerte->_id}}" style="display: none;">
            <table class="form">
              <tr>
                <th class="category">Alerte</th>
              </tr> 
              <tr>
                <td class="text" style="width: 300px;">
                  {{mb_value object=$_prescription_line_mix->_ref_alerte field=comments}}
                </td>
              </tr>
              <tr>
                <td class="button">
                  <form name="modifyAlert-{{$_prescription_line_mix->_ref_alerte->_id}}" action="?" method="post" class="form-alerte"
                        onsubmit="return onSubmitFormAjax(this, { 
                                    onComplete: function() { $('alert_manuelle_{{$_prescription_line_mix->_ref_alerte->_id}}').hide(); if(alerte_prescription) { alerte_prescription.hide(); } } });">
                    <input type="hidden" name="m" value="system" />
                    <input type="hidden" name="dosql" value="do_alert_aed" />
                    <input type="hidden" name="del" value="" />
                    <input type="hidden" name="alert_id" value="{{$_prescription_line_mix->_ref_alerte->_id}}" />
                    <input type="hidden" name="handled" value="1" />
                    <button type="submit" class="tick">
                      Traiter
                    </button>
                  </form>
                </td>
              </tr>
            </table>
          </div>
        </div>
      {{else}}
        <img style="float: right" src="images/icons/ampoule.png" title="Ligne récemment modifiée"/>
      {{/if}}  
		{{/if}}
	
	{{if $_prescription_line_mix->commentaire}}
    <img style="float: right; margin: 2px;" src="images/icons/postit.png" title="" onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-comment-{{$_prescription_line_mix->_guid}}');" />
   
    <table class="tbl" id="tooltip-content-comment-{{$_prescription_line_mix->_guid}}" style="display: none;">
      <tr>
        <th>Commentaire - {{$_prescription_line_mix->_view}}</th>
      </tr> 
      <tr>
        <td class="text" style="width: 300px;">
          {{$_prescription_line_mix->commentaire}}
        </td>
      </tr>
    </table>
  {{/if}}

  <div style="cursor: pointer; padding: 2px; font-weight: bold;" class="{{if @$transmissions.CPrescriptionLineMix.$prescription_line_mix_id|@count}}transmission{{else}}transmission_possible{{/if}}"
     onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_guid}}')" 
	 onclick="editPerf('{{$_prescription_line_mix->_id}}','{{$date}}',document.mode_dossier_soin.mode_dossier.value, '{{$sejour->_id}}');">
        {{tr}}CPrescriptionLineMix.type.{{$_prescription_line_mix->type}}{{/tr}} 
		{{if $_prescription_line_mix->voie}}
		  <div style="white-space: nowrap;">[{{$_prescription_line_mix->voie}}]</div>
		{{/if}}
		{{if $_prescription_line_mix->interface}}
          <div style="white-space: nowrap;">[{{tr}}CPrescriptionLineMix.interface.{{$_prescription_line_mix->interface}}{{/tr}}]</div>
    {{/if}}
  </div>
	
	{{if $_prescription_line_mix->_debit && $_prescription_line_mix->type_line != "oxygene"}}
		<form style="white-space: nowrap" name="modifDebit-{{$prescription_line_mix_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, { onComplete: function() { 
                            PlanSoins.loadTraitement('{{$_prescription_line_mix->_ref_prescription->object_id}}','{{$date}}','','administration');} } )">
		  <input type="hidden" name="m" value="dPprescription" />
			<input type="hidden" name="dosql" value="do_prescription_line_mix_variation_aed" />
      <input type="hidden" name="prescription_line_mix_variation_id" value="" />
      <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
			<input type="hidden" name="dateTime" value="current" />
      Débit {{mb_field object=$_prescription_line_mix->_last_variation field="debit" form="modifDebit-$prescription_line_mix_id" increment=1 min=0 size=2}} ml/h
			<button type="submit" class="submit notext" ></button>
		</form>
	{{/if}}
	
  {{if $_prescription_line_mix->_active && $_prescription_line_mix->type_line == "perfusion"}}
	  {{if $_prescription_line_mix->signature_prat || !$conf.dPprescription.CPrescription.show_unsigned_med_msg}}
      <div style="text-align: center;">
			<form name="editPerfusion-{{$_prescription_line_mix->_id}}" method="post" action="?" style="text-align: center;">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
        <input type="hidden" name="date_pose" value="{{$_prescription_line_mix->date_pose}}" />
        <input type="hidden" name="time_pose" value="{{$_prescription_line_mix->time_pose}}" />
        <input type="hidden" name="date_retrait" value="{{$_prescription_line_mix->date_retrait}}" />
        <input type="hidden" name="time_retrait" value="{{$_prescription_line_mix->time_retrait}}" />

        {{if !$_prescription_line_mix->date_pose}}<a href="#1" style="display: inline; border: 0px;" onclick="submitPosePerf(document.forms['editPerfusion-{{$_prescription_line_mix->_id}}']);">{{/if}}
         <img src="images/icons/play.png" title="Pose de la perfusion" style="{{if $_prescription_line_mix->date_pose}}opacity: 0.5{{/if}}" />
        {{if !$_prescription_line_mix->date_pose}}</a>{{/if}}

        {{if !$_prescription_line_mix->date_retrait}}<a href="#1" style="display: inline; border: 0px;" onclick="submitRetraitPerf(document.forms['editPerfusion-{{$_prescription_line_mix->_id}}']);">{{/if}}
         <img src="images/icons/stop.png" title="Retrait de la perfusion" style="{{if $_prescription_line_mix->date_retrait}}opacity: 0.5{{/if}}" />
        {{if !$_prescription_line_mix->date_retrait}}</a>{{/if}}
      </form>
			</div>
		{{/if}}
  {{/if}}

	{{if $_prescription_line_mix->conditionnel}}
    <form action="?" method="post" name="activeCondition-{{$_prescription_line_mix->_id}}-{{$_prescription_line_mix->_class_name}}">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
      <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
      <input type="hidden" name="del" value="0" />
      
      {{if !$_prescription_line_mix->condition_active}}
      <!-- Activation -->
      <input type="hidden" name="condition_active" value="1" />
      <button class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin('','perfusion', true); } });">
        Activer
      </button>
      {{else}}
      <!-- Activation -->
      <input type="hidden" name="condition_active" value="0" />
      <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin('','perfusion', true); } });">
        Désactiver
      </button>
       {{/if}}
     </form>
  {{/if}}
	
  {{if !$_prescription_line_mix->date_pose}}
    {{if ($_prescription_line_mix->_ref_substitution_lines.CPrescriptionLineMedicament|@count || $_prescription_line_mix->_ref_substitution_lines.CPrescriptionLineMix|@count) &&
          $_prescription_line_mix->_ref_substitute_for->substitution_plan_soin}}
    <form action="?" method="post" name="changeLine-{{$prescription_line_mix_id}}">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
      <select name="object_guid" style="width: 75px;" 
              onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
      										PlanSoins.loadTraitement('{{$_prescription_line_mix->_ref_prescription->object_id}}','{{$date}}','','administration');} } )">
        <option value="">Subst.</option>
        {{foreach from=$_prescription_line_mix->_ref_substitution_lines item=lines_subst_by_chap}}
          {{foreach from=$lines_subst_by_chap item=_line_subst}}
					  <option value="{{$_line_subst->_guid}}">
              {{if $_line_subst instanceof CPrescriptionLineMix}}
						      {{$_line_subst->_short_view}}
              {{else}}	
							  {{$_line_subst->_view}}
						  {{/if}}
						{{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
	        {{/foreach}}
	      {{/foreach}}
      </select>
    </form>
    {{/if}}
  {{/if}}
</td>

<td style="width: 200px;" class="text compact">
   {{foreach from=$_prescription_line_mix->_ref_lines item=_line}}
     <div style="margin: 5px 0;">
		   <strong>{{$_line->_ucd_view}}</strong>
		   <div>
         {{$_line->_posologie}}
	       {{if $_line->_unite_administration && $_line->_unite_administration != "ml"}}
	         [{{$_line->_unite_administration}}]
	       {{/if}}
       </div>
     </div>      
   {{/foreach}}

  <hr style="width: 70%; border-color: #aaa; margin: 1px auto;">
	<div style="white-space: nowrap;">
	{{if $_prescription_line_mix->_frequence}}
	  {{if $_prescription_line_mix->type_line == "perfusion"}}Débit initial: {{/if}}
		{{$_prescription_line_mix->_frequence}}
		{{if $_prescription_line_mix->volume_debit && $_prescription_line_mix->duree_debit}}
		  <br />
		  ({{mb_value object=$_prescription_line_mix field=volume_debit}} ml en {{mb_value object=$_prescription_line_mix field=duree_debit}} h)
		{{/if}}
	{{/if}}
  </div> 
</td>
	
<th></th>

{{if !$_prescription_line_mix->signature_prat && $conf.dPprescription.CPrescription.show_unsigned_med_msg}}
   {{foreach from=$count_composition_dossier key=_view_date item=_hours_by_moment}}
     {{foreach from=$_hours_by_moment key=moment_journee item=_count}}
     {{assign var=count_colspan value=$_count-2}}
      <td class="{{$_view_date}}-{{$moment_journee}}" colspan="{{$count_colspan}}">
        <div class="small-warning">Ligne non signée</div>
      </td>      
    {{/foreach}}
  {{/foreach}}
{{else}}
  <!-- Affichage des planifications manuelles -->

  {{assign var=nb_prevue value=""}}
	{{if $conf.dPprescription.CPrescription.manual_planif}}
	  <td id="manual_planifs_{{$_prescription_line_mix->_guid}}">
		 {{if $_prescription_line_mix->_continuite == "discontinue"}}
		   <div class="manual_planif_line"> 
			   {{foreach from=$_prescription_line_mix->_planifs_systeme item=_planifs_systeme}}
				   {{foreach from=$_planifs_systeme item=_planif_systeme}}
			     <div style="text-align: center; width: 40px; background-color: #fff; margin-top: -5px; margin-bottom: -5px;" 
					      data-datetime="{{$_planif_systeme->dateTime}}"
								data-planif_id="{{$_planif_systeme->_id}}" 
								data-prescription_line_mix_id="{{$_prescription_line_mix->_id}}"
             
								class="draggable administration manual_planif"
								id="drag_{{$_planif_systeme->_id}}">
	           {{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name="lines_planif"}}
						   {{assign var=nb_prevue value=$_perf_line->_quantite_administration}} 
	             {{$nb_prevue}} {{if !$smarty.foreach.lines_planif.last}}<hr style="margin:0" />{{/if}}
						 {{/foreach}}
			     </div>
			     <script type="text/javascript">
            var draggable_planif = $("drag_{{$_planif_systeme->_id}}");
            new Draggable("drag_{{$_planif_systeme->_id}}", PlanSoins.oDragOptions);
            draggable_planif.onmousedown = function(){
              PlanSoins.addDroppablesPerfDiv(draggable_planif);
            }
          </script>
					{{/foreach}}
			   {{/foreach}}
			  </div>
      {{/if}}
	  </td>
	{{/if}}
	{{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
		      {{assign var=_date_hour value="$_date $_heure_reelle"}}	
			    <td class="{{$_view_date}}-{{$moment_journee}} td_hour_adm colorPlanif 
					           {{if @!array_key_exists(manual, $_prescription_line_mix->_prises_prevues.$_date.$_hour)}}canDrop{{/if}}"
					    data-datetime="{{$_date}} {{$_hour}}:00:00"
							style='text-align: center; padding: 0; width: 100px; cursor: pointer; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
						
						<div style="position: relative;">
						  {{if @$app->user_prefs.show_hour_onmouseover_plan_soins}}
                <span class="hour_adm">
                  {{$_hour}}h
                </span>
						  {{/if}}
						
						{{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name="foreach_perf_line"}}
					     {{if isset($_perf_line->_administrations.$_date.$_hour|smarty:nodefaults)}}
					       {{assign var=nb_adm value=$_perf_line->_administrations.$_date.$_hour}}
								{{else}}
					       {{assign var=nb_adm value=""}}
					     {{/if}}

							 {{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour|smarty:nodefaults)}} 
							   {{if array_key_exists('real_hour', $_prescription_line_mix->_prises_prevues.$_date.$_hour)}}
								   {{assign var=count_prises value=$_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour|@count}}
	                 {{assign var=nb_prevue value=$_perf_line->_quantite_administration*$count_prises}} 
	                 {{assign var=hour_prevue value=$_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour}}
								 {{else}}
								   {{assign var=perf_line_id value=$_perf_line->_id}}
									 {{if array_key_exists($perf_line_id, $_prescription_line_mix->_prises_prevues.$_date.$_hour.manual)}}
								     {{assign var=nb_prevue value=$_prescription_line_mix->_prises_prevues.$_date.$_hour.manual.$perf_line_id}}
									 {{else}} 
                     {{assign var=nb_prevue value=""}}
                   {{/if}}
								 {{/if}}
							 {{else}}
							   {{assign var=nb_prevue value=""}}
							   {{assign var=hour_prevue value=""}}
							 {{/if}}
							 
							 {{assign var=etat value=""}}
							 {{if $nb_prevue && $_prescription_line_mix->_active && ($nb_adm || $nb_adm == 0)}}
                 {{if $nb_prevue == $nb_adm}}
								   {{assign var=etat value="administre"}}
                 {{elseif $nb_adm == '0'}}
								   {{assign var=etat value="administration_annulee"}}
                 {{elseif $nb_adm}}
								   {{assign var=etat value="administration_partielle"}}
                 {{elseif $_date_hour < $now}}
								   {{assign var=etat value="non_administre"}}
                 {{else}}
								   {{assign var=etat value="a_administrer"}}
								 {{/if}}
               {{/if}}
							 
							 {{if $nb_prevue && $conf.dPprescription.CPrescription.manual_planif}}
			          <script type="text/javascript">
			            // Pour empecher de deplacer une case ou il y a plusieurs prises
			            drag = new Draggable("drag-{{$_perf_line->_id}}-{{$_date}}-{{$_hour}}", PlanSoins.oDragOptions);
			          </script>
			         {{/if}}
							 
               <div id="drag-{{$_perf_line->_id}}-{{$_date}}-{{$_hour}}" style="margin: 3px;
							       {{if ($_prescription_line_mix->_fin && ($_prescription_line_mix->_fin|date_format:"%Y-%m-%d %H:00:00" < $_date_hour)) || ($_prescription_line_mix->_debut|date_format:"%Y-%m-%d %H:00:00" > $_date_hour) || !$_prescription_line_mix->_active}}
		                  background-color: #ccc
											{{/if}}"
										{{if $_prescription_line_mix->_active}}
							      {{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour|smarty:nodefaults)}}
										onclick="ObjectTooltip.createDOM(this, 'tooltip-content-prises-{{$_prescription_line_mix->_guid}}-{{$_date}}-{{$_hour}}', { duration: 0 } );"
										{{else}}
											ondblclick='
											  var planifs = $("manual_planifs_{{$_prescription_line_mix->_guid}}") ? $("manual_planifs_{{$_prescription_line_mix->_guid}}").select("div.planif_poste") : "";
												if (($V(getForm("mode_dossier_soin").mode_dossier) == "planification") && planifs && (planifs.length >= 1) && {{$conf.dPprescription.CPrescription.manual_planif}} && "{{$nb_prevue}}" == ""){
												  var last_planif = planifs.last();
													PlanSoins.addPlanificationPerf(last_planif.get("planif_id"), "{{$_date}} {{$_hour}}:00:00", "{{$_prescription_line_mix->_id}}", last_planif.get("datetime"));
										    } else {
											    PlanSoins.addAdministrationPerf("{{$_prescription_line_mix->_id}}","{{$_date}}","{{$_hour}}", null,"{{$sejour->_id}}");
	                    	}'
										{{/if}}
										{{/if}}
                    class="administration {{$etat}} perfusion {{if $nb_prevue && $conf.dPprescription.CPrescription.manual_planif}}draggablePlanif{{/if}}"
										data-prescription_line_mix_id="{{$_prescription_line_mix->_id}}"
										data-original_dateTime="{{$_date}} {{$_hour}}:00:00">
				
								 {{* Affichage des prises prevues et des administrations *}}
								 {{if $nb_adm}}{{$nb_adm}}
								 {{elseif $nb_prevue && $_prescription_line_mix->_active}}0{{/if}}
								 {{if $nb_prevue && $_prescription_line_mix->_active}}/{{$nb_prevue}}{{/if}}
								 
								 {{if (($_prescription_line_mix->_fin && ($_prescription_line_mix->_fin|date_format:"%Y-%m-%d %H:00:00" < $_date_hour)) || $_prescription_line_mix->_debut|date_format:"%Y-%m-%d %H:00:00" > $_date_hour) && $_prescription_line_mix->_active}}
                 <small>{{if $_prescription_line_mix->_fin > $_date_hour}}&gt;{{else}}&lt;{{/if}} </small>
                 {{/if}}
							 </div>
						{{/foreach}}
						
						{{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour|smarty:nodefaults)}}
						  <div id="tooltip-content-prises-{{$_prescription_line_mix->_guid}}-{{$_date}}-{{$_hour}}" style="display: none;">
							  	{{foreach from=$_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour item=_prises}}
									  {{foreach from=$_prises item=_prise}}
										  <table class="tbl">
										    <tr>
										    	<th colspan="2">
										    	  <button class="search" type="button" onclick='PlanSoins.addAdministrationPerf("{{$_prescription_line_mix->_id}}","{{$_date}}","{{$_hour}}","{{$_prise}}","{{$sejour->_id}}");'>Administrations de {{$_prise|date_format:$conf.time}}</button>
													</th>
										    </tr>
												{{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line}}
												<tr>
												  <td>{{$_perf_line->_ref_produit->libelle_abrege}}</td>
													<td>{{$_perf_line->_quantite_administration}} {{$_perf_line->_unite_administration}}</td
	                      </tr>
											  {{/foreach}}
	                    </table>	
										 {{/foreach}} 
								  {{/foreach}}
							</div>
						{{/if}}
						
						<!-- Affichage de la durée de passage indiquée dans la prescription -->
            {{if $_prescription_line_mix->_active}}
							{{if $nb_prevue && $_prescription_line_mix->duree_passage}}
						    <div class="opacity-70" style="font-size: 0.8em; height: 1em; text-align: center; background-color: #ccc; padding: 2px;">
	                {{$_prescription_line_mix->duree_passage}} min
							  </div>
	            {{else}}
						    <div style="font-size: 0.8em; height: 1em; padding: 2px;"></div>
						  {{/if}}
	             
							{{if $_prescription_line_mix->_debit}}
							  {{if array_key_exists($_date_hour, $_prescription_line_mix->_variations)}}
								   <table class="layout" style="width: 100%; margin: -2px; height: 2em;">
								   	<tr>
									 {{foreach from=$_prescription_line_mix->_variations.$_date_hour key=hour_variation item=_variation name="foreach_variation"}}
										 {{if !@$variation_id}}
								       {{assign var=background value="BBF"}} 
	                     {{assign var=variation_id value=$_variation.variation_id}}
	              		 {{/if}}
								     
										 {{if $variation_id != $_variation.variation_id}}
										   {{if $background == "BBF"}}
											  {{assign var=background value="BCF"}} 
											 {{else}}
											  {{assign var=background value="BBF"}} 
											 {{/if}} 
										   {{assign var=variation_id value=$_variation.variation_id}}
										 {{/if}} 
							
									   <td style="padding: 0; width: {{$_variation.pourcentage}}%; vertical-align: bottom;">
	                     <div onmouseover="showDebit(this, '777'); ObjectTooltip.createDOM(this, 'tooltip-content-debit-{{$_prescription_line_mix->_id}}-{{$_variation.debit}}-{{$_variation.variation_id}}');" 
											      onmouseout="showDebit(this, '{{$background}}');" style="position: relative; padding: 0px; margin: 0px;">
											 <div class="{{$_variation.debit}}-{{$_variation.variation_id}}"
											      style="position: absolute;  bottom:0px; width: 100%; background-color: #{{$background}}; border-right: 0px; border-left: 0px; height: {{$_variation.height}}em; {{if $_variation.debit == '0'}}border-bottom: 1px solid red;{{/if}}"></div>
											 {{if $_variation.debit != ''}}
											   <div style="position: absolute; bottom:0px; height: {{$_variation.normale}}em; width: 100%; border-top: 1px solid #000;"></div>
										   {{/if}}
										 </div>
										 <span id="tooltip-content-debit-{{$_prescription_line_mix->_id}}-{{$_variation.debit}}-{{$_variation.variation_id}}" style="display: none">
										   Débit: {{$_variation.debit}} ml/h
										 </span>
										 </td>
									 {{/foreach}}
									 </tr>
									 </table>
							   {{else}}
								   <table class="layout" style="width: 100%; margin: -2px; height: 2em;">
	                   <tr>
	                     <td></td>
	                   </tr>
	                 </table>
							 {{/if}}
						 {{/if}}
						 {{/if}}
						 </div>
						 
						 </div>
			    </td>
		    {{/foreach}}
     {{/foreach}}		   
   {{/foreach}}
 {{/foreach}}		
{{/if}}

<th></th>

<td style="text-align: center">
	 <div class="mediuser" style="border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}}">
   {{if $_prescription_line_mix->signature_prat}}
   <img src="images/icons/tick.png" title="Signée le {{$_prescription_line_mix->_ref_log_signature_prat->date|date_format:$conf.datetime}} par {{$_prescription_line_mix->_ref_praticien->_view}}" />
   {{else}}
   <img src="images/icons/cross.png" title="Non signée par le praticien" />
   {{/if}}
 </div>
</td>
<td style="text-align: center">
 {{if $_prescription_line_mix->signature_pharma}}
 <img src="images/icons/tick.png" title="Signée par le pharmacien" />
 {{else}}
 <img src="images/icons/cross.png" title="Non signée par le pharmacien" />
 {{/if}}
</td>