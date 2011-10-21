{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=advanced_prot value=0}}
<script type="text/javascript">

Main.add( function(){
  var editPerfForm = getForm('editPerf-{{$line->_id}}');
  
  {{if $line->type == "PCA"}}
	  $("bolus-{{$line->_id}}").show();
	  {{if $line->_perm_edit}}
		  changeModeBolus(editPerfForm);
		{{/if}}		
  {{/if}}
    
	{{if $line->type_line == "perfusion"}}
	  toggleContinuiteLineMix(editPerfForm.continuite_perf,'{{$line->_id}}');
  {{/if}}

	//Autocomplete des medicaments dans les aérosols
  var addLineAerosolForm = getForm("addLineAerosol");
	if(addLineAerosolForm){
		var urlAuto = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
		urlAuto.addParam("produit_max", 40);
		window.acAerosol = urlAuto.autoComplete(addLineAerosolForm.produit, "aerosol_auto_complete", {
		  minChars: 3,
		  updateElement: updateFieldsAerosol,
		  callback: 
		    function(input, queryString){
		      return (queryString + "&inLivret="+($V(addLineAerosolForm._recherche_livret)?'1':'0')+"&hors_specialite=0"); 
		    }
		} );
	}
	
	{{if $line->_protocole}}
	changePonctualProt(editPerfForm.__ponctual.checked, editPerfForm);
	{{else}}
	changePonctual(editPerfForm.__ponctual.checked, editPerfForm);
  {{/if}}
} );

</script>

{{mb_include module="dPprescription" template="inc_header_line"}}

