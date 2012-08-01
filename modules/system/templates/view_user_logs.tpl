
<script type="text/javascript">
var graphs = {{$graphs|@json}};
var graphSizes = [
  {width: '400px', height: '250px', yaxisNoTicks: 5},
  {width: '700px', height: '500px', yaxisNoTicks: 10}
];

function yAxisTickFormatter(val) {
  return Flotr.engineeringNotation(val, 2, 1000);
}

function drawGraphs(size) {
  var container;
  size = size || graphSizes[0];
  $A(graphs).each(function(g, key) {
    container = $('graph-'+key);
    container.setStyle(size);
    g.options.y2axis.noTicks = size.yaxisNoTicks;
    g.options.yaxis.noTicks = size.yaxisNoTicks;
    g.options.yaxis.tickFormatter = yAxisTickFormatter;
    g.options.y2axis.tickFormatter = yAxisTickFormatter;
    var f = Flotr.draw(container, g.series, g.options);
    console.log(g);
    
    {{if $groupmod==1}}
    f.overlay.setStyle({cursor: 'pointer'})
             .observe('click', function(m){return function(){$V(getForm('typevue').elements.groupmod, m)}}(g.module));
    {{/if}}
  });
}

Main.add(function () {
  Calendar.regField(getForm("typevue").date, null, {noView: true});
  drawGraphs({{if $groupmod == 2}}graphSizes[1]{{/if}});
});
</script>

<table class="main">

<tr>
  <th>
    <form action="?" name="typevue" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      
      Logs d'accès du {{$date|date_format:$conf.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      
      <label for="interval" title="Echelle d'affichage">Intervalle</label>
      <select name="interval" onchange="this.form.submit();">
        <option value="day"          {{if $interval == "day"}}         selected="selected" {{/if}}>Journée </option>
        <option value="month"        {{if $interval == "month"}}       selected="selected" {{/if}}>Mois    </option>
        <option value="hyear"        {{if $interval == "hyear"}}       selected="selected" {{/if}}>Semestre</option>
        <option value="twoyears"     {{if $interval == "twoyears"}}    selected="selected" {{/if}}>2 ans   </option>
        <option value="twentyyears"  {{if $interval == "twentyyears"}} selected="selected" {{/if}}>20 ans  </option>
      </select>
      
      {{if $interval == "day"}} 
        <label for="hour_min" title="Heure minimale">{{tr}}From{{/tr}}</label>
        <select name="hour_min" onchange="this.form.submit()">
          {{foreach from=$hours item=_hour}}
            <option value="{{$_hour}}" {{if $hour_min == $_hour}} selected="selected" {{/if}}>
              {{$_hour|pad:2:0}}h
            </option>
          {{/foreach}}
        </select>
  
        <label for="hour_max" title="Heure maximale">{{tr}}To{{/tr}}</label>
        <select name="hour_max"  onchange="this.form.submit()">
          {{foreach from=$hours item=_hour}}
            <option value="{{$_hour}}" {{if $hour_max == $_hour}} selected="selected" {{/if}}>
              {{$_hour|pad:2:0}}h
            </option>
          {{/foreach}}
        </select>
      {{/if}}
      
      <label for="bigsize" title="Afficher en plus grande taille">Grande taille</label>
      <input type="checkbox" name="bigsize" onclick="drawGraphs(graphSizes[this.checked ? 1 : 0])" {{if $groupmod == 2}}checked="checked"{{/if}} />
      <br />
      
      <label for="groupmod" title="Type de vue des graphiques">Type de vue</label>
      <select name="groupmod" onchange="this.form.submit()">
        <option value="1" {{if $groupmod == 1}}selected="selected"{{/if}}>Regrouper par module</option>
        <option value="2" {{if $groupmod == 2}}selected="selected"{{/if}}>Regrouper tout</option>
        <optgroup label="Détail du module">
          {{foreach from=$listModules item=curr_module}}
            <option value="{{$curr_module->mod_name}}" {{if $curr_module->mod_name == $module}} selected="selected" {{/if}}>
              {{tr}}module-{{$curr_module->mod_name}}-court{{/tr}}
            </option>
          {{/foreach}}
        </optgroup>
      </select>
      
      <div>
        Données :
        <select name="left_mode" onchange="this.form.submit()">
          <option value="counts" {{if $left_mode == 'counts'}}selected="selected"{{/if}}>Counts</option>
        </select>
        <select name="left_sampling" onchange="this.form.submit()">
          <option value="total" {{if $left_sampling == 'total'}}selected="selected"{{/if}}>Total</option>
        </select>
      </div>
    </form>
  </th>
</tr>

<tr>
  <td colspan="2">
  {{foreach from=$graphs item=graph name=graphs}}
    <div id="graph-{{$smarty.foreach.graphs.index}}" style="width: 350px; height: 250px; float: left; margin: 1em;"></div>
  {{/foreach}}
  </td>
</tr>

</table>

