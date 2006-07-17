<!-- $Id: $*/ -->

<script type="text/javascript">

function zoom(date, module, action) {
  url = new Url();
  url.setModuleAction("system", "graph_accesslog");
  url.addParam("suppressHeaders", 1);
  url.addParam("size"  , 2);
  url.addParam("date"  , date);
  url.addParam("module", module);
  url.addParam("action", action);
  url.popup(670, 270, date + " " + module + " " + action);
}

function pageMain() {
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">

<tr>
  <th>
  	Logs d'accès du  {{$date|date_format:"%A %d %b %Y"}}
    <img id="changeDate" src="./images/calendar.gif" title="Choisir la date" alt="calendar" />
    <form action="index.php" name="typevue" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <label for="groupmod" title="Type de vue des graphiques">Type de vue</label>
    <select name="groupmod" onchange="this.form.submit()">
      <option value="0"{{if $groupmod == 0}}selected="selected"{{/if}}>Pas de regroupement</option>
      <option value="1"{{if $groupmod == 1}}selected="selected"{{/if}}>Regrouper par module</option>
      <option value="2"{{if $groupmod == 2}}selected="selected"{{/if}}>Regrouper toute la journée</option>
    </select>
    {{if $groupmod == 0}}
    <br />
    <label for="module" title="Type de vue des graphiques">Module</label>
    <select name="module" onchange="this.form.submit()">
      {{foreach from=$listModules item=curr_module}}
      <option value="{{$curr_module.mod_directory}}" {{if $curr_module.mod_directory == $module}} selected="selected" {{/if}}>
        {{$curr_module.mod_ui_name}}
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
          <a href="javascript:zoom('{{$date}}', 0, 0)" title="Agrandir">
            <img src="index.php?m=dPstats&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module=0&amp;action=0" alt="Graphique pour la journée" />
          </a>
          {{elseif $groupmod == 1}}
          <a href="javascript:zoom('{{$date}}', '{{$log->module}}', 0)" title="Agrandir">
            <img src="index.php?m=dPstats&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module={{$log->module}}&amp;action=0" alt="Graphique pour {{$log->module}}" />
          </a>
          {{else}}
          <a href="javascript:zoom('{{$date}}', '{{$log->module}}', '{{$log->action}}')" title="Agrandir">
            <img src="index.php?m=dPstats&amp;a=graph_accesslog&amp;suppressHeaders=1&amp;date={{$date}}&amp;module={{$log->module}}&amp;action={{$log->action}}" alt="Graphique pour {{$log->module}} - {{$log->action}}" />
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

