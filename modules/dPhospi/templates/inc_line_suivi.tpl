{{if !@$line_guid}}
  {{assign var=line_guid value=""}}
{{/if}}
{{if $_suivi instanceof CObservationMedicale}}
  {{if @$show_patient}}
  <td><strong>{{$_suivi->_ref_sejour->_ref_patient}}</strong></td>
  <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td><strong>{{tr}}{{$_suivi->_class_name}}{{/tr}}</strong></td>
  <td>
    <strong>
      <div class="mediuser" style="border-color: #{{$_suivi->_ref_user->_ref_function->color}};">
        {{$_suivi->_ref_user}}
      </div>
    </strong>
  </td>
  <td  style="text-align: center">
    <strong>
      {{mb_ditto name=date value=$_suivi->date|date_format:$dPconfig.date}}
    </strong>
  </td>
  <td>{{$_suivi->date|date_format:$dPconfig.time}}</td>
  <td class="text" colspan="2"
    {{if $_suivi->degre == "high"}}style="background-color: #faa"{{/if}} 
    {{if $_suivi->degre == "info"}}style="background-color: #aaf"{{/if}}>
    <div>
      {{if $line_guid == $_suivi->_guid && $action == "show" && $_suivi->_canEdit}}
       <form name="editObsSuiviSoins" action="?" method="post">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_observation_aed" />
        <input type="hidden" name="observation_medicale_id" value="{{$_suivi->_id}}" />
        {{mb_field object=$_suivi field="text" onchange="return onSubmitFormAjax(this.form);" size="20"}}
      </form> 
	    {{else}}
	      <strong>{{mb_value object=$_suivi field=text}}</strong>
	    {{/if}}
    </div>
  </td>
  
  <td class="button">
	  {{if !$without_del_form && $_suivi->_canEdit}}
      <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_observation_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="observation_medicale_id" value="{{$_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
     
		  {{if $line_guid == $_suivi->_guid && $action == "show"}}
	      <button type="button" class="lock notext" onclick="refreshLineSuivi('{{$_suivi->_guid}}','hide');"></button>
	    {{else}}
				<button type="button" class="edit notext" onclick="refreshLineSuivi('{{$_suivi->_guid}}','show');"></button>
			{{/if}}
		{{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CTransmissionMedicale}}
  {{if @$show_patient}}
    <td>{{$_suivi->_ref_sejour->_ref_patient}}</td>
    <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td style="width: 1%;">{{tr}}{{$_suivi->_class_name}}{{/tr}}</td>
  <td style="width: 1%;">{{$_suivi->_ref_user}}</td>
  <td style="width: 1%; text-align: center;">
    {{mb_ditto name=date value=$_suivi->date|date_format:$dPconfig.date}}
  </td>
  <td style="width: 1%;">{{$_suivi->date|date_format:$dPconfig.time}}</td>
  <td class="text" style="height: 22px;">
	  {{if $_suivi->object_id && $_suivi->object_class}}
	    <a href="#1" onclick="if($('cibleTrans')){ $('cibleTrans').update('{{$_suivi->_ref_object}}'); $V(document.forms.editTrans.object_id, '{{$_suivi->object_id}}'); 
	    											$V(document.forms.editTrans.object_class, '{{$_suivi->object_class}}'); }">
	    	{{$_suivi->_ref_object->_view}}
				
			  {{if $_suivi->_ref_object->commentaire && $_suivi->_ref_object instanceof CPrescriptionLineElement}}
				  ({{$_suivi->_ref_object->commentaire}})
				{{/if}}

	    	{{if $_suivi->object_class == "CPrescriptionLineMedicament"}}
	    	<br />
	    	[{{$_suivi->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
	    	{{/if}}
	    	
	    	{{if $_suivi->object_class == "CPrescriptionLineElement"}}
	    	<br />
	    	[{{$_suivi->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
	    	{{/if}}
	    	
	    	{{if $_suivi->object_class == "CAdministration"}}
	    	  {{if $_suivi->_ref_object->object_class == "CPrescriptionLineMedicament"}}
	    	    <br />
	    	    [{{$_suivi->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
	    	  {{/if}}
	    	  
	    	  {{if $_suivi->_ref_object->object_class == "CPrescriptionLineElement"}}
	    	    <br />
	    	    [{{$_suivi->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
	    	  {{/if}}
	    	{{/if}}
	    	
	    </a>
	  {{/if}}
	  {{if $_suivi->libelle_ATC}}
	    <a href="#1" onclick="if($('cibleTrans')){ $('cibleTrans').update('{{$_suivi->libelle_ATC}}'); $V(document.forms.editTrans.libelle_ATC, '{{$_suivi->libelle_ATC}}'); }">{{$_suivi->libelle_ATC}}</a>
	  {{/if}}
  </td>
  <td class="text {{if $_suivi->type}}trans-{{$_suivi->type}}{{/if}} libelle_trans" {{if $_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
    
		{{if $line_guid == $_suivi->_guid && $action == "show" && $_suivi->_canEdit}}
		   <form name="editTransSuiviSoins" action="?" method="post">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_transmission_aed" />
        <input type="hidden" name="transmission_medicale_id" value="{{$_suivi->_id}}" />
        {{mb_field object=$_suivi field="text" onchange="return onSubmitFormAjax(this.form);" size="20"}}
      </form> 
		{{else}}
		  {{mb_value object=$_suivi field=text}}
		{{/if}}
  </td>
  
  <td class="button" style="width: 1%; white-space: nowrap;">
    {{if !$without_del_form && $_suivi->_canEdit}}
       <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
         <input type="hidden" name="dosql" value="do_transmission_aed" />
         <input type="hidden" name="del" value="1" />
         <input type="hidden" name="m" value="dPhospi" />
         <input type="hidden" name="transmission_medicale_id" value="{{$_suivi->_id}}" />
         <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
         <button type="button" class="trash notext" onclick="submitSuivi(this.form)">{{tr}}Delete{{/tr}}</button>
       </form>

		  {{if $line_guid == $_suivi->_guid && $action == "show"}}
        <button type="button" class="lock notext" onclick="refreshLineSuivi('{{$_suivi->_guid}}','hide');"></button>
      {{else}}
				<button type="button" class="edit notext" onclick="refreshLineSuivi('{{$_suivi->_guid}}','show');"></button>
			{{/if}}	
		{{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CConstantesMedicales}}
  <td>{{tr}}{{$_suivi->_class_name}}{{/tr}}</td>
  <td>
    {{$_suivi->_ref_user}}
  </td>
  <td  style="text-align: center">
     {{mb_ditto name=datetime value=$_suivi->datetime|date_format:$dPconfig.date}}
  </td>
  <td>{{$_suivi->datetime|date_format:$dPconfig.time}}</td>
  <td colspan="2">
    {{foreach from=$params key=_key item=_field name="const"}}
      {{if $_suivi->$_key != null && $_key|substr:0:1 != "_"}}
        {{$_key}}: {{$_suivi->$_key}} ({{$_field.unit}}), &nbsp;
      {{/if}}
    {{/foreach}}
  </td>
  <td></td>
{{/if}}

{{if $_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment}}
  <td>Prescription</td>
	<td>{{mb_value object=$_suivi field="praticien_id"}}</td>
  <td>{{mb_value object=$_suivi field="debut"}}</td>
	<td>{{mb_value object=$_suivi field="time_debut"}}</td>
	<td></td>
  <td>
  	{{if !($line_guid == $_suivi->_guid && $action == "show")}}
		  <button type="button" class="tick" onclick="addTransmissionAdm('{{$_suivi->_id}}','{{$_suivi->_class_name}}');" style="float: right;">Réaliser ({{$_suivi->_count.transmissions}})</button>
		{{/if}}
		
		{{if $_suivi instanceof CPrescriptionLineElement}}
		<strong>{{$_suivi->_view}}</strong>
		{{/if}}
		{{if $line_guid == $_suivi->_guid && $action == "show" && $_suivi->_canEdit}}
		  <br />
			{{if $_suivi instanceof CPrescriptionLineElement}}
		  <form name="editLinePrescriptionSuiviSoins" action="?" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="prescription_line_element_id" value="{{$_suivi->_id}}" />
        {{mb_field object=$_suivi field="commentaire" onchange="return onSubmitFormAjax(this.form);" size="50"}}
      </form>	
			{{else}}
			  <form name="editLinePrescriptionSuiviSoins" action="?" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_suivi->_id}}" />
          {{mb_field object=$_suivi field="commentaire" onchange="return onSubmitFormAjax(this.form);" size="50"}}
      </form> 
			{{/if}}
    {{else}}
      {{mb_value object=$_suivi field="commentaire"}}
		{{/if}}
	</td>
  <td>
    {{if !$without_del_form && $_suivi->_canEdit}}
			{{if $_suivi instanceof CPrescriptionLineElement}}
	      <form name="removeLine" action="?" method="post">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="prescription_line_element_id" value="{{$_suivi->_id}}" />
	        <input type="hidden" name="sejour_id" value="{{$_suivi->_ref_prescription->object_id}}" />
	        <button type="button" class="trash notext" onclick="submitSuivi(this.form);"></button>
	      </form>
	    {{else}}
	      <form name="removeLine" action="?" method="post">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="prescription_line_comment_id" value="{{$_suivi->_id}}" />
	        <input type="hidden" name="sejour_id" value="{{$_suivi->_ref_prescription->object_id}}" />
	        <button type="button" class="trash notext" onclick="submitSuivi(this.form);"></button>
	      </form>
	    {{/if}}
			
	    {{if $line_guid == $_suivi->_guid && $action == "show"}}
				<button type="button" class="lock notext" onclick="refreshLineSuivi('{{$_suivi->_guid}}','hide');">{{tr}}Edit{{/tr}}</button>
			{{else}}
	  	  <button type="button" class="edit notext" onclick="refreshLineSuivi('{{$_suivi->_guid}}','show');">{{tr}}Edit{{/tr}}</button>
			{{/if}}
		{{/if}}
    
  </td>
{{/if}}