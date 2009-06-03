<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("chgService").date_suivi, null, {noView: true});
});

</script>

<form name="chgService" action="?m={{$m}}" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<table class="main">
  <tr>
    <th>
      <label for="service_id">Service</label>
      <select name="service_id" onchange="this.form.submit()">
        <option value="0">&mdash; Tous les services</option>
        {{foreach from=$services item=currService}}
        <option value="{{$currService->service_id}}" {{if $currService->service_id==$service_id}}selected="selected"{{/if}}>
          {{$currService->nom}}
        </option>
        {{/foreach}}
      </select>
      le
      {{$date_suivi|date_format:$dPconfig.longdate}}
      <input type="hidden" name="date_suivi" class="date" value="{{$date_suivi}}" onchange="this.form.submit()" />
    </th>
  </tr>
</table>

<table class="tbl"> 
{{foreach from=$affOper key=keyServ item=currService}} 
  {{if $service_id==0}}
  <tr>
    <th class="title" colspan="4">{{$services.$keyServ->nom}}</th>
  </tr>
  {{/if}}
  <tr>
    <th class="category">Praticien</th>
    <th class="category">Patient</th>
    <th class="category">Etat</th>
    <th class="category">Chambre</th>
  </tr>
  {{foreach from=$currService item=currOp}}
  <tr>
    <td>Dr {{$currOp->_ref_chir->_view}}</td>
    <td>{{$currOp->_ref_sejour->_ref_patient->_view}}</td>
    <td>
      {{if !$currOp->entree_bloc && !$currOp->entree_salle}}       En attente d'entrée au bloc
      {{elseif $currOp->entree_bloc && !$currOp->entree_salle}}    Entré(e) au bloc
      {{elseif $currOp->entree_salle && !$currOp->sortie_salle}}   En salle d'op
      {{elseif $currOp->sortie_salle && !$currOp->entree_reveil}}  En attente salle de réveil
      {{elseif $currOp->entree_reveil && !$currOp->sortie_reveil}} En salle de réveil
      {{else}}                                                     Sorti(e) du bloc
      {{/if}}
    </td>
    <td>{{$currOp->_ref_sejour->_curr_affectation->_ref_lit->_view}}</td>
  </tr>
  {{/foreach}}
{{/foreach}}
</table>
</form>