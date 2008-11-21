<script type="text/javascript">
var series = {{$series|@json}};
var options = {{$options|@json}};

var graph;
Main.add(function () {
  filterForm = getForm('stats-filter');

  // Let's draw the graph
  graph = Flotr.draw(
    $('stats'),
    series, Object.extend({
      bars: {show:true, barWidth:0.5, fillOpacity: 0.6},
      mouse: {track: false},
      legend: {show: false, position: 'nw'},
      yaxis: {min: 0},
      HtmlText: false,
      showDataGrid: true,
      tabGraphLabel: 'Graphique',
      tabDataLabel: 'Données'
    }, options)
  );
});
</script>

<form name="stats-filter" action="?" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  <label for="months_count">Depuis</label>
  <select name="months_count">
    <option value="24" {{if $months_count == 24}}selected="selected"{{/if}}>24 mois</option>
    <option value="12" {{if $months_count == 12}}selected="selected"{{/if}}>12 mois</option>
    <option value="6" {{if $months_count == 6}}selected="selected"{{/if}}>6 mois</option>
  </select>
  
  <label for="filters[chir_id]">Chirurgien</label>
  <select name="filters[chir_id]">
    <option value="">&mdash; Tous</option>
    {{foreach from=$list_chir item=user}}
    <option value="{{$user->_id}}" {{if $filters.chir_id == $user->_id}}selected="selected"{{/if}} style="border-left: #{{$user->_ref_function->color}} 5px solid;">{{$user->_view}}</option>
    {{/foreach}}
  </select>
  
  <label for="filters[anesth_id]">Anésthesiste</label>
  <select name="filters[anesth_id]">
    <option value="">&mdash; Tous</option>
    {{foreach from=$list_anesth item=user}}
    <option value="{{$user->_id}}" {{if $filters.anesth_id == $user->_id}}selected="selected"{{/if}} style="border-left: #{{$user->_ref_function->color}} 5px solid;">{{$user->_view}}</option>
    {{/foreach}}
  </select>
  
  <label for="filters[code_asa]">Code ASA</label>
  <select name="filters[code_asa]">
    <option value="">&mdash; Tous</option>
    {{foreach from=$list_codes_asa item=code}}
    <option value="{{$code}}" {{if $code == $filters.code_asa}}selected="selected"{{/if}}>{{$code}}</option>
    {{/foreach}}
  </select>
  
  <button type="submit" class="search">Filtrer</button>

  <div style="text-align: center;">
    <div id="stats" style="width: 600px; height: 300px; margin: auto;"></div>
    <button onclick="graph.downloadCSV()" type="button" class="submit">Fichier CSV</button>
    <button onclick="graph.selectAllData()" type="button" class="tick">Sélectionner le tableau des données</button>
  </div>
</form>

