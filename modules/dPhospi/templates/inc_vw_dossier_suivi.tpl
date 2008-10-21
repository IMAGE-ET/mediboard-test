<script type="text/javascript">

delCibleTransmission = function() {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, "");
  $V(oForm.object_id, "");
  oDiv.innerHTML = "";
}

</script>

<table class="form">
  <tr>
    <th class="title" style="width: 50%" colspan="4">
      Observations
    </th>
    <th class="title" style="width: 50%" colspan="4">
      Transmissions
    </th>
  </tr>
  <tr>
    <td colspan="4">
      {{if $isPraticien}}
      <form name="editObs" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	      <input type="hidden" name="dosql" value="do_observation_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="m" value="dPhospi" />
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="user_id" value="{{$user->_id}}" />
	      <input type="hidden" name="date" value="now" /> 
	      <div style="float: right">
		      <select name="_helpers_text" size="1" onchange="pasteHelperContent(this);">
		        <option value="">&mdash; Choisir une aide</option>
		        {{html_options options=$observation->_aides.text.no_enum}}
		      </select>
		      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CObservationMedicale', this.form.text)">{{tr}}New{{/tr}}</button><br />      
	      </div>
	      {{mb_label object=$observation field="text"}}
	      {{mb_field object=$observation field="degre"}}
	      <br />
	      {{mb_field object=$observation field="text"}}
	      <br />
	      <button type="button" class="add" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Add{{/tr}}</button> 
      </form>
      {{/if}}
    </td>     
    <td colspan="4">
      <div id="cibleTrans" style="font-style: italic;" onclick="delCibleTransmission()">
      </div>
      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_transmission_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="object_class" value="" />
      <input type="hidden" name="object_id" value="" />
      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
      <input type="hidden" name="user_id" value="{{$user->_id}}" />
      <input type="hidden" name="date" value="now" />
      <div style="float: right">
		    <select name="_helpers_text" size="1" onchange="pasteHelperContent(this);">
		      <option value="">&mdash; Choisir une aide</option>
		      {{html_options options=$transmission->_aides.text.no_enum}}
		    </select>
		    <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTransmissionMedicale', this.form.text)">{{tr}}New{{/tr}}</button><br />      
	    </div>
      {{mb_label object=$transmission field="text"}}
      {{mb_field object=$transmission field="degre"}}
      <br />
      {{mb_field object=$transmission field="text"}}
      <br />
      <button type="button" class="add" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>

<table class="tbl">
  
  <tr>
    <th>Type</th>
    <th>Utilisateur</th>
    <th>Date</th>
    <th>Heure</th>
    
    <th colspan="3">Texte</th>
  </tr>
  
  {{assign var=date value=""}}
  
  {{foreach from=$sejour->_ref_suivi_medical item=curr_suivi}}
  <tr>
  {{if $curr_suivi->_class_name == "CObservationMedicale"}}
    <td><strong>Observation</strong></td>
    <td><strong>
    <div class="mediuser" style="border-color: #{{$curr_suivi->_ref_user->_ref_function->color}};">
      {{$curr_suivi->_ref_user->_view}}
    </div>
    </strong></td>
    
      <td  style="text-align: center">
        <strong>
      {{if $date != $curr_suivi->date|date_format:"%d/%m/%Y"}}
        {{$curr_suivi->date|date_format:"%d/%m/%Y"}}
      {{else}}
        &mdash;
      {{/if}}
        </strong>
      </td>
      <td>
           {{$curr_suivi->date|date_format:"%Hh%M"}}
      </td>
    
    <td class="text" colspan="2">
      
      <div {{if $curr_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
        <strong>{{$curr_suivi->text|nl2br}}</strong>
      </div>
    </td>
    <td>
    {{if $curr_suivi->user_id == $user->_id}}
      <form name="delObs{{$curr_suivi->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_observation_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="observation_medicale_id" value="{{$curr_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$curr_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, '$prescription->_id')">{{tr}}Delete{{/tr}}</button>
      </form>
      {{/if}}
    </td>
    </tr>
  {{/if}}
  {{if $curr_suivi->_class_name == "CTransmissionMedicale"}}
  <tr>
    <td>Transmission</td>
    <td>{{$curr_suivi->_ref_user->_view}}</td>
   <td  style="text-align: center">
      {{if $date != $curr_suivi->date|date_format:"%d/%m/%Y"}}
        {{$curr_suivi->date|date_format:"%d/%m/%Y"}}
      {{else}}
        &mdash;
      {{/if}}    
      </td>
      <td>
        {{$curr_suivi->date|date_format:"%Hh%M"}}
      </td>
    <td class="text" colspan="2">
      
      <div {{if $curr_suivi->degre == "high"}}style="background-color: #faa"{{/if}}>
	      {{if $curr_suivi->object_id}}
	      <em>Cible : {{$curr_suivi->_ref_object->_view}}</em><br />
	      {{/if}}
        {{$curr_suivi->text|nl2br}}
      </div>
    </td>
    <td class="button">
    {{if $curr_suivi->user_id == $user->_id}}
      <form name="delTrans{{$curr_suivi->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_transmission_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="transmission_medicale_id" value="{{$curr_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$curr_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Delete{{/tr}}</button>
      </form>
      {{/if}}
    </td>
    </tr>
  {{/if}}
  {{assign var=date value=$curr_suivi->date|date_format:"%d/%m/%Y"}}
  {{/foreach}}
</table>