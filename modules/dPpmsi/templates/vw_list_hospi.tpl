<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="8">
      Liste des {{$listSejours|@count}} personne(s) hospitalisée(s) au {{$date|date_format:$dPconfig.longdate}}
      
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=CSejour field=facture}}</th>
    <th>{{mb_title class=CSejour field=_num_dossier}}</th>
    <th>{{mb_title class=CSejour field=praticien_id}}</th>
    <th>{{mb_title class=CSejour field=patient_id}}</th>
    <th>{{mb_title class=CSejour field=_entree}}</th>
    <th>{{mb_title class=CSejour field=_sortie}}</th>
    <th>GHM</th>
    <th>Bornes</th>
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  {{assign var="GHM" value=$curr_sejour->_ref_GHM}}
  <tr>
    <td>
      {{if $curr_sejour->_ref_hprim_files|@count}}
       <img src="images/icons/tick.png" alt="ok" />
      {{else}}
      <img src="images/icons/cross.png" alt="alerte" />
      {{/if}}
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}')">
        [{{$curr_sejour->_num_dossier}}]
      </span>
    </td>
    <td class="text">
      Dr {{$curr_sejour->_ref_praticien->_view}}
    </td>

    <td class="text">
      {{assign var=patient value=$curr_sejour->_ref_patient}}
      <a href="?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={{$patient->_id}}" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient}}
        [{{$patient->_IPP}}]
      </a>
    </td>

    <td class="text">
      {{$curr_sejour->_entree|date_format:$dPconfig.datetime}}
    </td>

    <td class="text">
      {{$curr_sejour->_sortie|date_format:$dPconfig.datetime}}
    </td>
    
    <td class="text" {{if !$GHM->_CM}}style="background-color:#fdd"{{/if}}>
      {{$GHM->_GHM}}
      {{if $GHM->_DP}}: {{$GHM->_GHM_nom}}{{/if}}
    </td>
  
    <td class="text">
      {{if $GHM->_DP}}
        {{if $GHM->_borne_basse > $GHM->_duree}}
          <img src="images/icons/cross.png" alt="alerte" /> Séjour trop court
        {{elseif $GHM->_borne_haute < $GHM->_duree}}
          <img src="images/icons/cross.png" alt="alerte" /> Séjour trop long
        {{else}}
          <img src="images/icons/tick.png" alt="ok" />
        {{/if}}
      {{else}}
      -
      {{/if}}
    </td>
  </tr>
  {{/foreach}}
</table>