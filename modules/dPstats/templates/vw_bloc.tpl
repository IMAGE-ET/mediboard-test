<script type="text/javascript">

function pageMain() {
  regFieldCalendar("bloc", "debutact");
  regFieldCalendar("bloc", "finact");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="bloc" action="index.php" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Activité du bloc opératoire</th>
        </tr>
        <tr>
          <th><label for="debutact" title="Date de début">Début</label></th>
          <td class="date">
            <div id="bloc_debutact_da">{{$debutact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="debutact" title="date|notNull" value="{{$debutact}}" />
            <img id="bloc_debutact_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
         </td>
          <th><label for="salle_id" title="Salle">Salle</label></th>
          <td>
            <select name="salle_id">
              <option value="0">&mdash; Toutes les salles</option>
              {{foreach from=$listSalles item=curr_salle}}
              <option value="{{$curr_salle->id}}" {{if $curr_salle->id == $salle_id}}selected="selected"{{/if}}>
                {{$curr_salle->nom}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="finact" title="Date de fin">Fin</label></th>
          <td class="date">
            <div id="bloc_finact_da">{{$finact|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="finact" title="date|moreEquals|debutact|notNull" value="{{$finact}}" />
            <img id="bloc_finact_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
          <th><label for="prat_id" title="Praticien">Praticien</label></th>
          <td>
            <select name="prat_id" onchange="this.form.discipline_id.value = 0">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $prat_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="codeCCAM" title="Acte CCAM">Acte CCAM</label></th>
          <td><input type="text" name="codeCCAM" value="{{$codeCCAM}}" /></td>
          <th><label for="discipline_id" title="Spécialité">Spécialité</label></th>
          <td>
            <select name="discipline_id" onchange="this.form.prat_id.value = 0">
              <option value="0">&mdash; Toutes les spécialités</option>
              {{foreach from=$listDisciplines item=curr_disc}}
              <option value="{{$curr_disc->discipline_id}}" {{if $curr_disc->discipline_id == $discipline_id}}selected="selected"{{/if}}>
                {{$curr_disc->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        <tr>
          <td colspan="4" class="button"><button class="search" type="submit">Go</button></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <img alt="Nombre d'interventions" src='?m=dPstats&amp;a=graph_activite&amp;suppressHeaders=1&amp;debut={{$debutact}}&amp;fin={{$finact}}&amp;salle_id={{$salle_id}}&amp;prat_id={{$prat_id}}&amp;codeCCAM={{$codeCCAM}}&amp;discipline_id={{$discipline_id}}' />
            {{if $prat_id}}
              <img alt="Occupation des plages" src='?m=dPstats&amp;a=graph_praticienbloc&amp;suppressHeaders=1&amp;debut={{$debutact}}&amp;fin={{$finact}}&amp;salle_id={{$salle_id}}&amp;prat_id={{$prat_id}}&amp;codeCCAM={{$codeCCAM}}' />
            {{elseif $discipline_id}}
              <img alt="Répartition par praticiens" src='?m=dPstats&amp;a=graph_pratdiscipline&amp;suppressHeaders=1&amp;debut={{$debutact}}&amp;fin={{$finact}}&amp;salle_id={{$salle_id}}&amp;discipline_id={{$discipline_id}}&amp;codeCCAM={{$codeCCAM}}' />
            {{else}}
              <img alt="Patients par jour par salle" src='?m=dPstats&amp;a=graph_patjoursalle&amp;suppressHeaders=1&amp;debut={{$debutact}}&amp;fin={{$finact}}&amp;salle_id={{$salle_id}}&amp;prat_id={{$prat_id}}&amp;codeCCAM={{$codeCCAM}}' />
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>