{{mb_default var=hide_cible value=0}}
{{mb_default var=hide_button_add value=0}}
{{mb_default var=update_plan_soin value=0}}
{{assign var=hour_quantum value=$conf.dPhospi.nb_hours_trans}}
{{assign var=cible_mandatory_trans value=$conf.soins.cible_mandatory_trans}}
{{mb_default var=data_id value=""}}
{{mb_default var=action_id value=""}}
{{mb_default var=result_id value=""}}

<script type="text/javascript">
updateListTransmissions = function(data, object_class) {
  var url = new Url("dPhospi", "ajax_list_transmissions_short");
  url.addParam("sejour_id", "{{$transmission->sejour_id}}");
  if (isNaN(data)) {
    url.addParam("libelle_ATC", data);
  }
  else {
    url.addParam("object_id"   , data);
    url.addParam("object_class", object_class);
  }
  url.requestUpdate("list_transmissions");
}

Main.add(function() {
  var oFormTrans = getForm("editTrans");
  {{if !$hide_cible}}  
    var url = new Url("dPprescription", "httpreq_cible_autocomplete");
    var autocompleter = url.autoComplete(oFormTrans.cible, "cible_auto_complete", {
      minChars: 3,
      dropdown: 1,
      updateElement: function(selected) {
        var oForm = document.forms['editTrans'];
        Element.cleanWhitespace(selected);
        var data = selected.get("data");
        if(isNaN(data)){
          $V(oForm.libelle_ATC, data);
          updateListTransmissions(data);
        } else {
          $V(oForm.object_id, data);
          $V(oForm.object_class, 'CCategoryPrescription');
          updateListTransmissions(data, 'CCategoryPrescription');
        }
        var view = $(selected).down(".view").innerHTML.split(' : ')[1];
        $V(oFormTrans.cible, view);
      }
    } );
  {{/if}}

  var options = {
      minHours: '{{$hour-$hour_quantum}}',
      maxHours: '{{$hour+$hour_quantum}}'
    };
    
  var dates = {};
  dates.limit = {
    start: '{{$date}}',
    stop: '{{$date}}'
  };
  Calendar.regField(oFormTrans.date, dates, options);

  {{if !$transmission->_id && !$data_id && !$result_id && !$action_id}}
    //Initialisation du champ dates
    oFormTrans.date_da.value = "Heure actuelle";
    $V(oFormTrans.date, "now");
  {{/if}}

 
  {{if $transmission->object_id}}
    updateListTransmissions('{{$transmission->object_id}}', '{{$transmission->object_class}}');
  {{elseif $transmission->libelle_ATC}}
    updateListTransmissions('{{$transmission->libelle_ATC|smarty:nodefaults|JSAttribute}}');
  {{/if}}
  
  toggleDateMax();
});

updateCible = function(elt) {
  if (elt.value == '') {
    $V(elt.form.object_id, '');
    $V(elt.form.object_class, '')
    $V(elt.form.libelle_ATC, '');
    updateListTransmissions(elt.value);
  }
}

submitTrans = function(form) {
  {{if $cible_mandatory_trans}}
    if (!$V(form.libelle_ATC) && !$V(form.object_class) && !$V(form.object_id)) {
      alert("{{tr}}CTransmissionMedicale.cible_mandatory_trans{{/tr}}");
      return; 
    }
  {{/if}}
  {{if $refreshTrans || $update_plan_soin}}
    submitFormAjax(form, 'systemMsg', {onComplete: function() {
      {{if $refreshTrans}}
      loadSuivi('{{$transmission->sejour_id}}');
      Control.Modal.close();
      updateNbTrans('{{$transmission->sejour_id}}')
      {{else}}
        Control.Modal.close();
        updatePlanSoinsPatients();
      {{/if}}
    } });
  {{else}}
    if (window.submitSuivi) {
      submitSuivi(form);
    }
    else if (window.submitTransmissions) {
      submitTransmissions();
    }
  {{/if}}
}

completeTrans = function(type, button){
  var oFormTrans = getForm("editTrans");
  var fieldName = "_text_"+type;
  var oField = oFormTrans.elements["_text_"+type];
	var text = button.get("text");
  $V(oField, $V(oField) ? $V(oField)+"\n"+text : text);
}

toggleDateMax = function() {
  var oForm = getForm("editTrans");
  if ($V(oForm.degre) == "high") {
    $('date-max-{{$transmission->sejour_id}}').show()
  } else {
    $('date-max-{{$transmission->sejour_id}}').hide()
    $V(oForm.date_max, '');
  }
}

</script>

