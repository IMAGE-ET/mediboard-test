<script type="text/javascript">

function submitConsultWithChrono(chrono) {
  var oForm = document.editFrmFinish;
  oForm.chrono.value = chrono;
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadFinishBanner });
}

function reloadFinishBanner() {
  var mainUrl = new Url;
  mainUrl.setModuleAction("dPcabinet", "httpreq_vw_finish_banner");
  mainUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  mainUrl.addParam("_is_anesth", "{{$_is_anesth}}");
  mainUrl.requestUpdate('finishBanner');
}
</script>

<form class="watch" name="editFrmFinish" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
{{mb_field object=$consult field="chrono" hidden=1 prop=""}}

<table class="form">
  <tr>
    <th colspan="4" class="title text">
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
      <a style="float: left" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$consult->_ref_patient->_id}}">
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$consult->_ref_patient size=42}}
      </a>
      <div style="float:right"> 
        <button class="print" type="button" onclick="printAllDocs()">
          Imprimer les documents
        </button> 
        <br />
        {{if isset($consult->_ref_sejour->_id|smarty:nodefaults)}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$consult->_ref_sejour->_guid}}');">{{$consult->_ref_sejour->_view}} </span> 
        {{/if}}   
      </div>
      {{/if}}
      {{$consult->_ref_patient}} - {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult->_ref_chir}}
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