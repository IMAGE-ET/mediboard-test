{{mb_default var=current_m value=""}}
{{mb_default var=op_sans_dossier_anesth value=0}}

<script>
function checkConsult() {
  var url = new Url("dPcabinet", "ajax_check_consult_anesth");
  url.addParam("consult_id", "{{$consult->_id}}");
  url.requestModal();
}
function duplicateDossier(close) {
  {{if $consult_anesth && $consult_anesth->_id && $op_sans_dossier_anesth}}
    var url = new Url("dPcabinet", "ajax_duplicate_consult_anesth");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.addParam("operation_id", "{{$op_sans_dossier_anesth}}");
    url.addParam("consult_anesth_id", "{{$consult_anesth->_id}}");
    if (close) {
      url.requestModal(null, null, { onClose: function(){ submitAll(); submitConsultWithChrono({{$consult|const:'TERMINE'}}); } } );
    }
    else {
      url.requestModal();
    }
  {{else}}
    if (close) {
      submitAll(); submitConsultWithChrono({{$consult|const:'TERMINE'}});
    }
    else {
      checkConsult();
    }
  {{/if}}
}

function submitConsultWithChrono(chrono) {
  var oForm = document.editFrmFinish;
  oForm.chrono.value = chrono;
  return onSubmitFormAjax(oForm, { onComplete : reloadFinishBanner } );
}

function reloadFinishBanner() {
  var url = new Url("dPcabinet", "httpreq_vw_finish_banner");
  url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  url.addParam("_is_anesth", "{{$_is_anesth}}");
  url.requestUpdate('finishBanner');
}

function printConsult() {
  var url = new Url("dPcabinet", "print_consult");
  url.addParam("consult_id", "{{$consult->_id}}");
  url.popup(700, 550, "Consultation");
}

function changePratPec(prat_id) {
  if(confirm('Etes-vous sur de vouloir changer le praticien de la consultation ?')) {
    var oForm = getForm("editPratPec");
    $V(oForm.prat_id, prat_id);
    oForm.submit();
  }
}
</script>

<!-- Formulaire de changement de praticien pour la pec -->
<form name="editPratPec" method="post" action="?">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="current_m" value="{{$current_m}}" />
  <input type="hidden" name="dosql" value="do_change_prat_pec" />
  {{mb_key object=$consult}}
  <input type="hidden" name="prat_id" value="" />
</form>

{{mb_script module=cabinet script=file}}
{{mb_include module=files template=yoplet_uploader object=$consult}}

<form class="watch" name="editFrmFinish" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_key   object=$consult}}
{{mb_field object=$consult field="chrono" hidden=1}}
{{if $consult_anesth && $consult_anesth->_id}}
  <input type="hidden" name="_consult_anesth_id" value="{{$consult_anesth->_id}}" />
{{/if}}

<table class="form">
  <tr>
    <th colspan="4" class="title text">
      {{assign var=patient value=$consult->_ref_patient}}
      {{assign var=sejour value=$consult->_ref_sejour}}
      {{if $consult_anesth && $consult_anesth->_id}}
      <button class="print" type="button" style="float: left;" onclick="printFiche()">
        Imprimer la fiche
      </button>
      <button class="print" type="button" style="float: left;" onclick="printAllDocs()">
        Imprimer les documents
      </button>
      {{if "maternite"|module_active && $modules.maternite->_can->read}}
        <div style="float: right;">
          {{mb_include module=maternite template=inc_input_grossesse object=$consult submit=1 large_icon=1}}
        </div>
      {{/if}}
      {{else}}
      <button type="button" class="hslip notext" style="float:left" onclick="ListConsults.toggle();">
        {{tr}}Programme{{/tr}}
      </button>
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        {{mb_include module=patients template=inc_vw_photo_identite patient=$patient size=42}}
      </a>
      <div style="float:right">
        <button class="print" type="button" onclick="printAllDocs()">
          Imprimer les documents
        </button> 
        {{if $sejour && $sejour->_id}}
          <br />
          <button class="print" type="button" onclick="printConsult();">
            Imprimer la consultation
          </button><br/> 
          <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">{{$sejour->_shortview}} </span>
        {{/if}}
        {{if "maternite"|module_active && !$_is_anesth && $modules.maternite->_can->read}}
          <br />
          {{mb_include module=maternite template=inc_input_grossesse object=$consult submit=1 large_icon=1}}
        {{/if}}   
      </div>
      {{/if}}
      {{$patient}} - {{$patient->_age}} -
      <select name="prat_id" class="ref notNull" onchange="changePratPec($V(this));" style="width: 16em;" title="Changer le praticien">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPrats selected=$consult->_ref_chir->_id}}
      </select>
      <br />
      Consultation
      (Etat : {{$consult->_etat}}
      {{if $consult->chrono <= $consult|const:'EN_COURS'}}
        /
        {{if $conf.dPcabinet.CConsultAnesth.check_close && $consult_anesth && $consult_anesth->_id}}
          <button class="tick" type="button" onclick="duplicateDossier(false);">
        {{else}}
          <button class="tick" type="button" onclick="duplicateDossier(true);">
        {{/if}}
        Terminer
        </button>
      {{elseif $conf.dPcabinet.CConsultAnesth.check_close && $consult_anesth && $consult_anesth->_id}}
        <button class="search" type="button" onclick="checkConsult();">IPAQSS</button>
      {{/if}})
    </th>
  </tr>
</table>
</form>