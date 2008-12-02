<script type="text/javascript">

function getSpreadSheet() {
  var spreadSheet = new Url();
  spreadSheet.setModuleAction("dPstats", "vw_bloc2");
  spreadSheet.addParam("suppressHeaders", 1);
  spreadSheet.addParam("mode", "csv");
  spreadSheet.popup(550, 300, "statsBloc");
}

Main.add(function () {
  Calendar.regField("bloc", "deblistbloc");
  Calendar.regField("bloc", "finlistbloc");
});

</script>

<table class="main">
  <tr>
    <td>
      <form name="bloc" action="?" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Tableau d'activité du bloc sur une journée</th>
        </tr>
        <tr>
          <td class="button" rowspan="2">
            <img src="images/pictures/spreadsheet.png" onclick="getSpreadSheet()" />
          </td>
          <th><label for="deblistbloc" title="Date de début">Début</label></th>
          <td class="date">
            <div class="control">
            <div class="date" id="bloc_deblistbloc_da">{{$deblist|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="deblistbloc" class="notNull date" value="{{$deblist}}" />
            <img id="bloc_deblistbloc_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début"/>
            </div>
          </td>
          <td>
            <select name="bloc_id">
              <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
              {{foreach from=$listBlocs item=curr_bloc}}
              <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id }}selected="selected"{{/if}}>
                {{$curr_bloc->nom}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="finlistbloc" title="Date de fin">Fin</label></th>
          <td class="date">
            <div class="control">
            <div class="date" id="bloc_finlistbloc_da">{{$finlist|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="finlistbloc" class="notNull date" value="{{$finlist}}" />
            <img id="bloc_finlistbloc_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
            </div>
          </td>
          <td>
            <button class="search" type="submit">Afficher</button>
          </td>
        </tr>
      </table>
      </form>
      <table class="tbl">
        <tr>
          <th rowspan="2">Date</th>
          <th rowspan="2">Bloc</th>
          <th rowspan="2">Salle</th>
          <th colspan="2">Vacation</th>
          <th colspan="2">N° d'ordre</th>
          <th rowspan="2">Patient</th>
          <th rowspan="2">Prise en charge</th>
          <th rowspan="2">Chirurgien</th>
          <th rowspan="2">Anesthésiste</th>
          <th colspan="3">Nature</th>
          <th rowspan="2">Type<br />d'anesthésie</th>
          <th rowspan="2">Code<br />ASA</th>
          <th rowspan="2">Placement<br />programme</th>
          <th colspan="9">Timings intervention</th>
          <th colspan="2">Timings reveil</th>
        </tr>
        <tr>
          <th>Début</th>
          <th>Fin</th>
          <th>Prévu</th>
          <th>Réel</th>
          <th>libelle</th>
          <th>DP</th>
          <th>Actes</th>
          <th>entrée salle</th>
          <th>debut d'induction</th>
          <th>fin d'induction</th>
          <th>pose garrot</th>
          <th>début intervention</th>
          <th>fin intervention</th>
          <th>retrait garrot</th>
          <th>sortie salle</th>
          <th>patient suivant</th>
          <th>entrée</th>
          <th>sortie</th>
        </tr>
        {{foreach from=$listPlages item=curr_plage}}
        {{foreach from=$curr_plage->_ref_operations item=curr_op}}
        <tr>
          <td class="text">{{$curr_plage->date|date_format:"%d/%m/%Y"}}</td>
          <td class="text">{{$curr_plage->_ref_salle->_ref_bloc->_view}}</td>
          <td class="text">{{$curr_plage->_ref_salle->_view}}</td>
          <td class="text">{{$curr_plage->debut|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_plage->fin|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->rank}}</td>
          <td class="text">{{$curr_op->_rank_reel}}</td>
          <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
          <td class="text">{{tr}}CSejour.type.{{$curr_op->_ref_sejour->type}}{{/tr}}</td>
          <td class="text">Dr {{$curr_op->_ref_chir->_view}}</td>
          <td class="text">
            {{if $curr_op->_ref_anesth->_id}}
              Dr {{$curr_op->_ref_anesth->_view}}
            {{/if}}
          </td>
          <td class="text">{{$curr_op->libelle}}</td>
          <td class="text">{{$curr_op->_ref_sejour->DP}}</td>
          <td class="text">{{$curr_op->codes_ccam|replace:'|':' '}}</td>
          <td class="text">{{$curr_op->_lu_type_anesth}}</td>
          <td class="text">{{tr}}CConsultAnesth.ASA.{{$curr_op->_ref_consult_anesth->ASA}}{{/tr}}</td>
          <td class="text">
            {{if $curr_op->_ref_first_log}}
              {{$curr_op->_ref_first_log->date|date_format:$dPconfig.datetime}}
            {{else}}
              &mdash;
            {{/if}}
          </td>
          <td class="text">{{$curr_op->entree_salle|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->induction_debut|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->induction_fin|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->pose_garrot|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->debut_op|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->fin_op|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->retrait_garrot|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->sortie_salle|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->_pat_next|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->entree_reveil|date_format:$dPconfig.time}}</td>
          <td class="text">{{$curr_op->sortie_reveil|date_format:$dPconfig.time}}</td>
        </tr>
        {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>