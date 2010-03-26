<script type="text/javascript">
function viewGeneral() {
  $("general").show();
	$("current").hide();
}

function viewCurrent() {
  $("current").show();
	$("general").hide();
}

Main.add(function () {
  searchTabs = new Control.Tabs('main_tab_group');
  // Au chargement, vue de l'établissement courant
	viewGeneral();
});
</script>

<ul id="main_tab_group" class="control_tabs">
  <li><a href="#general">Général</a></li>
	{{if $nb_etabs > 1}}
	  <li><a href="#current">{{$etab->_view}}</a></li>
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
	      <label title="{{$curr_result.Update_time|date_format:$dPconfig.datetime}}">
	      	{{$relative.count}} {{tr}}{{$relative.unit}}{{if $relative.count > 1}}s{{/if}}{{/tr}}
	      </label>
	    </td>
	  </tr>
	  {{/foreach}}
	</table>
</div>
{{if $nb_etabs}}
	<div id="current" style="display: none;">
	  <table class="tbl main">
	    <tr>
	      <th width="50%">Type de données</th>
	      <th width="50%">Quantité</th>
	    </tr>
			{{foreach from=$res_current_etab item=curr_res key=field_res}}
			<tr>
				<td>{{tr}}{{$field_res}}{{/tr}}</td>
				<td>{{$curr_res}}</td>
	    </tr>
			{{/foreach}}
		</table>
	</div>
{{/if}}