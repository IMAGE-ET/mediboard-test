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
  mainUrl.requestUpdate('finishBanner', { waitingText : null });
}
</script>

      <form class="watch" name="editFrmFinish" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            <button id="triggerList" class="triggerHide" type="button" onclick="flipEffectElement('listConsult', 'Appear', 'Fade', 'triggerList');" style="float:left">+/-</button>
            {{if $_is_anesth}}
            <button class="print" type="button" style="float: left;" onclick="printFiche()">
              Imprimer la fiche
            </button>
            {{/if}}
            <input type="hidden" name="chrono" value="{{$consult->chrono}}" />
            Consultation
            (Etat : {{$consult->_etat}}
            {{if $consult->chrono <= $smarty.const.CC_EN_COURS}}
            / 
            <button class="submit" type="button" onclick="submitAll(); submitConsultWithChrono({{$smarty.const.CC_TERMINE}})">Terminer</button>
            {{/if}})
          </th>
        </tr>
      </table>
      </form>