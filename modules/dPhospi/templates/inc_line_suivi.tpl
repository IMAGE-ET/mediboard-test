{{if $_suivi->_class_name == "CObservationMedicale"}}
<tr>
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
      <strong>{{mb_value object=$_suivi field=text}}</strong>
    </div>
  </td>
  {{if !$without_del_form}}
  <td class="button">
  {{if $_suivi->user_id == $app->user_id}}
    <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_observation_aed" />
      <input type="hidden" name="del" value="1" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="observation_medicale_id" value="{{$_suivi->_id}}" />
      <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
      <button type="button" class="trash notext" onclick="submitSuivi(this.form, '$prescription->_id')">{{tr}}Delete{{/tr}}</button>
    </form>
    {{/if}}
  </td>
  {{/if}}
</tr>
{{/if}}

{{if $_suivi->_class_name == "CTransmissionMedicale"}}
<tr class="{{$_suivi->_cible}}">
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
  <td class="text {{if $_suivi->type}}trans-{{$_suivi->type}}{{/if}}" {{if $_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
    {{mb_value object=$_suivi field=text}}
  </td>
  
  <td class="button" style="width: 1%">
	  {{if !$without_del_form}}
		  {{if $_suivi->user_id == $app->user_id}}
	     <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		     <input type="hidden" name="dosql" value="do_transmission_aed" />
		     <input type="hidden" name="del" value="1" />
		     <input type="hidden" name="m" value="dPhospi" />
		     <input type="hidden" name="transmission_medicale_id" value="{{$_suivi->_id}}" />
		     <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
		     <button type="button" class="trash notext" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Delete{{/tr}}</button>
	     </form>
	    {{/if}}
		{{/if}}
  </td>
</tr>
{{/if}}