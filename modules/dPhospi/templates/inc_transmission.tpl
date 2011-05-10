{{mb_default var=hide_cible value=0}}
{{mb_default var=hide_button_add value=0}}
{{mb_default var=update_plan_soin value=0}}

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
      minHours: '{{$hour-1}}',
      maxHours: '{{$hour+1}}'
    };
    
  var dates = {};
  dates.limit = {
    start: '{{$date}}',
    stop: '{{$date}}'
  };
  Calendar.regField(oFormTrans.date, dates, options);

  {{if !$transmission->_id}}
    //Initialisation du champ dates
    oFormTrans.date_da.value = "Heure actuelle";
    $V(oFormTrans.date, "now");
    
    new AideSaisie.AutoComplete(oFormTrans._text_data, {
      property: "text",
      objectClass: "CTransmissionMedicale", 
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      dependField1: oFormTrans._type_data,
      dependField2: oFormTrans.cible,
      classDependField2: "CCategoryPrescription",
      validateOnBlur:0,
      strict: false
    });
    
    new AideSaisie.AutoComplete(oFormTrans._text_action, {
      property: "text",
      objectClass: "CTransmissionMedicale", 
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      dependField1: oFormTrans._type_action,
      dependField2: oFormTrans.cible,
      classDependField2: "CCategoryPrescription",
      validateOnBlur:0,
      strict: false
    });

    new AideSaisie.AutoComplete(oFormTrans._text_result, {
      property: "text",
      objectClass: "CTransmissionMedicale",
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      dependField1: oFormTrans._type_result,
      dependField2: oFormTrans.cible,
      classDependField2: "CCategoryPrescription",
      validateOnBlur:0,
      strict: false
    });
  {{else}}
    new AideSaisie.AutoComplete(oFormTrans.text, {
      objectClass: "CTransmissionMedicale", 
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      dependField1: oFormTrans.type,
      dependField2: oFormTrans.cible,
      classDependField2: "CCategoryPrescription",
      validateOnBlur:0,
      strict: false
    });
  {{/if}}
 
  {{if $transmission->object_id}}
    updateListTransmissions('{{$transmission->object_id}}', '{{$transmission->object_class}}');
  {{elseif $transmission->libelle_ATC}}
    updateListTransmissions('{{$transmission->libelle_ATC|smarty:nodefaults|JSAttribute}}');
  {{/if}}
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
          {{tr}}CTransmissionMedicale-degre{{/tr}} : {{mb_field object=$transmission field=degre}}
          {{tr}}CTransmissionMedicale-date{{/tr}} : {{mb_field object=$transmission field="date"}}
          
          {{if $transmission->_id && !$transmission->type}}
            {{tr}}CTransmissionMedicale-type{{/tr}} : {{mb_field object=$transmission field="type" typeEnum="radio"}}
            <button type="button" onclick="$V(this.form.type, '')" class="cancel notext"></button>
          {{elseif $transmission->_id}}
            {{tr}}CTransmissionMedicale-type{{/tr}} : {{mb_value object=$transmission field="type"}}
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
            {{mb_field object=$transmission field="_text_data"}}
          </fieldset>
        </td>
        <td>
          <fieldset>
            <legend>
              {{tr}}CTransmissionMedicale.type.action{{/tr}}
            </legend>
              <input type="hidden" name="_type_action" value="action"/>
              {{mb_field object=$transmission field="_text_action"}}
          </fieldset>
        </td>
        <td>
          <fieldset>
            <legend>
              {{tr}}CTransmissionMedicale.type.result{{/tr}}
            </legend>
            <input type="hidden" name="_type_result" value="result"/>
            {{mb_field object=$transmission field="_text_result"}}
          </fieldset>
        </td>
      {{else}}
        <fieldset>
          <legend>
            {{mb_label object=$transmission field="text"}}
          </legend>
          {{mb_field object=$transmission field="text"}}
        </fieldset>
      {{/if}}
    </tr>
  </table>
  {{if !$hide_button_add}}
    <button type="button" class="{{if $transmission->_id}}save{{else}}add{{/if}}" onclick="submitTrans(this.form);">
      {{if $transmission->_id}}
        {{tr}}Save{{/tr}}
      {{else}}
        {{tr}}Add{{/tr}}
      {{/if}}
    </button>
  {{/if}}
  <div style="margin-top: 20px;" id="list_transmissions"></div>
</form>