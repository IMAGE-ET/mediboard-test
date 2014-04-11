{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
function getSpreadSheet() {
  var form = document.bloc;
  var url = new Url('stats', 'vw_bloc2', 'raw');
  url.addParam('mode', 'csv');
  url.addElement(form.bloc_id);
  url.addElement(form.deblistbloc);
  url.addElement(form.finlistbloc);
  url.popup(550, 300, 'statsBloc');
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
    <th colspan="5" class="title">Tableau d'activité du bloc sur une journée</th>
  </tr>
  <tr>
    <td class="button" rowspan="4">
      <img src="images/pictures/spreadsheet.png" onclick="getSpreadSheet()" />
    </td>
    <th><label for="deblistbloc" title="Date de début">Du</label></th>
    <td>
      <input type="hidden" name="deblistbloc" class="notNull date" value="{{$deblist}}" />
    </td>
  </tr>
  <tr>
    <th><label for="finlistbloc" title="Date de début">Au</label></th>
    <td>
      <input type="hidden" name="finlistbloc" class="notNull date" value="{{$finlist}}" />
    </td>
  </tr>
  <tr>
    <th><label for="bloc_id" title="Bloc opératoire">Bloc</label></th>
    <td colspan="4">
      <select name="bloc_id">
        <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
        {{foreach from=$blocs item=_bloc}}
        <option value="{{$_bloc->_id}}" {{if $_bloc->_id == $bloc->_id }}selected="selected"{{/if}}>
          {{$_bloc->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="type" title="Type">Type</label></th>
    <td colspan="4">
      <select name="type">
        <option value="prevue" {{if $type == "prevue"}}selected="selected" {{/if}}>Prévues</option>
        <option value="hors_plage" {{if $type == "hors_plage"}}selected="selected" {{/if}}>Hors plage</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="5">
      <button class="search" type="submit">Afficher</button>
    </td>
  </tr>
</table>
</form>

<table class="tbl">
  <tr>
    <th rowspan="2">Date</th>
    <th colspan="2">Salle</th>
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
    <th>Prévu</th>
    <th>Réel</th>
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
  {{if $type == "prevue"}}
    {{foreach from=$plages item=_plage}}
    <tr>
      <th colspan="30" class="section">
        {{$_plage}} 
        &mdash; {{$_plage->_ref_salle}}
        &mdash; {{$_plage->_ref_owner}}
      </th>
    </tr>
    {{foreach from=$_plage->_ref_operations item=_operation}}
      {{mb_include template=inc_bloc2_line}}
    {{foreachelse}}
      <tr>
        <td colspan="30" class="empty">{{tr}}COperation.none{{/tr}}</td>
      </tr>
    {{/foreach}}


    {{/foreach}}

  {{else}}
    {{foreach from=$operations item=_operation}}
      <tr>
        <td class="text">{{$_operation->date|date_format:"%d/%m/%Y"}}</td>
        <td class="text">{{$_operation->_ref_salle->_view}}</td>
        <td class="text">{{$_operation->_ref_salle->_view}}</td>
        <td class="text">{{$_operation->date|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->date|date_format:$conf.time}}</td>
        <td class="text">
          {{if $_operation->rank}}
            #{{$_operation->rank}} à {{$_operation->time_operation|date_format:$conf.time}}
          {{else}}
            Non validé
          {{/if}}
        </td>
        <td class="text">
          {{if $_operation->_rank_reel}}
            #{{$_operation->_rank_reel}} à {{$_operation->entree_salle|date_format:$conf.time}}
          {{else}}
            Non renseigné
          {{/if}}
        </td>
        <td class="text">{{$_operation->_ref_sejour->_ref_patient->_view}} ({{$_operation->_ref_sejour->_ref_patient->_age}})</td>
        <td class="text">{{tr}}CSejour.type.{{$_operation->_ref_sejour->type}}{{/tr}}</td>
        <td class="text">{{$_operation->_ref_sejour->entree_prevue|date_format:$conf.datetime}}</td>
        <td class="text">
          {{if $_operation->_ref_sejour->entree_reelle}}
            {{$_operation->_ref_sejour->entree_reelle|date_format:$conf.datetime}}
          {{else}}
            Non renseigné
          {{/if}}
        </td>
        <td class="text">Dr {{$_operation->_ref_chir->_view}}</td>
        <td class="text">
          {{if $_operation->_ref_anesth->_id}}
            Dr {{$_operation->_ref_anesth->_view}}
          {{/if}}
        </td>
        <td class="text">{{$_operation->libelle}}</td>
        <td class="text">{{$_operation->_ref_sejour->DP}}</td>
        <td class="text">{{$_operation->codes_ccam|replace:'|':' '}}</td>
        <td class="text">{{$_operation->_lu_type_anesth}}</td>
        <td class="text">{{$_operation->ASA}}</td>
        <td class="text">{{$_operation->_ref_workflow->date_creation|date_format:$conf.datetime}}</td>
        <td class="text">{{$_operation->entree_salle|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->induction_debut|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->induction_fin|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->pose_garrot|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->debut_op|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->fin_op|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->retrait_garrot|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->sortie_salle|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->_pat_next|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->entree_reveil|date_format:$conf.time}}</td>
        <td class="text">{{$_operation->sortie_reveil_possible|date_format:$conf.time}}</td>
      </tr>

    {{foreachelse}}
      <tr>
        <td colspan="30" class="empty">{{tr}}COperation.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  {{/if}}
</table>