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
	
<td colspan="2" class="text">
  {{if $move_dossier_soin}}
	  <script type="text/javascript">
	    Main.add(function () {
	      $("line_{{$_prescription_line_mix->_guid}}").show();
	    });
	  </script>
  {{/if}}
  
	
  {{if $_prescription_line_mix->_recent_modification}}
    <img style="float: right; margin: 2px;" src="images/icons/ampoule.png" title="Ligne recemment modifiée" />
  {{/if}}
	
	{{if $_prescription_line_mix->commentaire}}
    <img style="float: right; margin: 2px;" src="images/icons/flag.png" title="" onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-comment-{{$_prescription_line_mix->_guid}}');" />
    <span id="tooltip-content-comment-{{$_prescription_line_mix->_guid}}" style="display: none;">
      {{$_prescription_line_mix->commentaire}}
    </span>
  {{/if}}
	
  <div style="cursor: pointer; padding: 2px;" class="{{if @$transmissions.CPrescriptionLineMix.$prescription_line_mix_id|@count}}transmission{{else}}transmission_possible{{/if}}"
     onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_guid}}')" 
	   onclick='editPerf("{{$_prescription_line_mix->_id}}","{{$date}}",document.mode_dossier_soin.mode_dossier.value, "{{$sejour->_id}}");
	            addCibleTransmission("CPrescriptionLineMix","{{$_prescription_line_mix->_id}}","{{$_prescription_line_mix->_view}}");'>
    {{tr}}CPrescriptionLineMix.type.{{$_prescription_line_mix->type}}{{/tr}} ({{$_prescription_line_mix->voie}})
  </div>
	
	{{if $_prescription_line_mix->vitesse}}
		<form style="white-space: nowrap" name="modifDebit-{{$prescription_line_mix_id}}" method="post" action="?" onsubmit="return onSubmitFormAjax(this, { onComplete: function() { 
                            Prescription.loadTraitement('{{$_prescription_line_mix->_ref_prescription->object_id}}','{{$date}}','','administration');} } )">
		  <input type="hidden" name="m" value="dPprescription" />
			<input type="hidden" name="dosql" value="do_prescription_line_mix_variation_aed" />
      <input type="hidden" name="prescription_line_mix_variation_id" value="" />
      <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
			<input type="hidden" name="dateTime" value="current" />
      Débit {{mb_field object=$_prescription_line_mix->_last_variation field="debit" form="modifDebit-$prescription_line_mix_id" increment=1 min=0 size=2}} ml/h
			<button type="submit" class="submit notext" ></button>
		</form>
	{{/if}}
	
  {{if $_prescription_line_mix->_active}}
	  {{if $_prescription_line_mix->signature_prat || !$dPconfig.dPprescription.CPrescription.show_unsigned_med_msg}}
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
      										Prescription.loadTraitement('{{$_prescription_line_mix->_ref_prescription->object_id}}','{{$date}}','','administration');} } )">
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

<td style="font-size: 1em; width: 200px;" class="text">
  <ul style="list-style-type: none; padding-left: 0px;">
   {{foreach from=$_prescription_line_mix->_ref_lines item=_line}}
     <li style="margin-bottom: 7px;">
		   <small>
		   	 {{$_line->_ucd_view}}<br />
		      <span style="opacity: 0.5;"> {{$_line->_posologie}}</span>
			 </small>
			 
      {{if $_line->_unite_administration && $_line->_unite_administration != "ml"}}
			  [{{$_line->_unite_administration}}]
 	    {{/if}}
     </small>
     </li>
   {{/foreach}}
  </ul>
	<span style="opacity: 0.5; white-space: nowrap;">
	<small>
	{{if $_prescription_line_mix->_frequence}}
	  Débit initial: {{$_prescription_line_mix->_frequence}}
	{{/if}}
	</small>
	</span>
</td>
	
<th></th>

