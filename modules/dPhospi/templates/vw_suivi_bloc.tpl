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
      <select name="service_id" onchange="this.form.submit()">
        <option value="0">&mdash; Tous les services</option>
        {{foreach from=$services item=currService}}
        <option value="{{$currService->_id}}" {{if $currService->_id==$service_id}}selected="selected"{{/if}}>
          {{$currService}}
        </option>
        {{/foreach}}
      </select>
      <select name="bloc_id" onchange="this.form.submit()">
        <option value="0">&mdash; Tous les blocs</option>
        {{foreach from=$blocs item=currBloc}}
        <option value="{{$currBloc->_id}}" {{if $currBloc->_id==$bloc_id}}selected="selected"{{/if}}>
          {{$currBloc}}
        </option>
        {{/foreach}}
      </select>
      le
      {{$date_suivi|date_format:$conf.longdate}}
      <input type="hidden" name="date_suivi" class="date" value="{{$date_suivi}}" onchange="this.form.submit()" />
    </th>
  </tr>
</table>
</form>

<table class="tbl"> 
{{foreach from=$listServices key=keyServ item=currService}}
  <tr>
    <th class="title" colspan="10">
       {{if $keyServ == "NP"}}
         Non placés
       {{else}}
         {{$services.$keyServ->_view}}
       {{/if}}
     </th>
  </tr>
  <tr>
    <th class="category narrow">Heure prévue</th>
    <th class="category">Patient</th>
    <th class="category">Praticien</th>
    <th class="category">Etat</th>
    <th class="category">Lit</th>
  </tr>
  {{foreach from=$currService item=currOp}}
  <tr>
    <td class="button">
      {{if $currOp->time_operation && $currOp->time_operation != "00:00:00"}}
        {{$currOp->time_operation|date_format:$conf.time}}
      {{else}}
        -
      {{/if}}
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$currOp->_ref_sejour->_ref_patient->_guid}}')">
        {{$currOp->_ref_sejour->_ref_patient->_view}}
      </span>
    </td>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$currOp->_ref_chir}}</td>
    <td>
      {{if !$currOp->entree_bloc && !$currOp->entree_salle}}       En attente d'entrée au bloc
      {{elseif $currOp->entree_bloc && !$currOp->entree_salle}}    Entré(e) au bloc
      {{elseif $currOp->entree_salle && !$currOp->sortie_salle}}   En salle d'op
      {{elseif $currOp->sortie_salle && !$currOp->entree_reveil}}  En attente salle de réveil
      {{elseif $currOp->entree_reveil && !$currOp->sortie_reveil}} En salle de réveil
      {{else}}                                                     Sorti(e) du bloc
      {{/if}}
      {{mb_include module=forms template=inc_widget_ex_class_register object=$currOp event=liaison}}
    </td>
    <td>{{$currOp->_ref_affectation->_ref_lit->_view}}</td>
  </tr>
  {{/foreach}}
{{/foreach}}
</table>