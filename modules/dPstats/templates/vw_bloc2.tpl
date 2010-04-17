{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function getSpreadSheet() {
  var oForm = document.bloc;
  var spreadSheet = new Url();
  spreadSheet.setModuleAction("dPstats", "vw_bloc2");
  spreadSheet.addParam("suppressHeaders", 1);
  spreadSheet.addParam("mode", "csv");
  spreadSheet.addParam("bloc_id", $V(oForm.bloc_id));
  spreadSheet.addParam("deblistbloc", $V(oForm.deblistbloc));
  spreadSheet.popup(550, 300, "statsBloc");
}

Main.add(function () {
  Calendar.regField(getForm("bloc").deblistbloc);
  Calendar.regField(getForm("bloc").finlistbloc);
});

</script>

<form name="bloc" action="?" method="get" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="dPstats" />
<table class="form">
  <tr>
    <th colspan="3" class="title">Tableau d'activité du bloc sur une journée</th>
  </tr>
  <tr>
    <td class="button" rowspan="3">
      <img src="images/pictures/spreadsheet.png" onclick="getSpreadSheet()" />
    </td>
    <th><label for="deblistbloc" title="Date de début">Date</label></th>
    <td>
      <input type="hidden" name="deblistbloc" class="notNull date" value="{{$deblist}}" />
    </td>
  </tr>
  <tr>
    <th><label for="bloc_id" title="Bloc opératoire">Bloc</label></th>
    <td colspan="2">
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
    <td class="button" colspan="2">
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
    <th colspan="3">Hospitalisation</th>
    <th rowspan="2">Chirurgien</th>
    <th rowspan="2">Anesthésiste</th>
    <th colspan="3">Nature</th>
    <th rowspan="2">Type<br />anesthésie</th>
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
    <th>Type</th>
    <th>Entree prévue</th>
    <th>Entrée réelle</th>
    <th>libelle</th>
    <th>DP</th>
    <th>Actes</th>
    <th>entrée<br />salle</th>
    <th>debut<br />induction</th>
    <th>fin<br />induction</th>
    <th>pose<br />garrot</th>
    <th>début<br />intervention</th>
    <th>fin<br />intervention</th>
    <th>retrait<br />garrot</th>
    <th>sortie<br />salle</th>
    <th>patient<br />suivant</th>
    <th>entrée</th>
    <th>sortie</th>
  </tr>
  {{foreach from=$listPlages item=curr_plage}}
  {{foreach from=$curr_plage->_ref_operations item=curr_op}}
  <tr>
    <td class="text">{{$curr_plage->date|date_format:"%d/%m/%Y"}}</td>
    <td class="text">{{$curr_plage->_ref_salle->_ref_bloc->_view}}</td>
    <td class="text">{{$curr_plage->_ref_salle->_shortview}}</td>
    <td class="text">{{$curr_plage->debut|date_format:$dPconfig.time}}</td>
    <td class="text">{{$curr_plage->fin|date_format:$dPconfig.time}}</td>
    <td class="text">
      {{if $curr_op->rank}}
        #{{$curr_op->rank}} à {{$curr_op->time_operation|date_format:$dPconfig.time}}
      {{else}}
        Non validé
      {{/if}}
    </td>
    <td class="text">
      {{if $curr_op->_rank_reel}}
        #{{$curr_op->_rank_reel}} à {{$curr_op->entree_salle|date_format:$dPconfig.time}}
      {{else}}
        Non renseigné
      {{/if}}
    </td>
    <td class="text">{{$curr_op->_ref_sejour->_ref_patient->_view}}</td>
    <td class="text">{{tr}}CSejour.type.{{$curr_op->_ref_sejour->type}}{{/tr}}</td>
    <td class="text">{{$curr_op->_ref_sejour->entree_prevue|date_format:$dPconfig.datetime}}</td>
    <td class="text">
      {{if $curr_op->_ref_sejour->entree_reelle}}
        {{$curr_op->_ref_sejour->entree_reelle|date_format:$dPconfig.datetime}}
      {{else}}
        Non renseigné
      {{/if}}
    </td>
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
    <td class="text">{{$curr_op->_ref_consult_anesth->ASA}}</td>
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