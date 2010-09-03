<script type="text/javascript">

function submitConsultWithChrono(chrono) {
  var oForm = document.editFrmFinish;
  oForm.chrono.value = chrono;
  return onSubmitFormAjax(oForm, { onComplete : reloadFinishBanner } );
}

function reloadFinishBanner() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_finish_banner");
  url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  url.addParam("_is_anesth", "{{$_is_anesth}}");
  url.requestUpdate('finishBanner');
}

function printConsult() {
  var url = new Url("dPcabinet", "print_consult");
  url.addParam("consult_id", "{{$consult->_id}}");
  url.popup(700, 550, "RPU");
}

</script>

<form class="watch" name="editFrmFinish" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_key   object=$consult}}
{{mb_field object=$consult field="chrono" hidden=1}}

<table class="form">
  <tr>
    <th colspan="4" class="title text">
      {{assign var=patient value=$consult->_ref_patient}}
      {{assign var=sejour value=$consult->_ref_sejour}}
      {{if $_is_anesth}}
      <button class="print" type="button" style="float: left;" onclick="printFiche()">
        Imprimer la fiche
      </button>
      <button class="print" type="button" style="float: left;" onclick="printAllDocs()">
        Imprimer les documents
      </button>      
      {{else}}
      <button type="button" class="hslip notext" style="float:left" onclick="ListConsults.toggle();">
        {{tr}}Programme{{/tr}}
      </button>
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        {{mb_include module=dPpatients template=inc_vw_photo_identite patient=$patient size=42}}
      </a>
      <div style="float:right">
        <button class="print" type="button" onclick="printAllDocs()">
          Imprimer les documents
        </button> 
        <br />
        {{if $sejour && $sejour->_id}}
          <button class="print" type="button" onclick="printConsult();">
            Imprimer la consultation
          </button><br/> 
          <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">{{$sejour}} </span>          
        {{/if}}   
      </div>
      {{/if}}
      {{$patient}} - {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult->_ref_chir}}
      <br />
      Consultation
      (Etat : {{$consult->_etat}}
      {{if $consult->chrono <= $consult|const:'EN_COURS'}}
        / 
        <button class="submit" type="button" onclick="submitAll(); submitConsultWithChrono({{$consult|const:'TERMINE'}})">
          Terminer
        </button>
      {{/if}})
    </th>
  </tr>
</table>
</form>