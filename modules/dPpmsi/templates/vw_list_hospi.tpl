<script type="text/javascript">

Main.add(function () {
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});

</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="8">
      Liste des {{$listSejours|@count}} personne(s) hospitalisée(s) au {{$date|date_format:$dPconfig.longdate}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    <th>Traité</th>
    <th>Dossier</th>
    <th>Praticien</th>
    <th>Patient</th>
    <th>Entrée</th>
    <th>Sortie</th>
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
      [{{$curr_sejour->_num_dossier}}]
    </td>
    <td class="text">
      Dr {{$curr_sejour->_ref_praticien->_view}}
    </td>

    <td class="text">
      <a title="Voir le dossier PMSI" href="?m=dPpmsi&amp;tab=vw_dossier&amp;pat_id={{$curr_sejour->patient_id}}">
        {{$curr_sejour->_ref_patient->_view}}
        [{{$curr_sejour->_ref_patient->_IPP}}]
      </a>
    </td>

    <td class="text">
      {{$curr_sejour->entree_prevue|date_format:$dPconfig.datetime}}
    </td>

    <td class="text">
      {{$curr_sejour->sortie_prevue|date_format:$dPconfig.datetime}}
    </td>
    
    <td class="text" {{if !$GHM->_CM}}style="background-color:#fdd"{{/if}}>
      {{$GHM->_GHM}}
      {{if $GHM->_DP}}: {{$GHM->_GHM_nom}}{{/if}}
    </td>
  
    <td class="text">
      {{if $GHM->_DP}}
        {{if $GHM->_borne_basse > $GHM->_duree}}
        <img src="images/icons/cross.png" alt="alerte" />
        Séjour trop court
        {{elseif $GHM->_borne_haute < $GHM->_duree}}
        <img src="images/icons/cross.png" alt="alerte" />
        Séjour trop long
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