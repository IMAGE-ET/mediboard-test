<script type="text/javascript">
  function showSejourButtons() {
    var options = {
      title: 'Nouvelle DHE',
      showClose: true
    };
    modal('sejour-buttons', options);
  }

  function newOperation(chir_id, pat_id) {
    var url = new Url;
    url.setModuleTab('planningOp', 'vw_edit_planning');
    url.addParam('chir_id', chir_id);
    url.addParam('pat_id', pat_id);
    url.addParam('operation_id', 0);
    url.addParam('sejour_id', 0);
    url.redirect();
  }

  function newHorsPlage(chir_id, pat_id) {
    var url = new Url;
    url.setModuleTab('planningOp', 'vw_edit_urgence');
    url.addParam('chir_id', chir_id);
    url.addParam('pat_id', pat_id);
    url.addParam('operation_id', 0);
    url.addParam('sejour_id', 0);
    url.redirect();
  }

  function newSejour(chir_id, pat_id) {
    var url = new Url;
    url.setModuleTab('planningOp', 'vw_edit_sejour');
    url.addParam('praticien_id', chir_id);
    url.addParam('patient_id', pat_id);
    url.addParam('sejour_id', 0);
    url.redirect();
  }

  function newConsultation(chir_id, pat_id, consult_urgence_id) {
    var url = new Url;
    url.setModuleTab('cabinet', 'edit_planning');
    url.addParam('chir_id', chir_id);
    url.addParam('pat_id', pat_id);
    url.addParam('consult_urgence_id', consult_urgence_id);
    url.addParam('consultation_id', 0);
    url.redirect();
  }

</script>

{{if !$app->user_prefs.simpleCabinet}}
  {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
  {{if $ecap_dhe}}
    {{mb_include
    module=ecap
    template=inc_button_dhe
    patient_id=$consult->patient_id
    praticien_id=$consult->_praticien_id
    show_non_prevue=false
    }}
  {{else}}
    {{if $m != "dPurgences"}}
      <button class="new" type="button" onclick="showSejourButtons();">
        {{tr}}CSejour-title-new{{/tr}}
      </button>
      <br/>

      <div id="sejour-buttons" style="display: none;">
        <button class="big" type="button" onclick="newOperation({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 20em;">
          {{tr}}COperation-title-create{{/tr}}
        </button>
        <br/>
        <button class="big" type="button" onclick="newHorsPlage({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 20em;">
          {{tr}}COperation-title-create-horsplage{{/tr}}
        </button>
        <br/>
        <button class="big" type="button" onclick="newSejour({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 20em;">
          {{tr}}CSejour-title-create{{/tr}}
        </button>

        <br/>
      </div>

    {{/if}}
  {{/if}}
{{/if}}

{{assign var=sejour value=$consult->_ref_sejour}}
{{if !$sejour || $sejour->type != "urg"}}
  <button class="new" type="button" onclick="newConsultation({{$consult->_praticien_id}},{{$consult->patient_id}})" style="width: 12em;">
    {{tr}}CConsultation-title-create{{/tr}}
  </button>
{{/if}}
