<table class="main">
  <tr>
    <th colspan="2">
      <a href="#" onclick="print()">Rapport du {{$date|date_format:"%d/%m/%Y"}}</a>
      <br />
      <form name="chgMode" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="a" value="{{$a}}" />
      <input type="hidden" name="dialog" value="1" />
      <input type="hidden" name="date" value="{{$date}}" />
      Type d'affichage :
      <select name="rapport_reel" onchange="this.form.submit();">
        <option value="1" {{if $reel}}selected="selected"{{/if}}>Rapport réel</option>
        <option value="0" {{if !$reel}}selected="selected"{{/if}}>Rapport prévisionnel</option>
      </select>
      </form>
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
        {{foreach from=$total_service item="service"}}
        <tr style="text-align: center">
          <td>{{$service.service->_view}}</td>
          <td>{{$service.total}}</td>
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
        <tr>
          <th>Sorties du jour</th>
          <td>{{$listSortieJour|@count}}</td>
        </tr>
        <tr>
          <th>Entrées du jour</th>
          <td>{{$listEntreeJour|@count}}</td>
        </tr>
        <tr>
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
        {{foreach from=$totalPrat item="prat"}}
        {{if $prat.total}}
        <tr style="text-align: center">
          <td>{{$prat.prat->_view}}</td>
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