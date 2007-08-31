<table class="main">
  <tr>
    <th colspan="2">
      Rapport du {{$date|date_format:"%d/%m/%Y"}}
    </th>
  </tr>
  <tr>
    <!-- Répartition des hospitalisés -->
    <td class="text">
      <table class="tbl">
        <tr>
          <th colspan="2">
            Répartition des hospitalisés présents par service
          </th>
        </tr>
        <tr>
          <th>Service</th>
          <th>Nombre</th>
        </tr>
        {{foreach from=$total_service item="nb_patient" key=nom_service}}
        <tr style="text-align: center">
          <td>{{$nom_service}}</td>
          <td>{{$nb_patient}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <!-- Synthèse -->
    <td class="text">
      <table class="tbl" style="text-align: center">
        <tr>
          <th colspan="4">
            Synthèse
          </th>
        </tr>
        <tr>
          <th style:"width=50px">Présents la veille</th>
          <td>{{$listPresentVeille|@count}}</td>
        </tr>
          <th>Sorties du jour</th>
          <td>{{$listSortieJour|@count}}</td>
        </tr>
          <th>Entrées du jour</th>
          <td>{{$listEntreeJour|@count}}</td>
        </tr>
          <th>Présents du jour</th>
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
          <th>Total par médecins</th>
        </tr>
        {{foreach from=$totalPrat item="prat" key=nom_prat}}
        {{if $prat.total}}
        <tr style="text-align: center">
          <td>{{$nom_prat}}</td>
          <td>{{$prat.hospi}}</td>
          <td>{{$prat.ambu}}</td>
          <td>{{$prat.total}}</td>
        </tr>
        {{/if}}
        {{/foreach}}
        <tr>
          <th>Total</th>
          <th>{{$totalHospi}}</th>
          <th>{{$totalAmbulatoire}}</th>
          <th>{{$totalMedecin}}</th>
        </tr>  
      </table>  
    </td>
  </tr>
</table>