{{if !$_prescription_line_mix->signature_prat && $dPconfig.dPprescription.CPrescription.show_unsigned_med_msg}}
  {{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      <td class="{{$_view_date}}-{{$moment_journee}}" colspan="{{if $moment_journee == 'soir'}}{{$count_soir-2}}{{/if}}
                             {{if $moment_journee == 'nuit'}}{{$count_nuit-2}}{{/if}}
                             {{if $moment_journee == 'matin'}}{{$count_matin-2}}{{/if}}">
        <div class="small-warning">Ligne non signée</div>
      </td>      
    {{/foreach}}
  {{/foreach}}
{{else}}

	{{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
		      {{assign var=_date_hour value="$_date $_heure_reelle"}}	
			    <td class="{{$_view_date}}-{{$moment_journee}}" style='text-align: center; padding: 0; width: 100px; cursor: pointer; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
						{{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name="foreach_perf_line"}}
					     {{if isset($_perf_line->_administrations.$_date.$_hour|smarty:nodefaults)}}
					       {{assign var=nb_adm value=$_perf_line->_administrations.$_date.$_hour}}
								{{else}}
					       {{assign var=nb_adm value=""}}
					     {{/if}}

							 {{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour|smarty:nodefaults)}} 
							   {{assign var=count_prises value=$_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour|@count}}
                 {{assign var=nb_prevue value=$_perf_line->_quantite_administration*$count_prises}} 
                 {{assign var=hour_prevue value=$_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour}}
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
							 
               <div style="margin: 3px;"
							      {{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour|smarty:nodefaults)}}
										onclick="ObjectTooltip.createDOM(this, 'tooltip-content-prises-{{$_prescription_line_mix->_guid}}-{{$_date}}-{{$_hour}}', { duration: 0 } );"
										{{else}}
										ondblclick='addAdministrationPerf("{{$_prescription_line_mix->_id}}","{{$_date}}","{{$_hour}}", null,document.mode_dossier_soin.mode_dossier.value, "{{$sejour->_id}}");'
										{{/if}}
                    class="administration {{$etat}} perfusion">
										
								 {{* Affichage des prises prevues et des administrations *}}
								 {{if $nb_adm}}{{$nb_adm}}
								 {{elseif $nb_prevue && $_prescription_line_mix->_active}}0{{/if}}
								 {{if $nb_prevue && $_prescription_line_mix->_active}}/{{$nb_prevue}}{{/if}}
							 </div>
						{{/foreach}}
						
						
						{{if isset($_prescription_line_mix->_prises_prevues.$_date.$_hour|smarty:nodefaults)}}
						  <div id="tooltip-content-prises-{{$_prescription_line_mix->_guid}}-{{$_date}}-{{$_hour}}" style="display: none;">
							  {{foreach from=$_prescription_line_mix->_prises_prevues.$_date.$_hour.real_hour item=_prises}}
								  {{foreach from=$_prises item=_prise}}
									  <table class="tbl">
									    <tr>
									    	<th colspan="2">
									    	  <button style="float: right" class="search" type="button" onclick='addAdministrationPerf("{{$_prescription_line_mix->_id}}","{{$_date}}","{{$_hour}}","{{$_prise}}",document.mode_dossier_soin.mode_dossier.value, "{{$sejour->_id}}");'>Administrations de {{$_prise|date_format:$dPconfig.time}}</button>
                        	
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
            {{if $nb_prevue && $_prescription_line_mix->duree_passage}}
					    <div style="opacity: 0.7; font-size: 0.8em; height: 1em; text-align: center; background-color: #ccc; padding: 2px;">
                {{$_prescription_line_mix->duree_passage}} min
						  </div>
            {{else}}
					    <div style="font-size: 0.8em; height: 1em; padding: 2px;"></div>
					  {{/if}}
             
						
						{{if $_prescription_line_mix->vitesse}}
						  {{if array_key_exists($_date_hour, $_prescription_line_mix->_variations)}}
							   <table class="layout" style="width: 100%; margin: -2px; height: 2em;">
							   	<tr>
								 {{foreach from=$_prescription_line_mix->_variations.$_date_hour key=hour_variation item=_variation name="foreach_variation"}}
									 
									 {{if !@$variation_id}}
							       {{assign var=background value="aaa"}} 
                     {{assign var=variation_id value=$_variation.variation_id}}
              		 {{/if}}
							     
									 {{if $variation_id != $_variation.variation_id}}
									 
									   {{if $background == "aaa"}}
										  {{assign var=background value="ccc"}} 
										 {{else}}
										  {{assign var=background value="aaa"}} 
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
   <img src="images/icons/tick.png" title="Signée le {{$_prescription_line_mix->_ref_log_signature_prat->date|date_format:$dPconfig.datetime}} par {{$_prescription_line_mix->_ref_praticien->_view}}" />
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