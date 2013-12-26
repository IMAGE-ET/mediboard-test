{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
    g.options.yaxis.tickFormatter  = yAxisTickFormatter;
    g.options.y2axis.tickFormatter = yAxisTickFormatter;
    g.options.mouse                = {
      track: true,
      position: "ne",
      relative: true,
      sensibility: 2,
      trackFormatter: function (obj) {
        return "Valeur : " + obj.y + "<br />Date : " + g.datetime_by_index[obj.index];
      }
    };
    var f = Flotr.draw(container, g.series, g.options);
    
    {{if $groupmod==1}}
    f.overlay.setStyle({cursor: 'pointer'})
             .observe('click', function(m){return function(){$V(getForm('typevue').groupmod, m)}}(g.module));
    {{/if}}
  });
}

var AccessLog = {
  crazyLogs: function(mode) {
    Control.Modal.close();  
    var url = new Url('system', 'crazy_logs');
    url.addParam('mode', mode);
    url.requestModal(400);
  },

  viewAggregateLogs: function() {
    Control.Modal.close();
    var url = new Url('system', 'view_aggregate_access_logs');
    url.requestModal(400);
  },

  aggregate: function(dry_run) {
    var url = new Url('system', 'aggregate_access_logs');

    if (dry_run) {
      url.addParam('dry_run', 1);
      url.requestUpdate("dry_run");
    }
    else {
      Modal.confirm("Agréger ?", {onValidate: function(v) { if (v) {url.requestUpdate("aggregate")} } });
    }
  }
};

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
        <option value="one-day"      {{if $interval == "one-day"     }} selected="selected" {{/if}}>1 jour     (par 10mn)    </option>
        <option value="one-week"     {{if $interval == "one-week"    }} selected="selected" {{/if}}>1 semaine  (par heure)   </option>
        <option value="height-weeks" {{if $interval == "height-weeks"}} selected="selected" {{/if}}>8 semaines (par jour)    </option>
        <option value="one-year"     {{if $interval == "one-year"    }} selected="selected" {{/if}}>1 an       (par semaine) </option>
        <option value="four-years"   {{if $interval == "four-years"  }} selected="selected" {{/if}}>4 ans      (par mois)    </option>
        <option value="twenty-years" {{if $interval == "twenty-years"}} selected="selected" {{/if}}>20 ans     (par an)      </option>
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
      {{if $DBorNotDB}}
        <button type="button" class="lookup" onclick="this.form.DBorNotDB_hidden.value = '0'; this.form.submit()">Vue standard</button>
        <input type="hidden" name="DBorNotDB_hidden" value="1" />
      {{else}}
        <button type="button" class="lookup" onclick="this.form.DBorNotDB_hidden.value = '1'; this.form.submit()">Détails des sources de données</button>
        <input type="hidden" name="DBorNotDB_hidden" value="0" />
      {{/if}}
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

      <label for="human_bot" title="Filtrage en fonction du type d'utilisateur">Visualiser</label>
      <select name="human_bot" onchange="this.form.submit()">
        <option value="0" {{if $human_bot === '0'}}selected="selected"{{/if}}>Humains</option>
        <option value="1" {{if $human_bot === '1'}}selected="selected"{{/if}}>Robots</option>
        <option value="2" {{if $human_bot === '2'}}selected="selected"{{/if}}>Les deux</option>
      </select>
      <br />
      
      {{if !$DBorNotDB}}
      <div>
        Gauche:
        <select name="left_mode" onchange="this.form.submit()">
          <option value="request_time" {{if $left_mode == 'request_time'}}selected="selected"{{/if}}>Temps de réponse</option>
          <option value="cpu_time"     {{if $left_mode == 'cpu_time'}}    selected="selected"{{/if}}>Temps CPU</option>
          <option value="errors"       {{if $left_mode == 'errors'}}      selected="selected"{{/if}}>Erreurs</option>
          <option value="memory_peak"  {{if $left_mode == 'memory_peak'}} selected="selected"{{/if}}>Pics de mémoire</option>
          <option value="nb_requests"  {{if $left_mode == 'nb_requests'}} selected="selected"{{/if}}>Requêtes SQL</option>
        </select>
        <select name="left_sampling" onchange="this.form.submit()">
          <option value="total" {{if $left_sampling == 'total'}}selected="selected"{{/if}}>Total</option>
          <option value="mean"  {{if $left_sampling == 'mean'}} selected="selected"{{/if}}>par hit</option>
        </select>
        
        Droite:
        <select name="right_mode" onchange="this.form.submit()">
          <option value="hits" {{if $right_mode == 'hits'}}selected="selected"{{/if}}>Hits</option>
          <option value="size" {{if $right_mode == 'size'}}selected="selected"{{/if}}>Bande passante</option>
        </select>
        <select name="right_sampling" onchange="this.form.submit()">
          <option value="total" {{if $right_sampling == 'total'}}selected="selected"{{/if}}>Total</option>
          <option value="mean"  {{if $right_sampling == 'mean'}} selected="selected"{{/if}}>par unité de temps</option>
        </select>
      </div>
      {{/if}}
    </form>
  </th>
  <td>
    {{if $can->admin}}
      <button class="search" type="button" onclick="AccessLog.crazyLogs('find')">Logs douteux</button>
      <br />
      <button class="search" type="button" onclick="AccessLog.viewAggregateLogs()">{{tr}}Aggregate{{/tr}}</button>
    {{/if}}
  </td>
</tr>

<tr>
  <td colspan="2">
  {{foreach from=$graphs item=graph name=graphs}}
    <div id="graph-{{$smarty.foreach.graphs.index}}" style="width: 350px; height: 250px; float: left; margin: 1em;"></div>
  {{/foreach}}
  </td>
</tr>

</table>

