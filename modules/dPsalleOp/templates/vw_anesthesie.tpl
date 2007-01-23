<script type="text/javascript">

function pageMain() {
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  opsUpdater.addParam("date", "{{$date}}");
  opsUpdater.periodicalUpdate('listplages', { frequency: 90 });
}
</script>

<table class="main">
  <tr>
    <td style="width: 200px;" id="listplages"></td>
    <td class="greedyPane">
      {{if $op && !$consult->consultation_id}}
        Il n'y a aucune consultation d'anesthésie pour cette opération
      {{elseif $op}}
        <form class="watch" name="editFrmFinish" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consultation_aed" />
        <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
        <input type="hidden" name="chrono" value="{{$consult->chrono}}" />
        </form>
        
        <table class="form">
          <tr>
            <th class="category">
              Consultation
            </th>
          </tr>
          <tr>
            <td>
              Consultation d'anesthésie de <strong>{{$consult->_ref_patient->_view}}</strong>
              le {{$consult->_date|date_format:"%A %d %B %Y"}}
              par <strong>{{$consult->_ref_chir->_view}}</strong><br />
              Type de Séjour : {{tr}}CSejour.type.{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}
              <br />
              <strong>Intervention :</strong>
              le <strong>{{$consult_anesth->_ref_operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
              par le <strong>Dr. {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong> (coté {{tr}}COperation.cote.{{$consult_anesth->_ref_operation->cote}}{{/tr}})<br />
              <a class="buttonsearch" href="index.php?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$consult->consultation_id}}">
                Voir la consultation
              </a>
            </td>
          </tr>
          <tr>
            <th class="category">
              Informations Anesthésie
            </th>
          </tr>
        </table>
        <div id="InfoAnesthContent">
          {{include file="../../dPcabinet/templates/inc_consult_anesth/acc_infos_anesth.tpl"}}
        </div>
        <div id="fdrConsultContent">
          {{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}
        </div>
      {{/if}}
    </td>
  </tr>
</table>