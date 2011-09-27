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
  <li href="#antecedents">
    <a href="#antecedents">Antécédents</a>
  </li>
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
    {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
      <li>
        <a href="#facteurs_risque">Facteurs de risques</a>
      </li>
    {{/if}}
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="antecedents" style="display: none">
  {{mb_include module=dPcabinet template="inc_ant_consult"}}
</div>
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
  {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
    <div id="facteurs_risque" style="display: none;">
      {{mb_include module=dPcabinet template="inc_consult_anesth/inc_vw_facteurs_risque"}}
    </div>
  {{/if}}
{{/if}}