<table class="main">
  <tr>
    <th colspan="2">
      Rapport du {{$date}}
    </th>
  </tr>
  <tr>
    <!-- Répartition des hospitalisés -->
    <td class="text">
      <table class="tbl">
        <tr>
          <th colspan="2">
            Répartition
          </th>
        </tr>
        <tr>
          <th>Service</th>
          <th>Nombre</th>
        </tr>
        {{foreach from=$total_service item="nb_patient" key=nom_service}}
        <tr>
          <td>{{$nom_service}}</td>
          <td>{{$nb_patient}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <!-- Synthèse -->
    <td class="text">
      <table class="tbl">
        <tr>
          <th colspan="4">
            Synthèse
          </th>
        </tr>
        <tr>
          <th>Présents la veille</th>
          <th>Sorties du jour</th>
          <th>Entrées du jour</th>
          <th>Présents du jour</th>
        </tr>
        <tr>        
          <td>{{$listPresentVeille|@count}}</td>
          <td>{{$listSortieJour|@count}}</td>
          <td>{{$listEntreeJour|@count}}</td>
          <td>{{$list_affectations|@count}}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th>Médecins</th>
          <th>Hospitalisés</th>
          <th>Ambulatoires</th>
          <th>Total</th>
        </tr>
        {{foreach from=$totalPrat item="prat" key=nom_prat}}
        {{if $prat.total}}
        <tr>
          <td>{{$nom_prat}}</td>
          <td>{{$prat.hospi}}</td>
          <td>{{$prat.ambu}}</td>
          <td>{{$prat.total}}</td>
        </tr>
        {{/if}}
        {{/foreach}}
      </table>  
    </td>
  </tr>
</table>