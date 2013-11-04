{{mb_default var=praticien_id value='0'}}
{{mb_default var=patient_id   value='0'}}
{{mb_default var=consult      value='0'}}
{{mb_default var=sejour       value='0'}}


<script>
  showSejourButtons = function() {
    var options = {
      title: 'Nouvelle DHE',
      showClose: true
    };
    modal('sejour-buttons', options);
  };

  newOperation = function(chir_id, pat_id) {
    var url = new Url;
    url.setModuleTab('planningOp', 'vw_edit_planning');
    url.addParam('chir_id', chir_id);
    url.addParam('pat_id', pat_id);
    url.addParam('operation_id', 0);
    url.addParam('sejour_id', 0);
    url.redirect();
  };

  newHorsPlage = function(chir_id, pat_id) {
    var url = new Url;
    url.setModuleTab('planningOp', 'vw_edit_urgence');
    url.addParam('chir_id', chir_id);
    url.addParam('pat_id', pat_id);
    url.addParam('operation_id', 0);
    url.addParam('sejour_id', 0);
    url.redirect();
  };

  newSejour = function(chir_id, pat_id) {
    var url = new Url;
    url.setModuleTab('planningOp', 'vw_edit_sejour');
    url.addParam('praticien_id', chir_id);
    url.addParam('patient_id', pat_id);
    url.addParam('sejour_id', 0);
    url.redirect();
  };

  newConsultation = function(chir_id, pat_id, consult_urgence_id) {
    var url = new Url;
    url.setModuleTab('cabinet', 'edit_planning');
    url.addParam('chir_id', chir_id);
    url.addParam('pat_id', pat_id);
    url.addParam('consult_urgence_id', consult_urgence_id);
    url.addParam('consultation_id', 0);
    url.redirect();
  };
</script>

{{if !$app->user_prefs.simpleCabinet}}
  {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
  {{if $ecap_dhe}}
    {{mb_include
    module=ecap
    template=inc_button_dhe
    patient_id=$patient_id
    praticien_id=$praticien_id
    show_non_prevue=false
    }}
  {{else}}
    {{if $m != "dPurgences"}}
      <button class="new" type="button" onclick="showSejourButtons();">
        {{tr}}CSejour-title-new{{/tr}}
      </button>

      <div id="sejour-buttons" style="display: none;">
        <button class="big" type="button" onclick="newOperation({{$praticien_id}},{{$patient_id}})" style="width: 20em;">
          {{tr}}COperation-title-create{{/tr}}
        </button>
        <br/>
        <button class="big" type="button" onclick="newHorsPlage({{$praticien_id}},{{$patient_id}})" style="width: 20em;">
          {{tr}}COperation-title-create-horsplage{{/tr}}
        </button>
        <br/>
        <button class="big" type="button" onclick="newSejour({{$praticien_id}},{{$patient_id}})" style="width: 20em;">
          {{tr}}CSejour-title-create{{/tr}}
        </button>
      </div>
    {{/if}}
  {{/if}}
{{/if}}

{{if (!$sejour || $sejour->type != "urg")}}
  <br/>
  <button class="new" type="button" onclick="newConsultation({{$praticien_id}},{{$patient_id}})" style="width: 12em;">
    {{tr}}CConsultation-title-create{{/tr}}
  </button>
{{/if}}
