<script type="text/javascript">

Main.add(function () {
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_liste_plages");
  opsUpdater.addParam("date", "{{$date}}");
  opsUpdater.periodicalUpdate('listplages', { frequency: 90 });
});

function printFiche() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_fiche"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printFiche");
  return;
}

function printAllDocs() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_docs"); 
  url.addElement(document.editFrmFinish.consultation_id);
  url.popup(700, 500, "printDocuments");
  return;
}
</script>

<table class="main">
  <tr>
    <td style="width: 200px;" id="listplages"></td>
    <td class="greedyPane">
      {{if $op && !$consult->consultation_id}}
        <table class="form">
          <tr>
            <th class="category">Consultation</th>
          </tr>
          <tr>
            <td>Il n'y a aucune consultation d'anesthésie pour cette intervention</td>
          </tr>
        </table>
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
            <th class="category" colspan="2">
              Consultation
            </th>
          </tr>
          <tr>
            <td>
              Consultation d'anesthésie de <strong>{{$consult->_ref_patient->_view}}</strong>
              le {{$consult->_date|date_format:$dPconfig.longdate}}
              par <strong>{{$consult->_ref_chir->_view}}</strong><br />
              Type de Séjour : {{tr}}CSejour.type.{{$consult_anesth->_ref_operation->_ref_sejour->type}}{{/tr}}
              <br />
              <strong>Intervention :</strong>
              le <strong>{{$consult_anesth->_ref_operation->_datetime|date_format:"%a %d %b %Y"}}</strong>
              par le <strong>Dr {{$consult_anesth->_ref_operation->_ref_chir->_view}}</strong> (coté {{tr}}COperation.cote.{{$consult_anesth->_ref_operation->cote}}{{/tr}})<br />
            </td>
            <td class="button">
              <a class="button search" href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$consult->consultation_id}}">
                Voir la consultation
              </a><br />
              <button class="print" type="button" onclick="printFiche()">
                Imprimer la fiche
              </button><br />
              <button class="print" type="button" onclick="printAllDocs()">
                Imprimer les documents
              </button> 
            </td>
          </tr>
          <tr>
            <th class="category" colspan="2">
              Informations Anesthésie
            </th>
          </tr>
        </table>
        <div id="InfoAnesth">
          {{include file="../../dPcabinet/templates/inc_consult_anesth/acc_infos_anesth.tpl"}}
        </div>
        <div id="fdrConsult">
          {{include file="../../dPcabinet/templates/inc_fdr_consult.tpl"}}
        </div>
      {{/if}}
    </td>
  </tr>
</table>