<script type="text/javascript">
function pageMain() {
  regRedirectPopupCal("{{$dateRecherche}}", "index.php?m={{$m}}&tab={{$tab}}&dateRecherche=");
}
</script>

<table class="main">
  <tr>
    <td colspan="2">
      <form name="editFrmPratDate" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
        <tr>
          <td>
            <label for="selPrat" title="Veuillez choisir un praticien">Praticiens</label>
            <select name="selPrat" onchange="submit()">
            <option value="0" {{if $selPrat == 0}}selected="selected"{{/if}}>&mdash; Selectionner un praticien &mdash;</option>
            {{foreach from=$listPrat item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $selPrat == $curr_prat->user_id}}selected="selected"{{/if}}>
              {{$curr_prat->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
          <td class="date">
            {{$dateRecherche|date_format:"%A %d %B %Y"}}
            <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td class="HalfPane">
      <table class="tbl">
        <tr>
          <th colspan="5">Entrees</th>
        </tr>
        <tr>
          <th>Heure</th>
          <th>Patient</th>
          <th>Service</th>
          <th>Chambre</th>
          <th>Lit</th>
        </tr>
        {{foreach from=$AfflistEntree item=curr_aff}}
        <tr>
          <td>{{$curr_aff->entree|date_format:"%H h %M"}}</td>
          <td>{{$curr_aff->_ref_sejour->_ref_patient->_view}}</td>
          <td>{{$curr_aff->_ref_lit->_ref_chambre->_ref_service->nom}}</td>
          <td>{{$curr_aff->_ref_lit->_ref_chambre->nom}}</td>
          <td>{{$curr_aff->_ref_lit->nom}}</td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="HalfPane">
      <table class="tbl">
        <tr>
          <th colspan="5">Sortie</th>
        </tr>
        <tr>
          <th>Heure</th>
          <th>Patient</th>
          <th>Service</th>
          <th>Chambre</th>
          <th>Lit</th>
        </tr>
        {{foreach from=$AfflistSortie item=curr_aff}}
        <tr>
          <td>{{$curr_aff->sortie|date_format:"%H h %M"}}</td>
          <td>{{$curr_aff->_ref_sejour->_ref_patient->_view}}</td>
          <td>{{$curr_aff->_ref_lit->_ref_chambre->_ref_service->nom}}</td>
          <td>{{$curr_aff->_ref_lit->_ref_chambre->nom}}</td>
          <td>{{$curr_aff->_ref_lit->nom}}</td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
  </tr>
</table>