<script type="text/javascript">

function pageMain() {
  regFieldCalendar("bloc", "deblist");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="2" class="category">Tableau d'activité du bloc sur une journée</th>
        </tr>
        <tr>
          <th><label for="debutlist" title="Date de début">Début</label></th>
          <td class="date">
            <div id="bloc_deblist_da">{{$deblist|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="deblist" title="notNull date" value="{{$deblist}}" />
            <img id="bloc_deblist_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début"/>
            <button class="search" type="submit">Go</button>
          </td>
        </tr>
      </table>
      </form>
      <table class="tbl">
        <tr>
          <th rowspan="2">Date</th>
          <th rowspan="2">Salle</th>
          <th rowspan="2">Plage</th>
          <th colspan="2">N° d'ordre</th>
          <th rowspan="2">Patient</th>
          <th colspan="2">Prise en charge</th>
          <th rowspan="2">Chirurgien</th>
          <th rowspan="2">Anesthésiste</th>
          <th colspan="2">Nature</th>
          <th rowspan="2">Type<br />d'anesthésie</th>
          <th rowspan="2">Code<br />ASA</th>
          <th rowspan="2">Placement<br />programme</th>
          <th rowspan="2">Attribution<br />n° d'ordre</th>
          <th colspan="8">Timings opération</th>
          <th colspan="2">Timings reveil</th>
        </tr>
        <tr>
          <th>Prévu</th>
          <th>Réèl</th>
          <th>Type</th>
          <th>Présent à minuit</th>
          <th>libelle</th>
          <th>CCAM</th>
          <th>entrée salle</th>
          <th>induction</th>
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
          <td>{{$curr_plage->_ref_salle->_view}}</td>
          <td class="text">{{$curr_plage->debut|date_format:"%Hh%M"}}-{{$curr_plage->fin|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->rank}}</td>
          <td class="text">{{$curr_op->_rank_reel}}</td>
          <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
          <td class="text">{{tr}}CSejour.type.{{$curr_op->_ref_sejour->type}}{{/tr}}</td>
          <td class="text">{{if $curr_op->_ref_sejour->_at_midnight}}Oui{{else}}Non{{/if}}</td>
          <td class="text">Dr. {{$curr_op->_ref_chir->_view}}</td>
          <td class="text">Dr. {{$curr_op->_ref_anesth->_view}}</td>
          <td class="text">{{$curr_op->libelle}}</td>
          <td class="text">{{$curr_op->codes_ccam|replace:'|':' '}}</td>
          <td class="text">{{$curr_op->_lu_type_anesth}}</td>
          <td class="text">{{tr}}CConsultAnesth.ASA.{{$curr_op->_ref_consult_anesth->ASA}}{{/tr}}</td>
          <td class="text">{{$curr_op->_ref_first_log->date|date_format:"%d/%m/%Y à %Hh%M"}}</td>
          <td class="text">?</td>
          <td class="text">{{$curr_op->entree_salle|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->induction|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->pose_garrot|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->debut_op|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->fin_op|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->retrait_garrot|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->sortie_salle|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->_pat_next|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->entree_reveil|date_format:"%Hh%M"}}</td>
          <td class="text">{{$curr_op->sortie_reveil|date_format:"%Hh%M"}}</td>
        </tr>
        {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>