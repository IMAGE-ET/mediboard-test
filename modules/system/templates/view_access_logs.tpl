<!-- $Id: $*/ -->

<script type="text/javascript">

function zoom(date, module, action, interval) {
  url = new Url();
  url.setModuleAction("dPstats", "graph_accesslog");
  url.addParam("suppressHeaders", 1);
  url.addParam("size"  , 2);
  url.addParam("date"  , date);
  url.addParam("module", module);
  url.addParam("actionName", action);
  url.addParam("interval", interval);
  url.popup(670, 270, date + " " + module + " " + action);
}

Main.add(function () {
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
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
    </select>
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
    <table class="tbl">
      {{foreach from=$logs item=log}}
      {{cycle values="0,1,2" assign=tr}}
      {{if $tr == 0}}
      <tr>
      {{/if}}
        <td class="button">
          {{if $groupmod == 2}}
          <a href="#" onclick="zoom('{{$date}}', 0, 0, '{{$interval}}')" title="Agrandir">
            <img src="?m=dPstats&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module=0&amp;actionName=0&amp;interval={{$interval}}" alt="Graphique pour la journée" />
          </a>
          {{elseif $groupmod == 1}}
          <a href="#" onclick="zoom('{{$date}}', '{{$log->module}}', 0, '{{$interval}}')" title="Agrandir">
            <img src="?m=dPstats&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module={{$log->module}}&amp;actionName=0&amp;interval={{$interval}}" alt="Graphique pour {{$log->module}}" />
          </a>
          {{else}}
          <a href="#" onclick="zoom('{{$date}}', '{{$log->module}}', '{{$log->action}}', '{{$interval}}')" title="Agrandir">
            <img src="?m=dPstats&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module={{$log->module}}&amp;actionName={{$log->action}}&amp;interval={{$interval}}" alt="Graphique pour {{$log->module}} - {{$log->action}}" />
          </a>
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

