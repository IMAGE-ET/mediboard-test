<tbody id="perfusion-{{$_perfusion->_id}}" class="hoverable {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}line_stopped{{/if}}">
{{assign var=perfusion_id value=$_perfusion->_id}}
  <tr>
    <th colspan="8" id="th-perf-{{$_perfusion->_id}}" class="text element {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}arretee{{/if}}">
      {{if $_perfusion->_count_parent_line}}
	      <div style="float: left">
	        <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique"/>
	      </div>
      {{/if}}
      
      <!-- Formulaire de signature du praticien -->
      {{if $_perfusion->_can_vw_form_signature_praticien}}
			  <div style="float: right">
					{{if $_perfusion->signature_prat}}
					  <button type="button" class="cancel" onclick="submitSignaturePraticien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','0')">Annuler la signature</button>
					{{else}}
					  <button type="button" class="tick" onclick="submitSignaturePraticien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','1')">Signer</button>
					{{/if}}
				</div>
      {{/if}}
      
      <!--  Validation infirmiere -->
		  {{if $_perfusion->_can_vw_form_signature_infirmiere}}
        <div style="float: right">
					{{if $_perfusion->validation_infir}}
					  (Validée par l'infirmiere)
					{{else}}
					  <button type="button" class="tick" onclick="submitValidationInfir('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','1')">Validation infirmière</button>
					{{/if}}
				</div>
		  {{/if}}
		  
		  <!-- Signature pharmacien -->
      {{if $_perfusion->_can_vw_form_signature_pharmacien}}
        <div style="float: right">
					{{if $_perfusion->signature_pharma}}
					  <button type="button" class="cancel" onclick="submitSignaturePharmacien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','0')">Annuler la validation pharmacien</button>
					{{else}}
					  <button type="button" class="tick" onclick="submitSignaturePharmacien('{{$_perfusion->_id}}','{{$_perfusion->prescription_id}}','1')">Validation pharmacien</button>
					{{/if}}
				</div>
		  {{/if}}
		  
		  <!-- Accord du praticien -->
			{{if $mode_pharma}}
				<div style="float: right">
					{{if !$_perfusion->signature_pharma}}
						<form action="?" method="post" name="editLineAccordPraticien-{{$_perfusion->_id}}">
						  <input type="hidden" name="m" value="dPprescription" />
						  <input type="hidden" name="dosql" value="do_perfusion_aed" />
						  <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
						  <input type="hidden" name="del" value="0" />
						  {{mb_field object=$_perfusion field="accord_praticien" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
						  {{mb_label object=$_perfusion field="accord_praticien"}}
						</form> 
					{{elseif $_perfusion->accord_praticien}}
					  En accord avec le praticien
					{{/if}}
				</div>
			{{/if}}
			
      <!-- Siganture du praticien -->
      {{if $_perfusion->_can_vw_signature_praticien}}
        <div style="float: right">
          {{$_perfusion->_ref_praticien->_view}}
					{{if $_perfusion->signature_prat}}
					   <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" />
					{{else}}
					   <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" />
					{{/if}}
        </div>
      {{/if}}
      
			<strong>
				Perfusion :
				{{foreach from=$_perfusion->_ref_lines item=_line name=perf_line}}
				 {{$_line->_ucd_view}}{{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
    </th>
  </tr>
  <tr>
    <td colspan="8">
      <form name="editPerf-{{$_perfusion->_id}}" action="" method="post">
	      <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_perfusion_aed" />
        <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
        <input type="hidden" name="del" value="0" />
        <table class="form">
          <tr>
            <td style="border:none;">
              {{if $_perfusion->_can_delete_perfusion}}
	              <button type="button" class="trash notext" onclick="$V(this.form.del,'1'); return onSubmitFormAjax(this.form, { 
	                onComplete: function(){
			              Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
			            }        
	              } );"></button>
              {{/if}}
            </td>
            <td style="border:none;">
			        {{mb_label object=$_perfusion field="type"}} : 
			        {{if $_perfusion->_ref_lines|@count || !$_perfusion->_can_modify_perfusion}}
				        {{mb_value object=$_perfusion field="type"}}
				      {{else}}
				        {{mb_field object=$_perfusion field="type" onchange="return onSubmitFormAjax(this.form);"}}
				      {{/if}}
				    </td>
				    <td style="border:none;">
				      {{mb_label object=$_perfusion field="vitesse"}}
				      {{if $_perfusion->_can_modify_perfusion}}
				        {{mb_field object=$_perfusion field="vitesse" size="3" increment=1 min=0 form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}} ml/h
				      {{else}}
				        {{mb_value object=$_perfusion field="vitesse"}} ml/h
				      {{/if}}
				    </td>
				    <td style="border:none;">
	            <strong>{{mb_value object=$_perfusion field="voie"}}</strong>
            </td>

	          
            {{if $_perfusion->_protocole}}
						  <td style="border:none;">
						  {{mb_label object=$_perfusion field="date_debut"}}
						    à I {{mb_field object=$_perfusion field=decalage_interv showPlus="1" increment=1 size="2" form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}} h
						  </td>
						{{else}}
						  <td style="border:none;">
	              {{mb_label object=$_perfusion field="date_debut"}}
	            </td>
	        		<td class="date"  style="border:none;">
	        		  {{if $_perfusion->_can_modify_perfusion}}
			            {{mb_field object=$_perfusion field=date_debut form="editPerf-$perfusion_id" onchange="changeColorPerf($perfusion_id,this.form); return onSubmitFormAjax(this.form);"}}
			            {{mb_field object=$_perfusion field=time_debut form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}}
		            {{else}}
		              {{mb_value object=$_perfusion field=date_debut}}
			            {{mb_value object=$_perfusion field=time_debut}}
		            {{/if}}
		            <script type="text/javascript">
									Main.add( function(){
									  prepareForm('editPerf-{{$perfusion_id}}'); 
									  Calendar.regField("editPerf-{{$perfusion_id}}", "date_debut", false);
									});
								</script>			
						  </td>				
						{{/if}}

            <td style="border:none;">
					   {{mb_label object=$_perfusion field=duree}}
					   {{if $_perfusion->_can_modify_perfusion}}
					     {{mb_field object=$_perfusion field=duree size=1 increment=1 min=0 form="editPerf-$perfusion_id" onchange="return onSubmitFormAjax(this.form);"}}heures
				     {{else}}
				       {{mb_value object=$_perfusion field=duree}}heures
				     {{/if}}
				    </td>
				    <td style="border:none;">
				      <!-- Modification de la ligne -->
				      {{if $_perfusion->_can_vw_form_add_perf_contigue}}
				        <button type="button" class="new" onclick="$V(this.form._add_perf_contigue, '1');
				                                                      return onSubmitFormAjax(this.form, { onComplete: function(){ 
				            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
				          } } );">Modifier</button>
				        <input type="hidden" name="_add_perf_contigue" value="" />
				        
				      {{/if}}
							<input type="hidden" name="date_arret" value="{{$_perfusion->date_arret}}" />
							<input type="hidden" name="time_arret" value="{{$_perfusion->time_arret}}" />
						  <!-- Arret de ligne -->
				      {{if $_perfusion->_can_vw_form_stop_perf}}
				        {{if $_perfusion->date_arret}}
				          
				          <button type="button" class="cancel" onclick="this.form.date_arret.value=''; this.form.time_arret.value=''; return onSubmitFormAjax(this.form, { onComplete: function(){ 
				            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
				          } } );">Annuler l'arrêt</button>
				          <br />
				          {{mb_value object=$_perfusion field=date_arret}} à {{mb_value object=$_perfusion field=time_arret}}
				        {{else}}
				          <button type="button" class="stop" onclick="this.form.date_arret.value='current';this.form.time_arret.value='current'; return onSubmitFormAjax(this.form, { onComplete: function(){ 
				            Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
				          } } );">Arrêter</button>
				        {{/if}}
				      {{/if}}
				    </td>       
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="8">
      <table class="form">
	      {{foreach from=$_perfusion->_ref_lines item=line}}
	        {{assign var=line_id value=$line->_id}}
	        <tr>
	          <td style="border:none;">
	            <form name="editLinePerf-{{$line->_id}}" action="" method="post">
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="dosql" value="do_perfusion_line_aed" />
	              <input type="hidden" name="perfusion_line_id" value="{{$line->_id}}" />
	              <input type="hidden" name="del" value="0" />
	              <table class="form">
		              <tr>
		                
		                <td style="border:none; width:1%;">
		                  {{if $_perfusion->_can_delete_perfusion_line}}
			                  <button class="trash notext" type="button" onclick="$V(this.form.del,'1'); submitFormAjax(this.form, 'systemMsg', { 
			                    onComplete: function(){
			                      Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$line->_protocole}}','{{$mode_pharma}}');
			                    }
			                  } );"></button>
		                  {{/if}}
		                </td>
					          	 
					          <td style="width: 30%; border:none; vertical-align:middle;" class="text">
					            {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
					            {{if $line->_can_vw_livret_therapeutique}}
									      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
									    {{/if}}  
									    {{if $line->_can_vw_generique}}
									      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
									    {{/if}} 
                      {{if $line->_ref_produit->_supprime}}
                        <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
                      {{/if}}
					            <strong>{{$line->_ucd_view}}</strong>
					          </td>
			              <td style="border:none; width: 20%;">
					            {{mb_label object=$line field=quantite}}
					            {{if $_perfusion->_can_modify_perfusion_line}}
					              {{mb_field object=$line field=quantite size=4 increment=1 min=0 form="editLinePerf-$line_id" onchange="return onSubmitFormAjax(this.form);"}}
						            <select name="unite" style="width: 75px;" onchange="return onSubmitFormAjax(this.form);">
											    {{foreach from=$line->_unites_prise item=_unite}}
											      <option value="{{$_unite}}" {{if $line->unite == $_unite}}selected="selected"{{/if}}>{{$_unite}}</option>
											    {{/foreach}}
											  </select>
					            {{else}}
					              {{mb_value object=$line field=quantite}}
					              {{mb_value object=$line field=unite}}					            
										  {{/if}}
					     		  </td>
					     		  {{if !$line->_protocole}}
							        <td  style="border:none;  vertical-align:middle; width: 1%">
							          {{mb_label object=$line field=date_debut}} 
						          </td>
						          <td class="date"  style="border:none; width: 20%">
						            {{if $_perfusion->_can_modify_perfusion_line}}
							            {{mb_field object=$line field=date_debut form="editLinePerf-$line_id" onchange="return onSubmitFormAjax(this.form);"}}
							            {{mb_field object=$line field=time_debut form="editLinePerf-$line_id" onchange="return onSubmitFormAjax(this.form);"}}
							          {{else}}
							            {{mb_value object=$line field=date_debut}}
							            {{mb_value object=$line field=time_debut}}
						            {{/if}}
						            <script type="text/javascript">
													Main.add( function(){
													  prepareForm('editLinePerf-{{$line->_id}}'); 
													  Calendar.regField("editLinePerf-{{$line->_id}}", "date_debut", false);
													});
												</script>
						          </td>
					          {{/if}}
			            </tr>
		            </table>
	            </form>  
	          </td>
	        </tr>
	      {{foreachelse}}
		      <div class="small-info">
		        Aucun produit n'est associé à la perfusion
		      </div>
	      {{/foreach}}
      </table>
    </td>
  </tr>
</tbody>