<form name="editTrans" action="?" method="post" onsubmit="return checkForm(this)" style="text-align: left;">
  <input type="hidden" name="m" value="dPhospi" />
  {{if $transmission->_id}}
    <input type="hidden" name="dosql" value="do_transmission_aed" />
  {{else}}
    <input type="hidden" name="dosql" value="do_multi_transmission_aed" />
  {{/if}}
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$transmission}}
  <input type="hidden" name="data_id" value="{{$data_id}}" />
  <input type="hidden" name="action_id" value="{{$action_id}}" />
  <input type="hidden" name="result_id" value="{{$result_id}}" />
  <input type="hidden" name="callback" value="" />
  <input type="hidden"  name="_locked" />
  <table style="width: 100%;">
    <tr>
      <td>
        <fieldset>
          <legend>
            {{tr}}CTransmissionMedicale.caracteristiques{{/tr}}
          </legend>
          {{if !$hide_cible}}
            {{tr}}CTransmissionMedicale-object_class{{/tr}} :
            <input name="cible" type="text"
            value="{{if $transmission->_ref_object}}{{$transmission->_ref_object->_view}}{{else}}{{$transmission->libelle_ATC}}{{/if}}"
            class="autocomplete" style="width: 400px;"
            onchange="updateCible(this);"/>
            <div style="display:none; width: 350px; white-space: normal; text-align: left;" class="autocomplete" id="cible_auto_complete"></div>
            <br />
          {{/if}}
          {{mb_label object=$transmission field=degre}} : {{mb_field object=$transmission field=degre onchange="toggleDateMax();"}} &mdash;
          {{mb_label object=$transmission field=date}} : {{mb_field object=$transmission field=date}}
          <span id="date-max-{{$transmission->sejour_id}}" style="display: none;">
            &mdash;
            {{mb_label object=$transmission field=date_max}} : {{mb_field object=$transmission field=date_max form="editTrans" register=true}}
          </span>
          
          {{if $transmission->_id && !$transmission->type}}
            &mdash;
            {{mb_label object=$transmission field=type}} : {{mb_field object=$transmission field="type" typeEnum="radio"}}
            <button type="button" onclick="$V(this.form.type, '')" class="cancel notext"></button>
          {{elseif $transmission->_id}}
            &mdash;
            {{mb_label object=$transmission field=type}} : {{mb_value object=$transmission field="type"}}
          {{/if}}
        </fieldset>
      </td>
    </tr>
  </table>

  <input type="hidden" name="object_class" value="{{$transmission->object_class}}" onchange="$V(this.form.libelle_ATC, '', false);"/>
  <input type="hidden" name="object_id" value="{{$transmission->object_id}}" />
  <input type="hidden" name="libelle_ATC" value="{{$transmission->libelle_ATC}}"
    onchange="$V(this.form.object_class, '', false); $V(this.form.object_id, '', false);"/>
  <input type="hidden" name="sejour_id" value="{{$transmission->sejour_id}}" />
  <input type="hidden" name="user_id" value="{{$transmission->user_id}}" />
  {{if $transmission->_id && $transmission->type}}
    <input type="hidden" name="type" value="{{$transmission->type}}" />
  {{/if}}

  <table style="width: 100%;">
    <tr>
      {{if !$transmission->_id}}
        <td>
          <fieldset>
            <legend>
              {{tr}}CTransmissionMedicale.type.data{{/tr}}
            </legend>
            <input type="hidden" name="_type_data" value="data"/>
            {{if $action_id && $result_id && !$data_id}}
              {{mb_field object=$transmission field="_text_data" rows=6 readonly="readonly"}}
            {{else}}
              {{mb_field object=$transmission field="_text_data" rows=6 form="editTrans"
                aidesaisie="property: 'text',
                            dependField1: getForm('editTrans')._type_data,
                            dependField2: getForm('editTrans').cible,
                            classDependField2: 'CCategoryPrescription',
                            validateOnBlur: 0,
                            updateDF: 0,
                            strict: 0"}}
            {{/if}}
          </fieldset>
        </td>
        <td>
          <fieldset>
            <legend>
              {{tr}}CTransmissionMedicale.type.action{{/tr}}
            </legend>
            <input type="hidden" name="_type_action" value="action"/>
            {{if $data_id && $result_id && !$action_id}}
              {{mb_field object=$transmission field="_text_action" rows=6 readonly="readonly"}}
            {{else}}
              {{mb_field object=$transmission field="_text_action" rows=6 form="editTrans"
                aidesaisie="property: 'text',
                            dependField1: getForm('editTrans')._type_action,
                            dependField2: getForm('editTrans').cible,
                            classDependField2: 'CCategoryPrescription',
                            validateOnBlur: 0,
                            updateDF: 0,
                            strict: 0"}}
            {{/if}}
            
          </fieldset>
        </td>
        <td>
          <fieldset>
            <legend>
              {{tr}}CTransmissionMedicale.type.result{{/tr}}
            </legend>
            <input type="hidden" name="_type_result" value="result"/>
            {{if $data_id && $action_id && !$result_id}}
              {{mb_field object=$transmission field="_text_result" rows=6 readonly="readonly"}}
            {{else}}
              {{mb_field object=$transmission field="_text_result" rows=6 form="editTrans"
                aidesaisie="property: 'text',
                            dependField1: getForm('editTrans')._type_result,
                            dependField2: getForm('editTrans').cible,
                            classDependField2: 'CCategoryPrescription',
                            validateOnBlur: 0,
                            updateDF: 0,
                            strict: 0"}}
            {{/if}}
            
          </fieldset>
        </td>
      {{else}}
        <fieldset>
          <legend>
            {{mb_label object=$transmission field="text"}}
          </legend>
          {{mb_field object=$transmission field="text" rows=6 form="editTrans"
                aidesaisie="property: 'text',
                            dependField1: getForm('editTrans').type,
                            dependField2: getForm('editTrans').cible,
                            classDependField2: 'CCategoryPrescription',
                            validateOnBlur: 0,
                            updateDF: 0,
                            strict: 0"}}
        </fieldset>
      {{/if}}
    </tr>
  </table>
  {{if !$hide_button_add}}
    <button type="button" class="{{if $transmission->_id || $data_id || $action_id || $result_id}}save{{else}}add{{/if}}" onclick="submitTrans(this.form);">
      {{if $transmission->_id || $data_id || $action_id || $result_id}}
        {{tr}}Save{{/tr}}
      {{else}}
        {{tr}}Add{{/tr}}
      {{/if}}
    </button>
    {{if !$transmission->_id && !$data_id && !$action_id && !$result_id}}
      <button type="button" class="add" onclick="$V(this.form._locked, 1); submitTrans(this.form);">Ajouter et fermer la cible</button>
    {{/if}}
  {{/if}}
  <div style="margin-top: 20px;" id="list_transmissions"></div>
</form>