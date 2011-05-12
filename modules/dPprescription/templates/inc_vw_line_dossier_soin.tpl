{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}
{{assign var=transmissions_line value=$line->_transmissions}}
{{assign var=administrations_line value=$line->_administrations}}
{{assign var=transmissions value=$prescription->_transmissions}}
{{mb_default var=update_plan_soin value=0}}

{{if $line instanceof CPrescriptionLineMedicament}}
  {{assign var=nb_lines_chap value=$prescription->_nb_produit_by_chap.$type}}
{{else}}
  {{assign var=nb_lines_chap value=$prescription->_nb_produit_by_chap.$name_chap}}
{{/if}}


<tr id="line_{{$line_class}}_{{$line_id}}_{{$unite_prise|regex_replace:'/[^a-z0-9_-]/i':'_'}}">
	{{if @$show_patient}}
	<td style="text-align: center;" class="text">
	  <span onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_ref_object->_guid}}')">
    {{mb_ditto name="patient" value=$prescription->_ref_patient->_view}}
		</span>
	</td>
	{{/if}}
  
  {{if $conf.dPprescription.CPrescription.show_categories_plan_soins && !$line->inscription && !@$show_patient}}
		{{if $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
	    {{if $line_class == "CPrescriptionLineMedicament"}}
	      {{assign var=libelle_ATC value=$line->_ref_produit->_ref_ATC_2_libelle}}
	      <!-- Cas d'une ligne de medicament -->
	      <th class="text {{if @$transmissions.ATC.$libelle_ATC|@count}}transmission{{else}}transmission_possible{{/if}}" rowspan="{{$prescription->_nb_produit_by_cat.$type.$_key_cat_ATC}}" 
	          onclick="addCibleTransmission('{{$line->_ref_prescription->object_id}}',null, null,'{{$libelle_ATC}}', '{{$update_plan_soin}}')"
						style="font-weight: normal; font-size: 0.9em;">
		      <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$libelle_ATC}}')">
	          {{$libelle_ATC|smarty:nodefaults}}
	        </span>
	        <div id="tooltip-content-{{$libelle_ATC}}" style="display: none; color: black; text-align: left">
	       		{{if @is_array($transmissions.ATC.$libelle_ATC)}}
	  		      <ul>
	  			  {{foreach from=$transmissions.ATC.$libelle_ATC item=_trans}}
	  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:$conf.datetime}}:<br /> {{$_trans->text}}</li>
	  			  {{/foreach}}
	  		      </ul>
	  			  {{else}}
	  			    Aucune transmission
	  			  {{/if}}
			    </div>
			  
		      {{if $line->_ref_produit->_ref_fiches_ATC}}
		        <img src="images/icons/search.png" onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-{{$_key_cat_ATC}}")' />
		      {{/if}}
		      
		      <div id="tooltip-content-{{$_key_cat_ATC}}" style="display: none;">
		          <strong>Fiches disponibles</strong><br />
		          <ul>
		          {{foreach from=$line->_ref_produit->_ref_fiches_ATC item=_fiche_ATC}}
		            <li><a href="#{{$_fiche_ATC->_id}}" onclick="PlanSoins.viewFicheATC('{{$_fiche_ATC->_id}}');">Fiche ATC {{if $_fiche_ATC->libelle}}{{$_fiche_ATC->libelle}}{{/if}}</a></li>
		          {{/foreach}}
		          </ul>
		      </div>
				</th>
	    {{else}}
	        <!-- Cas d'une ligne d'element, possibilité de rajouter une transmission à la categorie -->
	        {{assign var=categorie_id value=$categorie->_id}}
	        <th class="text {{if @$transmissions.CCategoryPrescription.$name_cat|@count}}transmission{{else}}transmission_possible{{/if}}" 
	            rowspan="{{$prescription->_nb_produit_by_cat.$name_cat}}" 
	            onclick="addCibleTransmission('{{$line->_ref_prescription->object_id}}','CCategoryPrescription','{{$name_cat}}', null, '{{$update_plan_soin}}');">
	          <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$name_cat}}')">
	            {{$categorie->nom}}
	          </span>
	          <div id="tooltip-content-{{$name_cat}}" style="display: none; color: black; text-align: left">
	       		{{if @is_array($transmissions.CCategoryPrescription.$name_cat)}}
	  		      <ul>
	  			  {{foreach from=$transmissions.CCategoryPrescription.$name_cat item=_trans}}
	  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:$conf.datetime}}:<br /> {{$_trans->text}}</li>
	  			  {{/foreach}}
	  		      </ul>
	  			{{else}}
	  			  Aucune transmission
	  			{{/if}}
			  </div>
		    </th>
	    {{/if}}
    
	  {{/if}}
  {{/if}}
	
  {{if $line->inscription || $smarty.foreach.$last_foreach.first}}
  <td {{if $conf.dPprescription.CPrescription.show_categories_plan_soins && $line->inscription}}colspan="2"{{/if}}
      class="text" rowspan="{{$nb_line}}" {{if $line instanceof CPrescriptionLineMedicament && $line->traitement_personnel}}style="background-color: #BDB"{{/if}}>
		{{if $line->commentaire}}
      <img src="images/icons/postit.png" title="" style="float: right; margin: 2px;" onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-comment-{{$line->_guid}}');" />
			<table class="tbl" id="tooltip-content-comment-{{$line->_guid}}" style="display: none;">
			  <tr>
			  	<th>Commentaire - {{$line->_view}}</th>
				</tr>	
			 <tr>
			 	<td class="text" style="width: 300px;">
			 		 {{$line->commentaire}}
			 	</td>
			 </tr>
			</table>
    {{/if}}
		
	  {{if $line->_recent_modification}}
		  {{if @$conf.object_handlers.CPrescriptionAlerteHandler && $line->_ref_alerte->_id}}
				<div id="alert_manuelle_{{$line->_ref_alerte->_id}}">
					 {{assign var=img_src value="ampoule"}}
					 {{if $line->_urgence}}
					   {{assign var=img_src value="ampoule_urgence"}}
					 {{/if}}
					 <img style="float: right" src="images/icons/{{$img_src}}.png" onmouseover="alerte_prescription = ObjectTooltip.createDOM(this, 'editAlerte-{{$line->_ref_alerte->_id}}'); "/>
	         
				  <div id="editAlerte-{{$line->_ref_alerte->_id}}" style="display: none;">
					  <table class="form">
					  	<tr>
					  	  <th class="category">Alerte</th>
							</tr>	
					  	<tr>
					  		<td class="text" style="width: 300px;">{{$line->_ref_alerte->comments}}</td>
					  	</tr>
							<tr>
								<td class="button">
									<form name="modifyAlert-{{$line->_ref_alerte->_id}}" action="?" method="post" class="form-alerte{{if $line->_urgence}}-urgence{{/if}}"
									      onsubmit="return onSubmitFormAjax(this, { 
												            onComplete: function() { $('alert_manuelle_{{$line->_ref_alerte->_id}}').hide(); if(alerte_prescription) { alerte_prescription.hide(); } } });">
									  <input type="hidden" name="m" value="system" />
										<input type="hidden" name="dosql" value="do_alert_aed" />
										<input type="hidden" name="del" value="" />
	                  <input type="hidden" name="alert_id" value="{{$line->_ref_alerte->_id}}" />
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
				{{if is_array($line->_dates_urgences) && array_key_exists($date, $line->_dates_urgences)}}
		      <img style="float: right" src="images/icons/ampoule_urgence.png" title="Urgence"/>
		    {{/if}}
    	{{/if}}
		{{/if}}

    <!-- Gestion de la prise de RDV pour une ligne d'element -->
		{{if $line instanceof CPrescriptionLineElement && $line->_ref_element_prescription->rdv}}
		<span id="show_task_{{$line->_id}}">
			{{mb_include module=soins template=inc_vw_task_icon}}
		</span>
    {{/if}}
		
		<div onclick="addCibleTransmission('{{$line->_ref_prescription->object_id}}', '{{$line_class}}', '{{$line->_id}}', null, '{{$update_plan_soin}}');" 
	       class="{{if @$transmissions.$line_class.$line_id|@count}}transmission{{else}}transmission_possible{{/if}}"
				 onmouseover="
           {{if $line instanceof CPrescriptionLineMedicament || $line instanceof CPrescriptionLineMix }}
             ObjectTooltip.createEx(this, '{{$line->_guid}}');
           {{else}}
             ObjectTooltip.createEx(this, '{{$line->_ref_element_prescription->_guid}}');
           {{/if}}"
				 style="font-weight: bold">
	   
	      {{if $line_class == "CPrescriptionLineMedicament"}}
					{{$line->_ucd_view}}
					<br />
          {{include file="../../dPprescription/templates/inc_vw_info_line_medicament.tpl"}}
					{{if $line->traitement_personnel}} (Traitement perso){{/if}}
	      {{else}}
				  <div class="mediuser" style="border-color: #{{$line->_ref_element_prescription->_color}}">
					  {{$line->_view}}
						{{if $line->cip_dm}}
						  <br />
						  <span style="opacity: 0.7; font-size: 0.8em;">({{$line->_ref_dm->libelle}})</span>
						{{/if}}
					</div>
				{{/if}} 
	  </div>
		  
    {{if $line->conditionnel}}
      <form action="?" method="post" name="activeCondition-{{$line_id}}-{{$line_class}}">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="{{$dosql}}" />
        <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
        <input type="hidden" name="del" value="0" />
        
        {{if !$line->condition_active}}
	      <!-- Activation -->
	      <input type="hidden" name="condition_active" value="1" />
	      <button class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin('','{{$chapitre}}', true); } });">
	        Activer
	      </button>
	      {{else}}
 				<!-- Activation -->
	      <input type="hidden" name="condition_active" value="0" />
	      <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin('','{{$chapitre}}', true); } });">
	        Désactiver
	      </button>
	       {{/if}}
       </form>
		{{/if}}
		
	  {{if $line instanceof CPrescriptionLineMedicament}}
	    {{if !$line->_count.administration}}
		    {{if ($line->_ref_substitution_lines.CPrescriptionLineMedicament|@count || $line->_ref_substitution_lines.CPrescriptionLineMix|@count) &&
		    			$line->_ref_substitute_for->substitution_plan_soin}}
			    <form action="?" method="post" name="changeLine-{{$line->_guid}}">
			      <input type="hidden" name="m" value="dPprescription" />
			      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
			      <select name="object_guid" style="width: 75px;" 
			              onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
			                           PlanSoins.loadTraitement('{{$line->_ref_prescription->object_id}}','{{$date}}','','administration')
			                         } } )">
			        <option value="">Subst.</option>
				      {{foreach from=$line->_ref_substitution_lines item=lines_subst_by_chap}}
				          {{foreach from=$lines_subst_by_chap item=_line_subst}}
									{{if $_line_subst->_class_name == "CPrescriptionLineMix"}}
									<option value="{{$_line_subst->_guid}}">{{$_line_subst->_short_view}}
		              {{else}}
				          <option value="{{$_line_subst->_guid}}">{{$_line_subst->_view}}
				          {{/if}}
									{{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
				        {{/foreach}}
				      {{/foreach}}
			      </select>
			    </form>
		    {{/if}}
	    {{/if}}
	  {{/if}}
		</td>
  {{/if}}
  
  <!-- Affichage des posologies de la ligne -->
  <td class="text">
  	{{if !$line->signee && $line instanceof CPrescriptionLineMedicament && $conf.dPprescription.CPrescription.show_unsigned_med_msg}}
		
		{{else}}
	    <small style="font-weight: bold;">
	    {{if @$line->_prises_for_plan.$unite_prise}}
	      {{if is_numeric($unite_prise)}}
	        <!-- Cas des posologies de type "tous_les", "fois par" ($unite_prise == $prise->_id) -->
	        <div style="white-space: nowrap;">
		        {{assign var=prise value=$line->_prises_for_plan.$unite_prise}}
		        {{$prise->_short_view}}
	        </div>
	      {{else}}
	        <!-- Cas des posologies sous forme de moments -->
	        {{foreach from=$line->_prises_for_plan.$unite_prise item=_prise}}
	          <div style="white-space: nowrap;">
						  {{if $_prise->condition}}
								<form name="duplicate-{{$_prise->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ refreshDossierSoin('','{{$chapitre}}', true); }  })">
							  	<input type="hidden" name="m" value="dPprescription" />
									<input type="hidden" name="del" value="0" />
	                <input type="hidden" name="dosql" value="do_duplicate_prise_cond_aed" />
	                <input type="hidden" name="prise_id" value="{{$_prise->_id}}" />
		              <button type="submit" class="add notext"></button>
								 </form>
							 {{/if}}
						  {{$_prise->_short_view}}
						</div>
	        {{/foreach}}
	      {{/if}}
	    {{else}}
	      {{if $line->inscription}}
			Inscription
		  {{/if}}	
	    {{/if}}
	    </small>
		{{/if}}

    {{if $line instanceof CPrescriptionLineMedicament}}
	    <!-- Affichage de la forme galenique -->
			<div style="opacity: 0.7;">
			{{if !$line->inscription}}
			  <hr style="width: 70%; border-color: #AAA; margin: 1px auto;" />
	    {{/if}}
			
			  <small>
	        {{$line->_forme_galenique}}
	        {{if $line->_ref_produit_prescription->unite_prise}}
	          ({{$line->_ref_produit_prescription->unite_prise}})
	        {{else}}
	          {{if $line->_unite_administration && ($line->_unite_administration != $line->_forme_galenique)}}
	            ({{$line->_unite_administration}})
	          {{/if}}
	        {{/if}}
					{{if $line->voie}}
					  ({{$line->voie}})
					{{/if}}
	      </small>
	    </div>
	  {{/if}}
  </td>
  
		{{if !$line->inscription}}
			{{if @$nb_lines_element}}
			  {{if $first_iteration}}
			    <th class="before" style="cursor: pointer" onclick="PlanSoins.showBefore();" rowspan="{{$nb_lines_element}}" onmouseout="clearTimeout(PlanSoins.timeOutBefore);">
            <img src="images/icons/a_left.png" />
          </th>
				{{/if}}
			{{else}}
			  {{if $smarty.foreach.$global_foreach.first && $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
				  <th class="before" style="cursor: pointer" onclick="PlanSoins.showBefore();" rowspan="{{$nb_lines_chap}}" onmouseout="clearTimeout(PlanSoins.timeOutBefore);">
				    <img src="images/icons/a_left.png" />
					</th>
				{{/if}}
		 	{{/if}}
		{{else}}
	    <th></th>
	  {{/if}}
	 
	  <td id="first_{{$line_id}}_{{$line_class}}_{{$unite_prise|regex_replace:'/[^a-z0-9_-]/i':'_'}}" style="display: none;">
	  </td>
	  
		{{if !$line->signee && $line->_class_name == "CPrescriptionLineMedicament" && $conf.dPprescription.CPrescription.show_unsigned_med_msg && !$line->inscription}}
		
		{{foreach from=$count_composition_dossier key=_view_date item=_hours_by_moment}}
      {{foreach from=$_hours_by_moment key=moment_journee item=_count}}
			  {{assign var=count_colspan value=$_count-2}}
        <td class="{{$_view_date}}-{{$moment_journee}}" colspan="{{$count_colspan}}">
	        <div class="small-warning">Ligne non signée</div>
	      </td>      
	      {{/foreach}}
	    {{/foreach}}
   {{else}} 
     {{include file="../../dPprescription/templates/inc_vw_content_line_dossier_soin.tpl" nodebug=true}}
   {{/if}}
	 
	  <td id="last_{{$line_id}}_{{$line_class}}_{{$unite_prise|regex_replace:'/[^a-z0-9_-]/i':'_'}}" style="display: none;">
	  </td>
 
   {{if !$line->inscription}}
	   {{if @$nb_lines_element}}
		   {{if $first_iteration}}
			    <th class="after" style="cursor: pointer" onclick="PlanSoins.showAfter();" rowspan="{{$nb_lines_element}}" onmouseout="clearTimeout(PlanSoins.timeOutAfter);">
	          <img src="images/icons/a_right.png" />
	        </th>
				{{/if}}
		 {{else}}
	     {{if $smarty.foreach.$global_foreach.first &&  $smarty.foreach.$first_foreach.first  && $smarty.foreach.$last_foreach.first}}
	       <th class="after" style="cursor: pointer" onclick="PlanSoins.showAfter();" rowspan="{{$nb_lines_chap}}" onmouseout="clearTimeout(PlanSoins.timeOutAfter);">
	           <img src="images/icons/a_right.png" />
	        </th>
	     {{/if}}		 
		 {{/if}}

	 {{else}}
	   <th></th>
	 {{/if}}
 
 <!-- Signature du praticien -->
 <td style="text-align: center">
 	 <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}}">
   {{if $line->signee}}
   <img src="images/icons/tick.png" title="Signée le {{$line->_ref_log_signee->date|date_format:$conf.datetime}} par {{$line->_ref_praticien->_view}}" />
   {{else}}
   <img src="images/icons/cross.png" title="Non signée par le praticien" />
   {{/if}}
	 </div>
 </td>
 <!-- Signature du pharmacien -->
 <td style="text-align: center">
	  {{if $line_class == "CPrescriptionLineMedicament"}}
	    {{if $line->valide_pharma}}
	    <img src="images/icons/tick.png" title="Signée par le pharmacien" />
	    {{else}}
	    <img src="images/icons/cross.png" title="Non signée par le pharmacien" />
	    {{/if}}
	  {{else}}
	    - 
	  {{/if}}
  </td>
</tr>