<table class="tbl" id="prescription_line_mix-{{$line->_id}}"> 
{{assign var=prescription_line_mix_id value=$line->_id}}
  <tr>
    <th id="th-perf-{{$line->_id}}" class="text {{if $line->perop}}perop{{/if}}">
      <div style="float: left">
      	 <a title="Historique" class="button list notext" href="#1"
         onclick="Prescription.showLineHistory('{{$line->_guid}}')" 
         {{if $line->_ref_parent_line->_id}}
         onmouseover="ObjectTooltip.createEx(this, '{{$line->_ref_parent_line->_guid}}')"  
         {{/if}}>
         </a>
			 
				{{if $line->_ref_prescription->type != "externe"}}
				  {{if $line->_perm_edit}}
					  <form name="editCondPerf-{{$line->_id}}" action="?" method="post">
					  	<input type="hidden" name="m" value="dPprescription" />
							<input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
							<input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
				      {{mb_field object=$line field=conditionnel onchange="submitFormAjax(this.form, 'systemMsg');" typeEnum=checkbox}}
				      {{mb_label object=$line field="conditionnel"}}
						</form>
						
						<form name="editPeropPerf-{{$line->_id}}" action="?" method="post">
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
              <input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
              {{mb_field object=$line field=perop onchange="submitFormAjax(this.form, 'systemMsg');" typeEnum=checkbox}}
              {{mb_label object=$line field="perop"}}
            </form>
            
					{{else}}
				    {{mb_label object=$line field="conditionnel"}}:
				    {{if $line->conditionnel}}Oui{{else}}Non{{/if}} 
				  {{/if}}
				{{/if}}
				
      </div>
      <div style="float: right">
        {{if $line->_protocole && $line->_perm_edit && !$line->variante_for_id && !$mode_pack}}
          <button type="button" class="add" onclick="Prescription.viewSubstitutionLines('{{$line->_id}}','{{$line->_class}}')">
             Variantes
            ({{$line->_count_variantes}})
            </button>
        {{/if}}
        
         <button class="lock notext" onclick="modalPrescription.close();
				 {{if @$mode_substitution}}
           Prescription.viewSubstitutionLines.defer('{{$line->variante_for_id}}','{{$line->variante_for_class}}');
				 {{else}}
				   Prescription.reload.defer('{{$prescription->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, '', null, {{$advanced_prot}});
				 {{/if}}"></button>
		  </div>
		  <!-- Accord du praticien -->
			{{if $mode_pharma}}
				<div style="float: right">
					{{if !$line->signature_pharma}}
						<form action="?" method="post" name="editLineAccordPraticien-{{$line->_id}}">
						  <input type="hidden" name="m" value="dPprescription" />
						  <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
						  <input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
						  <input type="hidden" name="del" value="0" />
						  {{mb_field object=$line field="accord_praticien" typeEnum="checkbox" onchange="onSubmitFormAjax(this.form);"}}
						  {{mb_label object=$line field="accord_praticien"}}
						</form> 
					{{elseif $line->accord_praticien}}
					  En accord avec le praticien
					{{/if}}
				</div>
			{{/if}}
			
      <!-- Signature du praticien -->
      {{if $line->_can_vw_signature_praticien}}
        <div class="mediuser" style="float: right; border-color: #{{$line->_ref_praticien->_ref_function->color}};">	
          {{$line->_ref_praticien->_view}}
					{{if $line->signature_prat}}
					   <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
					{{else}}
					   <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
					{{/if}}
          {{if $prescription->type != "externe"}}
						{{if $line->signature_pharma}}
					    <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
        </div>
      {{/if}}
			<strong>
				{{foreach from=$line->_ref_lines item=_line_item name=perf_line}}
				  <a href="#produit{{$_line_item->_guid}}" onclick="Prescription.viewProduit(null,'{{$_line_item->code_ucd}}','{{$_line_item->code_cis}}');" style="display: inline;">
     		    {{$_line_item->_ucd_view}} 
					</a>
				  {{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
			
			<!-- Selection de la voie / interface -->
			<form name="editVoieLineMix-{{$line->_id}}" action="" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
        <input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
        <input type="hidden" name="del" value="0" />
				
				{{if $line->_perm_edit}}
				  <br />
		      {{if $line->type_line == "aerosol"}}
		       {{assign var=interfaces value="CPrescriptionLineMix"|static:"interface_by_line"}}
		       {{assign var=interfaces_for_line value=$interfaces.aerosol}}
		        
		        <select name="interface" onchange="return onSubmitFormAjax(this.form);">           
		          <option value="">&mdash; Interface</option>               
		          {{foreach from=$interfaces_for_line item=_interface}}
		            <option value="{{$_interface}}" {{if $line->interface == $_interface}}selected="selected"{{/if}}>{{tr}}CPrescriptionLineMix.interface.{{$_interface}}{{/tr}}</option> 
		          {{/foreach}}
		        </select>
		      {{else}}
		        {{if $line->_voies}}
		          <select name="voie" onchange="{{if !in_array($line->voie, $line->_voies) && $line->voie != 'none'}}
		                                          Element.hide($('warning_voie_{{$line->_id}}')); 
		                                          Element.hide($('last_option_{{$line->_id}}'));
		                                        {{/if}}
		                                        submitFormAjax(this.form, 'systemMsg');">
		            {{foreach from=$line->_voies key=key_voie item=_voie}}
		              <option value="{{$key_voie}}" {{if $line->voie == $key_voie}}selected="selected"{{/if}}>{{$_voie}}</option>
		            {{/foreach}}
		            {{if !in_array($line->voie, $line->_voies) && $line->voie != "none"}}
		              <option value="{{$line->voie}}" selected="selected" id="last_option_{{$line->_id}}">{{$line->voie}}</option>
		            {{/if}}
		          </select>
		           {{if !in_array($line->voie, $line->_voies) && $line->voie != "none"}}
		           <div class="warning" id="warning_voie_{{$line->_id}}">
		             Attention, la voie selectionnée n'est plus disponible
		           </div>
		           {{/if}}
		        {{/if}}
		      {{/if}}
		    {{else}}
		      {{if $line->type_line == "aerosol"}}
		        <strong>{{tr}}CPrescriptionLineMix.interface.{{mb_value object=$line field="interface"}}{{/tr}}</strong>
		      {{else}}
		        <strong>{{mb_value object=$line field="voie"}}</strong>
		      {{/if}}
		    {{/if}}
			</form>
    </th>
  </tr>
</table>

<table class="main layout">
  <tr>
    <td>
    	{{assign var=type_line value=$line->type_line}}
      <form name="editPerf-{{$line->_id}}" action="" method="post">
	      <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
        <input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
        <input type="hidden" name="del" value="0" />
				<input type="hidden" name="callback" value="" />
        <table class="layout main">
          <tr>
          	<td>
						  <fieldset>
						 	  <legend>Durée de la prescription</legend>
						
		            <!-- Début -->
								{{if $line->_protocole}}
									 
									 <span id="duree-{{$line->_id}}">
									 {{mb_label object=$line field=duree}}
	                 {{if $line->_can_modify_prescription_line_mix}}
									   <script type="text/javascript">
									   	Main.add(function(){
											  var oForm = getForm("editPerf-{{$line->_id}}");
												modifUniteDecal(oForm.jour_decalage, oForm.unite_decalage);
												modifUniteDecal(oForm.jour_decalage_fin, oForm.unite_decalage_fin);
                      });
										 </script>
										 {{mb_field object=$line field=duree size=1 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="\$V(this.form.jour_decalage_fin, '', false); return onSubmitFormAjax(this.form);"}}
	                   {{mb_field object=$line field=unite_duree onchange="return onSubmitFormAjax(this.form);"}}
										 </span>
										 
										 à partir de
										 {{mb_field object=$line field="jour_decalage" onchange="modifUniteDecal(this, this.form.unite_decalage); return onSubmitFormAjax(this.form);" emptyLabel="Moment"}} 
                     {{mb_field object=$line field=decalage_line showPlus="1" increment=1 size="2" form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}
                     {{mb_field object=$line field=unite_decalage onchange="return onSubmitFormAjax(this.form)" emptyLabel="Unit"}}
										 
										 <span id="date-fin-{{$line->_id}}">
										 jusqu'à 
										 {{mb_field object=$line field="jour_decalage_fin" onchange="\$V(this.form.duree, '', false); modifUniteDecal(this, this.form.unite_decalage_fin); return onSubmitFormAjax(this.form);" emptyLabel="Moment"}} 
                     {{mb_field object=$line field=decalage_line_fin showPlus="1" increment=1 size="2" form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}
                     {{mb_field object=$line field=unite_decalage_fin onchange=" return onSubmitFormAjax(this.form);" emptyLabel="Unit"}}
	                   </span>
									 {{else}}
	                   {{if $line->duree}}
	                     {{mb_value object=$line field=duree}}
	                     {{mb_value object=$line field=unite_duree}}
	                   {{else}}
	                     - 
	                   {{/if}}
										 
										 {{if $line->jour_decalage}}
											 à partir de
	                     {{mb_value object=$line field="jour_decalage"}} 
											 {{if $line->decalage_line >= 0}}+{{/if}}
											 {{mb_value object=$line field=decalage_line}} {{mb_value object=$line field=unite_decalage}}
										 {{/if}}
										 
										 {{if $line->jour_decalage_fin}}
	                     jusqu'à  
	                     {{mb_value object=$line field="jour_decalage_fin"}} 
											 {{if $line->decalage_line_fin >= 0}}+{{/if}}
											 {{mb_value object=$line field=decalage_line_fin}} {{mb_value object=$line field=unite_decalage_fin}}
										 {{/if}}
                   {{/if}}
									 
								   <span style="float: right">
                      {{mb_field object=$line field=ponctual typeEnum="checkbox" onchange="changePonctualProt(this.form.__ponctual.checked, this.form); return onSubmitFormAjax(this.form);"}}
                      {{mb_label object=$line field=ponctual}}
                    </span>
										 
								{{else}}

										<input type="hidden" name="jour_decalage" value="{{$line->jour_decalage}}" />
										<input type="hidden" name="decalage_line" value="{{$line->decalage_line}}" />
										<input type="hidden" name="unite_decalage" value="{{$line->unite_decalage}}" />
										<input type="hidden" name="jour_decalage_fin" value="{{$line->jour_decalage_fin}}" />
										<input type="hidden" name="decalage_line_fin" value="{{$line->decalage_line_fin}}" />
										<input type="hidden" name="unite_decalage_fin" value="{{$line->unite_decalage_fin}}" />
									 
		                <strong>{{mb_label object=$line field="date_debut"}}</strong>
		                {{if $line->_can_modify_prescription_line_mix}}
		                  {{mb_field object=$line field=date_debut form="editPerf-$prescription_line_mix_id" onchange="removeRelativesDates(this.form); return onSubmitFormAjax(this.form);" register=true}}
		                  {{mb_field object=$line field=time_debut form="editPerf-$prescription_line_mix_id" onchange="removeRelativesDates(this.form); return onSubmitFormAjax(this.form);" register=true}}
											
											<span id="dates-perf-{{$prescription_line_mix_id}}">
											  <strong>{{mb_label object=$line field=duree}}</strong>
											  {{mb_field object=$line field=duree size=1 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="removeRelativesDates(this.form); return onSubmitFormAjax(this.form);"}}
                        {{mb_field object=$line field=unite_duree onchange="return onSubmitFormAjax(this.form);"}}
										  </span>
											
											<span style="float: right">
		                    {{mb_field object=$line field=ponctual typeEnum="checkbox" onchange="changePonctual(this.form.__ponctual.checked, this.form); return onSubmitFormAjax(this.form);"}}
		                    {{mb_label object=$line field=ponctual}}
		                  </span>
										{{else}}
		                  {{mb_value object=$line field=date_debut}}
		                  {{mb_value object=$line field=time_debut}}
											
											<strong>{{mb_label object=$line field=duree}}</strong>
											{{mb_value object=$line field=duree}}
                      {{mb_value object=$line field=unite_duree}}
		                {{/if}}
										
		              {{/if}}
									
									
							</fieldset> 					
             
						  <fieldset>
						  	<legend>Débit</legend>
						
	              {{if $line->type_line != "aerosol"}}
	              <label>
	                <input type="radio" name="continuite_perf" 
	                                    {{if !$line->_perm_edit}}disabled="disabled"{{/if}} 
	                                    {{if $line->_continuite != 'discontinue'}}checked="checked"{{/if}} 
	                                    onchange="toggleContinuiteLineMix(this, '{{$line->_id}}');
	                                    {{if $line->type_line == 'oxygene'}}
	                                      this.form.nb_tous_les.value = ''; 
	                                      this.form.duree_passage.value = '';
	                                    {{/if}}" value="continue"/> 
	                Continue
	              </label>
	              <label>
	                <input type="radio" name="continuite_perf"
	                                    {{if !$line->_perm_edit}}disabled="disabled"{{/if}} 
	                                    {{if $line->_continuite == 'discontinue'}}checked="checked"{{/if}} 
	                                    onchange="toggleContinuiteLineMix(this, '{{$line->_id}}');" value="discontinue"/>
	                Discontinue   
	              </label>
	              {{/if}}
	              
								<div style="display: inline; {{if $line->type_line != 'aerosol'}}float: right;{{/if}}">
	              {{if $line->type_line == "perfusion"}}
	                <span style="display: none;" id="continue-{{$line->_id}}">
	                  {{assign var=types value="CPrescriptionLineMix"|static:"unite_by_line"}}
	                  {{if $line->_can_modify_prescription_line_mix}}
	                    {{mb_label object=$line field="volume_debit"}}
										  {{mb_field object=$line field="volume_debit" size="3" increment=1 min=0 form="editPerf-$prescription_line_mix_id"}} ml
                      <script type="text/javascript">
                        var oForm = getForm("editPerf-{{$line->_id}}");
                        oForm.volume_debit.onchange = function() {
                          Prescription.updateDebit('{{$prescription_line_mix_id}}');
                          oForm.nb_tous_les.value = '';
                          oForm.duree_passage.value = '';
                          return onSubmitFormAjax(oForm, {onComplete: function() {
                            {{foreach from=$line->_ref_lines item=_line_item_update}}
                              updateDebitProduit({{$_line_item_update->_id}});
                            {{/foreach}}
                          } });
                        }
                        </script>
	                    en {{mb_field object=$line field="duree_debit" size="3" increment=1 min=0 form="editPerf-$prescription_line_mix_id"}} h
                      <script type="text/javascript">
                        var oForm = getForm("editPerf-{{$line->_id}}");
                        oForm.duree_debit.onchange = function() {
                          Prescription.updateDebit('{{$prescription_line_mix_id}}');
                          oForm.nb_tous_les.value = '';
                          oForm.duree_passage.value = '';
                          return onSubmitFormAjax(oForm, {onComplete: function() {
                            {{foreach from=$line->_ref_lines item=_line_item_update}}
                              updateDebitProduit({{$_line_item_update->_id}});
                            {{/foreach}}
                          } });
                        }
                        </script>
	                    soit <span id="debitLineMix-{{$line->_id}}" style="font-weight: bold; font-size: 1.2em;">{{mb_value object=$line field="_debit"}}</span> ml/h
										{{else}}
	                    {{if $line->volume_debit}}
											  {{mb_value object=$line field="volume_debit"}} ml en {{mb_value object=$line field="duree_debit"}} h soit <strong>{{mb_value object=$line field="_debit"}} ml/h</strong>
	                    {{else}}
											  <div class="warning">Débit non renseigné</div>
											{{/if}}
										{{/if}}
	                </span>
	                <span style="display: none;" id="discontinue-{{$line->_id}}">
	                  {{if $line->type_line == "perfusion"}}
	                    {{if $line->_can_modify_prescription_line_mix}}
	                      A passer en
	                     {{mb_field object=$line size=2 field=duree_passage increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="this.form.volume_debit.value = ''; this.form.duree_debit.value = '';return onSubmitFormAjax(this.form);"}}
	                    {{elseif $line->duree_passage}}
	                      A passer en
	                      {{mb_value object=$line field=duree_passage}}
	                    {{/if}}
	                    <!-- duree de passage en minutes -->
	                    {{mb_value object=$line field=unite_duree_passage}}
	                  {{/if}} 
										<span id="nb-tous-les-{{$line->_id}}">
		                  toutes les 
		                  {{if $line->_can_modify_prescription_line_mix}}
		                    {{mb_field object=$line field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="this.form.volume_debit.value = ''; this.form.duree_debit.value = ''; return onSubmitFormAjax(this.form);"}} h
		                  {{else}}      
		                    {{mb_value object=$line field="nb_tous_les"}} h
		                  {{/if}}
										</span>
	                </div>
	              {{/if}}
								
								{{if $line->type_line == "oxygene"}}
	                  {{assign var=types value="CPrescriptionLineMix"|static:"unite_by_line"}}
	                  Débit
	                  {{if $line->_can_modify_prescription_line_mix}}
	                    {{mb_field object=$line field="volume_debit" size="3" increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}
	                  {{else}}
	                    {{mb_value object=$line field="_debit"}}
	                  {{/if}}
	                  
	                  <!-- Affichage de l'unite de prise -->
	                  {{$types.$type_line}}
	              
	                  <span style="display: none;" id="continue-{{$line->_id}}"></span>
	                  <span style="display: none;" id="discontinue-{{$line->_id}}">
	                    {{if $line->type_line == "oxygene"}}
	                      {{assign var=types value="CPrescriptionLineMix"|static:"unite_by_line"}}
	                      
	                      {{if $line->_can_modify_prescription_line_mix}}
	                       pendant {{mb_field object=$line size=2 field=duree_passage increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}
	                      {{elseif $line->duree_passage}}
	                       pendant {{mb_value object=$line field=duree_passage}}
	                      {{/if}}
	                      <!-- Duree de passage en heures -->
	                      {{mb_value object=$line field=unite_duree_passage}}
	                      
	                    {{/if}}
	                     toutes les 
	                    {{if $line->_can_modify_prescription_line_mix}}
	                      {{mb_field object=$line field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}} h
	                    {{else}}      
	                      {{mb_value object=$line field="nb_tous_les"}} h
	                    {{/if}}
	                  </span>
	              {{/if}}
	              
	              {{if $line->type_line == "aerosol"}}
								  {{if $line->_can_modify_prescription_line_mix}}
                   pendant {{mb_field object=$line size=2 field=duree_passage increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}}
                  {{elseif $line->duree_passage}}
                   pendant {{mb_value object=$line field=duree_passage}}
                  {{/if}}
                  <!-- duree de passage en minutes -->
                  {{mb_value object=$line field=unite_duree_passage}}
                  
	                toutes les 
	                {{if $line->_can_modify_prescription_line_mix}}
	                  {{mb_field object=$line field=nb_tous_les size=2 increment=1 min=0 form="editPerf-$prescription_line_mix_id" onchange="return onSubmitFormAjax(this.form);"}} h
	                {{else}}      
	                  {{mb_value object=$line field="nb_tous_les"}} h
	                {{/if}}
								
	              {{/if}}
								</div>
							 </fieldset>
						 </td>
	         </tr>
					 
					 <tr>					
			      <td>
						  <fieldset>
						  	<legend>{{mb_label object=$line field="type"}}</legend>
								{{if $line->_perm_edit}}
                {{assign var=types value="CPrescriptionLineMix"|static:"type_by_line"}}
                {{assign var=types_for_line value=$types.$type_line}}
                <select name="type" 
                        onchange="{{if $line->type_line == "perfusion"}}
                                  if(this.value == 'PCA'){ 
                                    $('bolus-{{$prescription_line_mix_id}}').show(); 
                                    changeModeBolus(this.form);
                                  } else { 
                                    resetBolus(this.form); 
                                    $('bolus-{{$prescription_line_mix_id}}').hide(); 
                                  }; 
                                  {{/if}}
                                  return onSubmitFormAjax(this.form);">
                                    
                  {{foreach from=$types_for_line item=_type}}
                    <option value="{{$_type}}" {{if $_type == $line->type}}selected="selected"{{/if}}>{{tr}}CPrescriptionLineMix.type.{{$_type}}{{/tr}}</option>  
                  {{/foreach}}
                </select>
              {{else}}
                {{mb_value object=$line field="type"}}
              {{/if}}
								
								<span id="bolus-{{$line->_id}}" style="display: none; float: right;">
									{{mb_label object=$line field="mode_bolus"}}
		              {{if $line->_perm_edit}}
		                {{mb_field object=$line field="mode_bolus" onchange="changeModeBolus(this.form); return onSubmitFormAjax(this.form);"}}
		              {{else}}
		                {{mb_value object=$line field="mode_bolus"}}
		              {{/if}}
		          
		              {{mb_label object=$line field="dose_bolus"}}
		              {{if $line->_perm_edit}}
		                {{mb_field object=$line field="dose_bolus" onchange="return onSubmitFormAjax(this.form);" size="2" increment=1 min=0 form="editPerf-$prescription_line_mix_id"}} mg
		              {{else}}
		                {{mb_value object=$line field="dose_bolus"}} mg
		              {{/if}}
		         
		              {{mb_label object=$line field="periode_interdite"}}
		              {{if $line->_perm_edit}}
		                {{mb_field object=$line field="periode_interdite" onchange="return onSubmitFormAjax(this.form);" size="2" increment=1 min=0 form="editPerf-$prescription_line_mix_id"}} min
		              {{else}}
		                {{mb_value object=$line field="periode_interdite"}} min
		              {{/if}}
								</span>	
						  </fieldset>
						</td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
	
	{{if $line->type_line == "perfusion" || $line->type_line == "aerosol"}}
  <tr>
    <td>
			<fieldset>
				<legend>Produits</legend>			
	      <table class="main layout line_mix_items" id="lines-{{$line->_id}}">
	      	{{if $line->type_line == "aerosol" && $line->_perm_edit}}
	          <!-- Formulaire d'ajout de ligne dans l'aerosol -->
						<tr>
	            <td>
	            	<script type="text/javascript">
									updateFieldsAerosol = function(selected){
									  var oFormProduit = getForm("addLineAerosol");
									  Element.cleanWhitespace(selected);
									  var dn = selected.childNodes;
									  if(dn[0].className != 'informal'){
									    $V(oFormProduit.code_cip, dn[0].firstChild.nodeValue);
									  }
									}
	            	</script>
	            	
								<form name="addLineAerosol" action="?" method="post" onsubmit="return false;">
	            		<input type="hidden" name="m" value="dPprescription" />
	                <input type="hidden" name="dosql" value="do_prescription_line_mix_item_aed" />
									<input type="hidden" name="del" value="0" />
	                <input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
	                <input type="hidden" name="prescription_line_mix_item_id" value="" />
	                
									Produit 
									<input type="text" name="produit" value="" size="20" style="width: 300px;" class="autocomplete" />
	                <div style="display:none; width: 350px;" class="autocomplete" id="aerosol_auto_complete"></div>
	   						  <input type="hidden" name="code_cip" value="" onchange="onSubmitFormAjax(this.form, { onComplete: function(){ 
									  Prescription.reloadLine('{{$line->_guid}}','{{$line->_protocole}}', '{{$mode_pharma}}', null, '{{$mode_substitution}}', {{$advanced_prot}});
									} } )" />
	                <label title="Recherche dans le livret thérapeutique">
	                  <input type="checkbox" value="1" name="_recherche_livret" {{if $prescription->type=="sejour" && $conf.dPprescription.CPrescription.preselect_livret}}checked="checked"{{/if}} onchange="if($V(getForm('addLineAerosol').produit)) { acAerosol.activate.bind(acAerosol)() };" />
	                  Livret Thérap.
	                </label>
		            </form>
	            </td>
						</tr>
					{{/if}}	
		      {{foreach from=$line->_ref_lines item=_line_item}}
		        {{assign var=line_item_id value=$_line_item->_id}}
						<tr>
		          <td>
                {{if $line->type_line == "perfusion"}}
                  <script type="text/javascript">
                    Main.add(function() {
                      updateDebitProduit('{{$line_item_id}}');
                      showHideDebitProduit(getForm("editLinePerf-{{$_line_item->_id}}").__solvant, '{{$line_item_id}}');
                    });
                  </script>
                {{/if}}
		            <form name="editLinePerf-{{$_line_item->_id}}" action="" method="post">
		              <input type="hidden" name="m" value="dPprescription" />
		              <input type="hidden" name="dosql" value="do_prescription_line_mix_item_aed" />
		              <input type="hidden" name="prescription_line_mix_item_id" value="{{$_line_item->_id}}" />
		              <input type="hidden" name="del" value="0" />
		              <table class="form layout">
			              <tr>
			                <td style="border:none" class="narrow">
			                  {{if $line->_can_delete_prescription_line_mix_item && $line->type_line != "oxygene"}}
				                  <button class="trash notext" type="button"
	                        onclick="if (Prescription.confirmDelLine('{{$_line_item->_view|smarty:nodefaults|JSAttribute}}')) {
	                          $V(this.form.del,'1'); submitFormAjax(this.form, 'systemMsg', { 
				                    onComplete: function(){
				                    	{{if @$mode_substitution}}
										  		      Prescription.viewSubstitutionLines('{{$line->variante_for_id}}','{{$line->variante_for_class}}');
										  		    {{else}}
										  			    Prescription.reloadLine('{{$line->_guid}}','{{$_line_item->_protocole}}','{{$mode_pharma}}', null, null, {{$advanced_prot}});
				                      {{/if}}
										  			}
				                  } ); }"></button>
			                  {{/if}}
			                </td>
						          <td style="width: 20%; border:none; vertical-align:middle;" class="text">
						            {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$_line_item}}
						            {{if $_line_item->_can_vw_livret_therapeutique}}
										      <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non présent dans le livret Thérapeutique" />
										    {{/if}} 
												{{if $_line_item->stupefiant}}
									        <img src="images/icons/stup.png" title="Produit stupéfiant" />
									      {{/if}} 
										    {{if !$_line_item->_ref_produit->inT2A}}
									        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
									      {{/if}}
										    {{if $_line_item->_can_vw_generique}}
										      <img src="images/icons/generiques.gif" title="Produit générique" />
										    {{/if}} 
	                      {{if $_line_item->_ref_produit->_supprime}}
	                        <img src="images/icons/medicament_barre.gif" title="Produit supprimé" />
	                      {{/if}}
						            <strong>{{$_line_item->_ucd_view}}
						                <span style="font-size: 0.8em;" class="opacity-70">({{$_line_item->_forme_galenique}})</span>
						             </strong>
						          </td>
				              <td style="border:none; width: 30%;">
						            <span style="float: right">
						             	{{if $line->_can_modify_prescription_line_mix_item && $line->type_line == "perfusion"}}
						             	{{assign var="is_mg" value=false}}
						             	{{foreach from=$_line_item->_unites_prise item=_unite}}
						             	  {{if substr($_unite, 0, 2) == "mg" || substr($_unite, 0, 2) == "µg"}}
						             	    {{assign var="is_mg" value=true}}
						             	  {{/if}}
						             	{{/foreach}}
						             	{{if $is_mg}}
						             	<button type="button" class="search calcul_debit" onclick="calculDebit('{{$line->_id}}', '{{$_line_item->_id}}')">
						             	  Debit
						             	</button>
						             	{{/if}}
													<label>
													  {{mb_field object=$_line_item field="solvant" typeEnum=checkbox 
														  onchange="removeSolvant(this.form.__solvant); showHideDebitProduit(this.form.__solvant, $line_item_id, 1); updateVolumeTotal('$prescription_line_mix_id', 1); return onSubmitFormAjax(this.form);"}} 
														Solvant
												  </label>
													{{/if}}
	                      </span>
                        <span style="float: left">
  												{{mb_label object=$_line_item field=quantite}}:
  						            {{if $line->_can_modify_prescription_line_mix_item}}
  													{{mb_field object=$_line_item field=quantite size=4 increment=1 min=0 form="editLinePerf-$line_item_id"}}
  	                        <script type="text/javascript">
                            Main.add(function() {
                            var oForm = getForm("editLinePerf-{{$_line_item->_id}}");
                            oForm.quantite.onchange = function() {
                              return onSubmitFormAjax(oForm, {onComplete: function() {
                                {{if $line->type_line == "perfusion"}}
                                  var oFormQte = getForm('editQuantiteTotale-{{$prescription_line_mix_id}}');
                                  var oFormPerf = getForm('editLinePerf-{{$_line_item->_id}}');
                                  updateVolumeTotal('{{$prescription_line_mix_id}}', 1, $V(oFormQte._quantite_totale), oFormPerf.__solvant.checked ? 1 : 0, '{{$line_item_id}}');
                                  {{foreach from=$line->_ref_lines item=_line_item_update}}
                                    if (oFormPerf.__solvant.checked) {
                                     updateDebitProduit({{$_line_item_update->_id}});
                                    }
                                  {{/foreach}}
                                {{/if}}
                              } });
                            };
                            });
                            </script>
  	                        <!-- Unite de prise -->
  														<select name="unite" style="width: 12em;"
                                onchange="return onSubmitFormAjax(this.form, {onComplete: function() {
                                  var oFormQte = getForm('editQuantiteTotale-{{$prescription_line_mix_id}}');
                                  var oFormPerf = getForm('editLinePerf-{{$_line_item->_id}}');
                                  updateVolumeTotal('{{$prescription_line_mix_id}}', 1, $V(oFormQte._quantite_totale), oFormPerf.__solvant.checked ? 1 : 0, '{{$line_item_id}}');
                                  {{foreach from=$line->_ref_lines item=_line_item_update}}
                                  updateDebitProduit('{{$_line_item_update->_id}}');
                                  {{/foreach}}
                                }});">
  														  {{if $_line_item->_ref_produit_prescription->_id}}
  															   <option value="{{$_line_item->_ref_produit_prescription->unite_prise}}">{{$_line_item->_ref_produit_prescription->unite_prise}}</option>
  															{{else}}
  														    {{foreach from=$_line_item->_unites_prise item=_unite}}
  														      <option value="{{$_unite}}" {{if $_line_item->unite == $_unite}}selected="selected"{{/if}}>{{$_unite}}</option>
  														    {{/foreach}}
  															{{/if}}
  													  </select>
  												{{else}}
  												  {{if $_line_item->quantite}}
  													  {{mb_value object=$_line_item field=quantite}}
  							              {{mb_value object=$_line_item field=unite}}	
  													{{else}}
  													  <div class="warning" style="display: inline;">Non renseignée</div>
  													{{/if}}
  													{{if $_line_item->solvant && $line->type_line == "perfusion"}}
  													  (Solvant)
  													{{/if}}
  											  {{/if}}
                        </span>
                        <span style="margin-left: 5%; display: none;" id="debit_{{$line_item_id}}" class="debit_produit">
                          Débit :
                          <span></span>
                        </span>
						     		  </td>
				            </tr>
			            </table>
		            </form>  
		          </td>
		        </tr>
		      {{foreachelse}}
						<tr>
	            <td colspan="20">
	            	<div class="small-info">Aucun produit</div>
							</td>
	          </tr>
					{{/foreach}}
          <tr>
            <td style="text-align: right;">
              <form name="editQuantiteTotale-{{$prescription_line_mix_id}}" method="get" action="?">
              <div id="volume_total_{{$line->_id}}" style="display: none;">
                <hr />
                  Volume total :
                  {{mb_field object=$line field=_quantite_totale min=0 increment=1 size=4 form="editQuantiteTotale-$prescription_line_mix_id"}} ml
              </div>
              {{if $line->type_line == "perfusion"}}
                <script type="text/javascript">
                  Main.add(function() {
                    updateVolumeTotal('{{$prescription_line_mix_id}}', 1);
                  });
                  getForm("editQuantiteTotale-{{$prescription_line_mix_id}}")._quantite_totale.onchange= function() {
                    updateSolvant('{{$prescription_line_mix_id}}', this.value);
                    {{foreach from=$line->_ref_lines item=_line_item}}
                      updateDebitProduit('{{$_line_item->_id}}');
                    {{/foreach}}
                  };
                </script>
              {{/if}}
              </form>
            </td>
          </tr>
	      </table>
		  </fieldset>
    </td>
  </tr>
	{{/if}}
	
  {{if $line->_perm_edit || $line->commentaire}}
	  <tr>
	    <td class="text">
				{{if $line->_protocole}}
	        {{assign var=_line_praticien_id value=$app->user_id}}
	      {{else}}
	        {{assign var=_line_praticien_id value=$line->praticien_id}}
	      {{/if}}
        
	      {{if $line->_perm_edit}}
  	      <script type="text/javascript">
  	        Main.add( function(){
  	          var oFormCommentaireElement = getForm("editCommentaire-{{$line->_guid}}");
  	          new AideSaisie.AutoComplete(oFormCommentaireElement.commentaire, {
  	            objectClass: "{{$line->_class}}", 
  	            contextUserId: "{{$_line_praticien_id}}",
  	            resetSearchField: false,
  	            validateOnBlur: false
  	          });
  	        })
  	      </script>
	      {{/if}}
        
	      <form name="editCommentaire-{{$line->_guid}}" method="post" action="?" onsubmit="testPharma({{$line->_id}}); return onSubmitFormAjax(this);">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
	        <input type="hidden" name="del" value="0" />
	        {{mb_key object=$line}}
	        
					<fieldset>
						<legend>
							{{mb_label object=$line field="commentaire"}}
						</legend>
	
		        {{if $line->_perm_edit}}                 
		          {{mb_field object=$line field="commentaire" onblur="this.form.onsubmit();"}}
		        {{else}}
		          {{if $line->commentaire}}
		            {{mb_value object=$line field="commentaire"}}
		          {{/if}}
		        {{/if}}
					</fieldset>
	      </form>
	    </td>
	  </tr>
  {{/if}}
	
	{{if !$line->date_pose && $line->_ref_prescription->object_id && 
       (($line->_ref_variantes.CPrescriptionLineMedicament|@count) || ($line->_ref_variantes.CPrescriptionLineMix|@count))}} 
		<tr>
			<td>
				<fieldset>
	        <form name="changeLine-{{$line->_guid}}" action="?" method="post">
	          <input type="hidden" name="m" value="dPprescription" />
	          <input type="hidden" name="dosql" value="do_substitution_line_aed" />
	          <select name="object_guid" style="width: 150px;" 
	                  onchange="onSubmitFormAjax(this.form, { onComplete: Prescription.reloadLine.curry(this.value, null, null, null, null, {{$advanced_prot}}) } )">
	            <option value="">Variantes</option>
	            {{foreach from=$line->_ref_variantes item=lines_subst_by_chap}}
	                {{foreach from=$lines_subst_by_chap item=_line_subst}}
	                <option value="{{$_line_subst->_guid}}">
	                   {{if $_line_subst->_class == "CPrescriptionLineMix"}}
	                     {{$_line_subst->_short_view}}
                       ({{$_line_subst->_frequence}})
	                   {{else}}
	                     {{$_line_subst->_view}}
	                   {{/if}}
	                   {{if !$_line_subst->variante_for_id}}(originale){{/if}}</option>
	              {{/foreach}}
	            {{/foreach}}
	          </select>
	        </form>
			  </fieldset>
			</td>
		</tr>
	{{/if}}
				
	{{if !$line->_protocole}}
		<tr>
			<td>
				<!-- Evolution -->
				{{if $line->_can_vw_form_add_perf_contigue}}
					<fieldset style="float: left; width: 48%;">
						<legend>
							Evolution
						</legend>
						
						<form name="modifyLineMix-{{$line->_id}}" action="" method="post">
			        <input type="hidden" name="m" value="dPprescription" />
			        <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
			        <input type="hidden" name="prescription_line_mix_id" value="{{$line->_id}}" />
			        <input type="hidden" name="del" value="0" />
			        <input type="hidden" name="callback" value="" />
							
				
		         <button type="button" class="new" onclick="$V(this.form.callback, 'reloadPerfEvolution'); 
		                                                  $V(this.form._add_perf_contigue, '1');
		                                                  if(document.forms.selPraticienLine){
		                                                    this.form._praticien_id.value = document.forms.selPraticienLine.praticien_id.value;
		                                                  }
		                                                  return onSubmitFormAjax(this.form);">Faire évoluer</button>
		         <input type="hidden" name="_add_perf_contigue" value="" />
		         <input type="hidden" name="_praticien_id" value="" />
		        
		         <input type="hidden" name="date_arret" value="{{$line->date_arret}}" />
		         <input type="hidden" name="time_arret" value="{{$line->time_arret}}" />
		  
		         {{if $line->date_arret}}
		           <button type="button" class="cancel" onclick="this.form.date_arret.value=''; this.form.time_arret.value=''; return onSubmitFormAjax(this.form, { onComplete: function(){ 
		             Prescription.reloadLine('{{$line->_guid}}','{{$line->_protocole}}','{{$mode_pharma}}');
		           } } );">Annuler l'arrêt</button>
		           <br />
		           {{mb_value object=$line field=date_arret}} à {{mb_value object=$line field=time_arret}}
		         {{else}}
		           <button type="button" class="stop" onclick="this.form.date_arret.value='current';this.form.time_arret.value='current'; return onSubmitFormAjax(this.form, { onComplete: function(){ 
		             Prescription.reloadLine('{{$line->_guid}}','{{$line->_protocole}}','{{$mode_pharma}}');
		           } } );">Arrêter</button>
		         {{/if}}
						 
						 </form>
		      </fieldset>
				{{/if}}
				
				<!-- Actions -->
				{{if ($line->_can_delete_prescription_line_mix || 
				    ($line->signature_prat &&  ($app->user_id == $line->praticien_id) || !$line->signature_prat) || 
						$line->_can_vw_form_signature_pharmacien) && !$advanced_prot}}
					<fieldset style="float: right; width: 48%;">
						<legend>
							Actions
						</legend>
					  {{if $line->_can_delete_prescription_line_mix}}
		          <button type="button" class="trash"
		            onclick="
		              if (Prescription.confirmDelLine('{{$line->_view}}')) {
		                $V(getForm('editPerf-{{$line->_id}}').del,'1');
		                return onSubmitFormAjax(getForm('editPerf-{{$line->_id}}'), { onComplete: function(){
		                  {{if @$mode_substitution}}
		                    Prescription.viewSubstitutionLines('{{$line->variante_for_id}}','{{$line->variante_for_class}}');
		                  {{else}}
		                    modalPrescription.close(); Prescription.reloadPrescPerf('{{$line->prescription_id}}','{{$line->_protocole}}','{{$mode_pharma}}');
		                  {{/if}}
		               }        
		            } ); }">{{tr}}Delete{{/tr}}</button>
		        {{/if}}
						
						<!-- Formulaire de signature du praticien -->
			      {{if $line->_can_vw_form_signature_praticien}}
			        <form name="validation-{{$line->_class}}-{{$line->_id}}" action="" method="post">
			          <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
			          <input type="hidden" name="m" value="dPprescription" />
			          <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
			          {{if $line->signature_prat &&  ($app->user_id == $line->praticien_id)}}
			            <!-- Annulation de la signature -->
			            <input type="hidden" name="signature_prat" value="0" />
			            <button type="button" class="cancel" onclick="onSubmitFormAjax(this.form, { onComplete: function() { Prescription.reloadLine('{{$line->_guid}}'); } });">Annuler la signature</button>
			          {{elseif !$line->signature_prat}}
			            <!-- signature --> 
                  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
			            <input type="hidden" name="signature_prat" value="1" />
			            <button type="button" class="tick" id="signature_{{$line->_id}}" 
									        onclick="
													  {{if $app->user_id != $line->praticien_id}}
				                      if(!confirm('Attention, vous etes sur le point de signer une ligne créée par un autre praticien, êtes vous sur de vouloir continuer ?')){
				                        return;
				                      }
				                    {{/if}}
													  onSubmitFormAjax(this.form, { onComplete: function(){ modalPrescription.close(); Prescription.reload.defer('{{$prescription->_id}}','','medicament'); } });">Signer</button>  
			          {{/if}}
			        </form>
			      {{/if}}
		        
		        <!-- Signature pharmacien -->
		        {{if $line->_can_vw_form_signature_pharmacien}}
		          {{if $line->signature_pharma}}
		            <button type="button" class="cancel" onclick="submitSignaturePharmacien('{{$line->_id}}','{{$line->prescription_id}}','0')">Annuler la validation pharmacien</button>
		          {{else}}
		            <button type="button" class="tick" onclick="submitSignaturePharmacien('{{$line->_id}}','{{$line->prescription_id}}','1')">Validation pharmacien</button>
		          {{/if}}
		        {{/if}}
		      </fieldset>
				{{/if}}
	    </td>   
		</tr>		
	{{/if}}			
</table>