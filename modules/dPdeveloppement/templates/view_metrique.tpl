<script type="text/javascript">
function viewGeneral() {
  $("general").show();
  $("current").hide();
}

function viewCurrent() {
  var url = new Url("dPdeveloppement", "view_metrique");
  url.addParam("view_current", 1);
  url.requestUpdate("current");
}

Main.add(function () {
  searchTabs = Control.Tabs.create('main_tab_group');
  {{if $nb_etabs > 1}}
    viewGeneral();
  {{/if}}
});
</script>

<ul id="main_tab_group" class="control_tabs">
  <li><a href="#general">Général</a></li>
  {{if $nb_etabs > 1}}
    <li onmousedown="viewCurrent();"><a href="#current">{{$etab->_view}}</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="general" style="display: none;">
  <table class="tbl main">
    <tr>
      <th>Type de données</th>
      <th>Quantité</th>
      <th>Dernière mise à jour</th>
    </tr>
    {{foreach from=$result item=curr_result key=class}}
    <tr>
      <td>{{tr}}{{$class}}{{/tr}}</td>
      <td>{{$curr_result.Rows}}</td>
      <td>
        {{assign var=relative value=$curr_result.Update_relative}}
        <label title="{{$curr_result.Update_time|date_format:$conf.datetime}}">
          {{$relative.count}} {{tr}}{{$relative.unit}}{{if $relative.count > 1}}s{{/if}}{{/tr}}
        </label>
      </td>
    </tr>
    {{/foreach}}
  </table>
</div>

{{if $nb_etabs > 1}}
  <div id="current" style="display: none;"></div>
{{/if}}