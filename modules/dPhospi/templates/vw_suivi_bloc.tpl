<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date_suivi}}", "index.php?m={{$m}}&tab={{$tab}}&date_suivi=");
}

</script>

<form name="chgService" action="?m={{$m}}" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<table class="main">
  <tr>
    <th>
      <label for="service_id">Service</label>
      <select name="service_id" onchange="submit()">
        <option value="">&mdash; Veuillez sélectionner un service</option>
        {{foreach from=$services item=currService}}
        <option value="{{$currService->service_id}}" {{if $currService->service_id==$service_id}}selected="selected"{{/if}}>
          {{$currService->nom}}
        </option>
        {{/foreach}}
      </select>
      le
      {{$date_suivi|date_format:"%A %d %B %Y"}}
      <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
</table>

{{if $listOps|@count}}
<table class="tbl">  
  <tr>
    <th class="category">Praticien</th>
    <th class="category">Patient</th>
    <th class="category">Etat</th>
    <th class="category">Chambre</th>
  </tr>
  {{foreach from=$listOps item=currOp}}
  <tr>
    <td>Dr. {{$currOp->_ref_chir->_view}}</td>
    <td>{{$currOp->_ref_sejour->_ref_patient->_view}}</td>
    <td>
      {{if !$currOp->entree_bloc && !$currOp->entree_salle}}       En Attente d'entrée au Bloc
      {{elseif $currOp->entree_bloc && !$currOp->entree_salle}}    Entré au Bloc
      {{elseif $currOp->entree_salle && !$currOp->sortie_salle}}   En Salle d'Op
      {{elseif $currOp->sortie_salle && !$currOp->entree_reveil}}  En Attente Salle de Réveil
      {{elseif $currOp->entree_reveil && !$currOp->sortie_reveil}} En Salle de Réveil
      {{else}}                                                     Sorti du Bloc
      {{/if}}
    </td>
    <td>{{$currOp->_ref_sejour->_curr_affectation->_ref_lit->_view}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}
</form>