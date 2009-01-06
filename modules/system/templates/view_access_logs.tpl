<!-- $Id: $*/ -->

<script type="text/javascript">
var graphs = {{$graphs|@json}};
var graphSizes = [
  {width: '350px', height: '250px', yaxisNoTicks: 5},
  {width: '700px', height: '500px', yaxisNoTicks: 10}
];

function drawGraphs(size) {
  var container;
  size = size || graphSizes[0];
  $A(graphs).each(function(g, key) {
  	container = $('graph-'+key);
    container.setStyle(size);
    g.options.y2axis.noTicks = size.yaxisNoTicks;
    g.options.yaxis.noTicks = size.yaxisNoTicks;
    Flotr.draw(container, g.series, g.options);
  });
}

Main.add(function () {
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
  drawGraphs({{if $groupmod == 2}}graphSizes[1]{{/if}});
});
</script>

<table class="main">

<tr>
  <th>
  	Logs d'accès du  {{$date|date_format:"%A %d %b %Y"}}
    <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    
    <form action="?" name="typevue" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      
      <label for="interval" title="Echelle d'affichage">Intervalle</label>
      <select name="interval" onchange="this.form.submit()">
        <option value="day" {{if $interval == "day"}} selected="selected" {{/if}}>Journée</option>
        <option value="month" {{if $interval == "month"}} selected="selected" {{/if}}>Mois</option>
        <option value="hyear" {{if $interval == "hyear"}} selected="selected" {{/if}}>Semestre</option>
        <option value="twoyears" {{if $interval == "twoyears"}} selected="selected" {{/if}}>2 ans</option>
        <option value="twentyyears" {{if $interval == "twentyyears"}} selected="selected" {{/if}}>20 ans</option>
      </select>
      
      <label for="bigsize" title="Afficher en plus grande taille">Grande taille</label>
      <input type="checkbox" name="bigsize" onclick="drawGraphs(graphSizes[this.checked ? 1 : 0])" {{if $groupmod == 2}}checked="checked"{{/if}} />
      <br />
      
      <label for="groupmod" title="Type de vue des graphiques">Type de vue</label>
      <select name="groupmod" onchange="this.form.submit()">
        <option value="0"{{if $groupmod == 0}}selected="selected"{{/if}}>Pas de regroupement</option>
        <option value="1"{{if $groupmod == 1}}selected="selected"{{/if}}>Regrouper par module</option>
        <option value="2"{{if $groupmod == 2}}selected="selected"{{/if}}>Regrouper tout</option>
      </select>

      {{if $groupmod == 0}}
      &gt;
      <label for="module" title="Type de vue des graphiques">Module</label>
      <select name="module" onchange="this.form.submit()">
        {{foreach from=$listModules item=curr_module}}
        <option value="{{$curr_module->mod_name}}" {{if $curr_module->mod_name == $module}} selected="selected" {{/if}}>
          {{tr}}module-{{$curr_module->mod_name}}-court{{/tr}}
        </option>
        {{/foreach}}
      </select>
      {{/if}}
    </form>
  </th>
</tr>

<tr>
  <td>
    {{foreach from=$graphs item=graph name=graphs}}
        <div id="graph-{{$smarty.foreach.graphs.index}}" style="width: 350px; height: 250px; float: left; margin: 1em;"></div>
    {{/foreach}}
  </td>
</tr>

</table>

