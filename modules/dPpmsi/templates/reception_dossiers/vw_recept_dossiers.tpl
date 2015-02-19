{{mb_script module="pmsi" script="reception" ajax=true}}

<script>
  Main.add(function() {
    Reception.form = 'selType';

    var url1 = new Url("pmsi", "ajax_recept_dossiers_month");
    url1.periodicalUpdate('allDossiers', {frequency: 120});

    var url2 = new Url("pmsi", "ajax_recept_dossiers_lines");
    url2.periodicalUpdate('listDossiers', {frequency: 120});
  });
</script>

<table class="main">
  <tr>
    <td>
      <a href="#legend" onclick="Reception.showLegend()" class="button search">Légende</a>
    </td>
    <td style="float: right">
      <form action="?" name="selType" method="get">
        <input type="hidden" name="date" value="{{$date}}" />
        <input type="hidden" name="tri_recept" value="{{$tri_recept}}" />
        <input type="hidden" name="tri_complet" value="{{$tri_complet}}" />
        <input type="hidden" name="order_col" value="{{$order_col}}" />
        <input type="hidden" name="order_way" value="{{$order_way}}" />
        <input type="hidden" name="filterFunction" value="{{$filterFunction}}" />
        <select name="period" onchange="Reception.reloadListDossiers();">
          <option value=""      {{if !$period          }}selected{{/if}}>&mdash; Toute la journée</option>
          <option value="matin" {{if $period == "matin"}}selected{{/if}}>Matin</option>
          <option value="soir"  {{if $period == "soir" }}selected{{/if}}>Soir</option>
        </select>
        {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="Reception.reloadAllReceptDossiers()"}}
        <select name="service_id" onchange="Reception.reloadAllReceptDossiers();" {{if $sejour->service_id|@count > 1}}size="5" multiple="true"{{/if}}>
          <option value="">&mdash; Tous les services</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if in_array($_service->_id, $sejour->service_id)}}selected{{/if}}>{{$_service}}</option>
          {{/foreach}}
        </select>
        <input type="checkbox" onclick="Reception.toggleMultipleServices(this)" {{if $sejour->service_id|@count > 1}}checked{{/if}}/>

        <select name="prat_id" onchange="Reception.reloadAllReceptDossiers();">
          <option value="">&mdash; Tous les praticiens</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$sejour->praticien_id}}
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <td id="allDossiers" style="width: 250px">
    </td>
    <td id="listDossiers" style="width: 100%">
    </td>
  </tr>
</table>