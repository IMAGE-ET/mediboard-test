{{mb_script module="dPcabinet" script="exam_dialog"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">
  function submitForm(oForm) {
    onSubmitFormAjax(oForm);
  }
  Main.add(function() {
    new Control.Tabs.create('tabs_consult');
  });
</script>

<!-- Formulaire pour réactualiseér -->
<form name="editFrmFinish" method="get">
  {{mb_key object=$consult}}
</form>

<ul id="tabs_consult" class="control_tabs">
  {{if !$consult_anesth->_id}}
    <li>
      <a href="#exams">Examens</a>
    </li>
  {{else}}
    <li>
      <a href="#exam_clinique">Exam. Clinique</a>
    </li>
    <li>
      <a href="#intubation">Intubation</a>
    </li>
    <li>
      <a href="#exam_comp">Exam. Comp.</a>
    </li>
    <li>
      <a href="#infos_anesth">Infos. Anesth.</a>
    </li>
  {{/if}}
</ul>

<hr class="control_tabs" />

{{if !$consult_anesth->_id}}
  <div id="exams" style="display: none;">
    {{mb_include module=dPcabinet template="inc_main_consultform"}}
  </div>
{{else}}
  <div id="exam_clinique" style="display: none;">
    {{mb_include module=dPcabinet template="inc_consult_anesth/acc_examens_clinique"}}
  </div>
  <div id="intubation" style="display: none;">
    {{mb_include module=dPcabinet template="inc_consult_anesth/intubation"}}
  </div>
  <div id="exam_comp" style="display: none;">
    {{mb_include module=dPcabinet template="inc_consult_anesth/acc_examens_complementaire"}}
  </div>
  <div id="infos_anesth" style="display: none;">
    {{mb_include module=dPcabinet template="inc_consult_anesth/acc_infos_anesth"}}
  </div>
{{/if}}