{{if $_suivi instanceof CObservationMedicale}}
  {{if @$show_patient}}
  <td><strong>{{$_suivi->_ref_sejour->_ref_patient}}</strong></td>
  <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td><strong>{{tr}}{{$_suivi->_class}}{{/tr}}</strong></td>
  <td>
    <strong>
      <div class="mediuser" style="border-color: #{{$_suivi->_ref_user->_ref_function->color}};">
        {{$_suivi->_ref_user}}
      </div>
    </strong>
  </td>
  <td  style="text-align: center">
    <strong>
      {{mb_ditto name=date value=$_suivi->date|date_format:$conf.date}}
    </strong>
  </td>
  <td>{{$_suivi->date|date_format:$conf.time}}</td>
  <td class="text" colspan="2"
    {{if $_suivi->degre == "high"}} style="background-color: #faa" {{/if}} 
    {{if $_suivi->degre == "info"}} style="background-color: #aaf" {{/if}}>
    <div>
	    <strong>{{mb_value object=$_suivi field=text}}</strong>
    </div>
  </td>
	<td>
  	{{if !$readonly && $_suivi->_canEdit}}
      <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_observation_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="observation_medicale_id" value="{{$_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, 1)">{{tr}}Delete{{/tr}}</button>
      </form>
  	  <button type="button" class="edit notext" onclick="addObservation(null, null, '{{$_suivi->_id}}');"></button>
    {{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CTransmissionMedicale}}
  {{if @$show_patient}}
    <td>{{$_suivi->_ref_sejour->_ref_patient}}</td>
    <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td class="narrow">{{tr}}{{$_suivi->_class}}{{/tr}}</td>
  <td class="narrow">{{$_suivi->_ref_user}}</td>
  <td style="text-align: center;" class="narrow">
    {{mb_ditto name=date value=$_suivi->date|date_format:$conf.date}}
  </td>
  <td class="narrow">{{$_suivi->date|date_format:$conf.time}}</td>
  <td class="text" style="height: 22px;">
	  {{if $_suivi->object_id && $_suivi->object_class}}
      {{assign var=classes value=' '|explode:"CPrescriptionLineMedicament CPrescriptionLineElement CAdministration CPrescriptionLineMix"}}
      {{if in_array($_suivi->object_class, $classes)}}
        <span
         title="{{$_suivi->_ref_object->_view}} {{if $_suivi->_ref_object instanceof CPrescriptionLineElement && $_suivi->_ref_object->commentaire}}({{$_suivi->_ref_object->commentaire}}){{/if}}"
          style="float: left; border: 2px solid #800; width: 5px; height: 11px; margin-right: 3px;">
        </span>
      {{/if}}
      {{if !$readonly && $_suivi->_canEdit}}
	      <a href="#1" onclick="if (window.addTransmission) { addTransmission('{{$_suivi->sejour_id}}', '{{$app->user_id}}', null, '{{$_suivi->object_id}}', '{{$_suivi->object_class}}'); }">
      {{/if}}
        {{if !in_array($_suivi->object_class, $classes)}}
          {{$_suivi->_ref_object->_view}}
        {{/if}}
	    	{{if $_suivi->object_class == "CPrescriptionLineMedicament"}}
	    	[{{$_suivi->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
	    	{{/if}}
	    	
	    	{{if $_suivi->object_class == "CPrescriptionLineElement"}}
	    	[{{$_suivi->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
	    	{{/if}}
	    	
	    	{{if $_suivi->object_class == "CAdministration"}}
	    	  {{if $_suivi->_ref_object->object_class == "CPrescriptionLineMedicament"}}
	    	    [{{$_suivi->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
	    	  {{/if}}
	    	  
	    	  {{if $_suivi->_ref_object->object_class == "CPrescriptionLineElement"}}
	    	    [{{$_suivi->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
	    	  {{/if}}
	    	{{/if}}
	    {{if !$readonly && $_suivi->_canEdit}}
	      </a>
      {{/if}}
	  {{/if}}
	  {{if $_suivi->libelle_ATC}}
	    <a href="#1" onclick="if (window.addTransmission) { addTransmission('{{$_suivi->sejour_id}}', '{{$_suivi->user_id}}', null, null, null, '{{$_suivi->libelle_ATC|smarty:nodefaults|JSAttribute}}'); }">{{$_suivi->libelle_ATC}}</a>
	  {{/if}}
  </td>
  <td class="text {{if $_suivi->type}}trans-{{$_suivi->type}}{{/if}} libelle_trans" {{if $_suivi->degre == "high"}} style="background-color: #faa" {{/if}}>
		{{mb_value object=$_suivi field=text}}
  </td>
  
  <td style="white-space: nowrap;">
    {{if !$readonly && $_suivi->_canEdit}}
			<form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
				<input type="hidden" name="dosql" value="do_transmission_aed" />
				<input type="hidden" name="del" value="1" />
				<input type="hidden" name="m" value="dPhospi" />
				<input type="hidden" name="transmission_medicale_id" value="{{$_suivi->_id}}" />
				<input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
				<button type="button" class="trash notext" onclick="submitSuivi(this.form, 1)">{{tr}}Delete{{/tr}}</button>
			</form>
		  <button type="button" class="edit notext" onclick="addTransmission(null, null, '{{$_suivi->_id}}', null, null, null, 1)"></button>
		{{/if}}
	</td>
  
{{/if}}

{{if $_suivi instanceof CConstantesMedicales}}
  <td>{{tr}}{{$_suivi->_class}}{{/tr}}</td>
  <td>
    {{$_suivi->_ref_user->_view}}
  </td>
  <td style="text-align: center">
     {{mb_ditto name=date value=$_suivi->datetime|date_format:$conf.date}}
  </td>
  <td>{{$_suivi->datetime|date_format:$conf.time}}</td>
  <td colspan="2" class="text">
    {{foreach from=$params key=_key item=_field name="const"}}
      {{if $_suivi->$_key != null && $_key|substr:0:1 != "_"}}
        {{mb_title object=$_suivi field=$_key}} :
        {{if array_key_exists("formfields", $_field)}}
          {{mb_value object=$_suivi field=$_field.formfields.0 size="2" }} / 
          {{mb_value object=$_suivi field=$_field.formfields.1 size="2" }}
        {{else}}
          {{mb_value object=$_suivi field=$_key}}
        {{/if}} {{$_field.unit}},
      {{/if}}
    {{/foreach}}
  </td>
  <td></td>
{{/if}}

{{if $_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment}}
  <td><strong>Prescription</strong></td>
	<td>
		<strong>
      <div class="mediuser" style="border-color: #{{$_suivi->_ref_praticien->_ref_function->color}};">
        {{mb_value object=$_suivi field="praticien_id"}}
      </div>
    </strong>
	</td>
  <td style="text-align: center">
  	{{mb_ditto name=date value=$_suivi->debut|date_format:$conf.date}}
	</td>
	<td>{{mb_value object=$_suivi field="time_debut"}}</td>
  <td colspan="2">
  	{{if !$readonly}}
		  <button type="button" class="tick" onclick="addTransmissionAdm('{{$_suivi->_id}}','{{$_suivi->_class}}');" style="float: right;">Réaliser ({{$_suivi->_count.transmissions}})</button>
		{{/if}}
		
		{{if $_suivi instanceof CPrescriptionLineElement}}
		<strong>{{$_suivi->_view}}</strong>
		{{/if}}
    {{mb_value object=$_suivi field="commentaire"}}
	</td>
	<td>
    {{if !$readonly && $_suivi->_canEdit}}
      <form name="Del-{{$_suivi->_guid}}" action="?" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        {{if $_suivi instanceof CPrescriptionLineElement}}
          <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        {{else}}
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
        {{/if}}
        <input type="hidden" name="del" value="1" />
        {{mb_key object=$_suivi}}
        <input type="hidden" name="sejour_id" value="{{$_suivi->_ref_prescription->object_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, 1);"></button>
      </form>
      <button type="button" class="edit notext"
        onclick="addPrescription('{{$_suivi->_ref_prescription->object_id}}', '{{$app->user_id}}', '{{$_suivi->_id}}', '{{$_suivi->_class}}');">{{tr}}Edit{{/tr}}</button>
    {{/if}}
    </td>
  {{/if}}