<!-- $Id: $*/ -->

<script type="text/javascript">

function zoom(date, module, element, interval, numelem) {
  url = new Url();
  url.setModuleAction("dPstats", "graph_ressourceslog");
  url.addParam("suppressHeaders", 1);
  url.addParam("size"           , 2);
  url.addParam("date"           , date);
  url.addParam("module"         , module);
  url.addParam("element"        , element);
  url.addParam("interval"       , interval);
  url.addParam("numelem"        , numelem);
  url.popup(500, 445, date + " " + module + " " + element);
}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">

<tr>
  <th>
  	Logs d'accès du  {{$date|date_format:"%A %d %b %Y"}}
    <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    <form action="index.php" name="typevue" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <label for="interval" title="Echelle d'affichage">Intervalle</label>
    <select name="interval" onchange="this.form.submit()">
      <option value="day" {{if $interval == "day"}} selected="selected" {{/if}}>Journée</option>
      <option value="month" {{if $interval == "month"}} selected="selected" {{/if}}>Mois</option>
      <option value="hyear" {{if $interval == "hyear"}} selected="selected" {{/if}}>Semestre</option>
    </select>
    &mdash;
    <label for="numelem" title="Nombre maximum d'éléments à afficher">Eléments maximums</label>
    <input type="text" name="numelem" value="{{$numelem}}" size="2" />
    <br />
    <label for="element" title="Choix de la mesure">Type de mesure</label>
    <select name="element" onchange="this.form.submit()">
      <option value="duration"{{if $element == "duration"}}selected="selected"{{/if}}>Durée totale (php + DB)</option>
      <option value="request"{{if $element == "request"}}selected="selected"{{/if}}>Durée DB</option>
    </select>
    &mdash;
    <label for="groupres" title="Type de vue des graphiques">Type de vue</label>
    <select name="groupres" onchange="this.form.submit()">
      <option value="0"{{if $groupres == 0}}selected="selected"{{/if}}>Regrouper par module</option>
      <option value="1"{{if $groupres == 1}}selected="selected"{{/if}}>Regrouper tout</option>
    </select>
    </form>
  </th>
</tr>

<tr>
  <td>
    <table class="tbl">
      {{foreach from=$logs item=log}}
      {{cycle values="0,1,2" assign=tr}}
      {{if $tr == 0}}
      <tr>
      {{/if}}
        <td class="button">
        {{if $dPconfig.graph_engine == "eZgraph"}}
          {{if $groupres == 1}}
          <a href="#" onclick="zoom('{{$date}}', 0, '{{$element}}', '{{$interval}}', '{{$numelem}}')" title="Agrandir">
			<object data="index.php?m=dPstats&amp;a=graph_ressourceslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module=0&amp;element={{$element}}&amp;interval={{$interval}}&amp;numelem={{$numelem}}" type="image/svg+xml">
			        You need a browser capeable of SVG to display this image.
			</object>
          </a>
          </td>
          <td class="button">
          <a href="#" onclick="zoom('{{$date}}', 'total', '{{$element}}', '{{$interval}}', '{{$numelem}}')" title="Agrandir">
          	<object data="index.php?m=dPstats&amp;a=graph_ressourceslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module=total&amp;element={{$element}}&amp;interval={{$interval}}&amp;numelem={{$numelem}}" type="image/svg+xml">
			        You need a browser capeable of SVG to display this image.
			</object>
          </a>
          {{else}}
          <a href="#" onclick="zoom('{{$date}}', '{{$log->module}}', '{{$element}}', '{{$interval}}', '{{$numelem}}')" title="Agrandir">
            <object data="index.php?m=dPstats&amp;a=graph_ressourceslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module={{$log->module}}&amp;element={{$element}}&amp;interval={{$interval}}&amp;numelem={{$numelem}}" type="image/svg+xml">
			        You need a browser capeable of SVG to display this image.
			</object>
          </a>
          {{/if}}
         {{else}}
          {{if $groupres == 1}}
          <a href="#" onclick="zoom('{{$date}}', 0, '{{$element}}', '{{$interval}}', '{{$numelem}}')" title="Agrandir">
           	<img src="index.php?m=dPstats&amp;a=graph_ressourceslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module=0&amp;element={{$element}}&amp;interval={{$interval}}&amp;numelem={{$numelem}}" alt="Graphique pour la journée" /> 
          </a>
          </td>
          <td class="button">
          <a href="#" onclick="zoom('{{$date}}', 'total', '{{$element}}', '{{$interval}}', '{{$numelem}}')" title="Agrandir">
            <img src="index.php?m=dPstats&amp;a=graph_ressourceslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module=total&amp;element={{$element}}&amp;interval={{$interval}}&amp;numelem={{$numelem}}" alt="Graphique pour la journée" />
          </a>
          {{else}}
          <a href="#" onclick="zoom('{{$date}}', '{{$log->module}}', '{{$element}}', '{{$interval}}', '{{$numelem}}')" title="Agrandir">
            <img src="index.php?m=dPstats&amp;a=graph_ressourceslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module={{$log->module}}&amp;element={{$element}}&amp;interval={{$interval}}&amp;numelem={{$numelem}}" alt="Graphique pour {{$log->module}}" />
          </a>
          {{/if}}
         {{/if}}
        </td>
      {{if $tr == 2}}
      </tr>
      {{/if}}
      {{/foreach}}
    </table>
  </td>
</tr>
</table>

