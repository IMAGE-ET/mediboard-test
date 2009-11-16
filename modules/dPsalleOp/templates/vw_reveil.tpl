{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}


{{if $dPconfig.dPsalleOp.CDailyCheckList.active_salle_reveil != '1' || 
     $date < $smarty.now|date_format:"%Y-%m-%d" || 
     $check_list->_id && $check_list->validator_id}}
		 
<script type="text/javascript">
	
var updater_encours = null;
var updater_ops = null;
	
Main.add(function () {
  new Control.Tabs('reveil_tabs');
	
  var url = new Url;
  url.addParam("bloc_id", "{{$bloc->_id}}");
  url.addParam("date", "{{$date}}");
	
  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "encours");
  updater_encours = url.periodicalUpdate("encours", { frequency: 90 });  // Laisser la variable updater_encours, utile dans inc_edit_check_list.tpl

  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "ops");
  updater_ops = url.periodicalUpdate("ops", { frequency: 90 });  // Laisser la variable updater_ops, utile dans inc_edit_check_list.tpl

  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "reveil");
  url.requestUpdate("reveil", {waitingText: null});

  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "out");
  url.requestUpdate("out", {waitingText: null});
  
  Calendar.regField(getForm("selection").date, null, {noView: true});
});

function refreshTabsReveil() {
  var url = new Url;
	
	url.addParam("bloc_id", "{{$bloc->_id}}");
  url.addParam("date", "{{$date}}");
	
  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "encours");
  url.requestUpdate("encours", {waitingText : null});
	
  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "ops");
  url.requestUpdate("ops", {waitingText : null});
	
  url.setModuleAction("dPsalleOp", "httpreq_reveil");
	url.addParam("type", "reveil");
  url.requestUpdate("reveil", {waitingText : null});
	
  url.setModuleAction("dPsalleOp", "httpreq_reveil");
  url.addParam("type", "out");
  url.requestUpdate("out", {waitingText : null});
}

</script>

		 
	<ul id="reveil_tabs" class="control_tabs">
	  {{if $dPconfig.dPsalleOp.CReveil.multi_tabs_reveil == 1}}
		  <li><a href="#encours">{{tr}}SSPI.Encours{{/tr}} (<span id="liencours">0</span>)</a></li>
	  	<li><a href="#ops">{{tr}}SSPI.Attente{{/tr}} (<span id="liops">0</span>)</a></li>
	  	<li><a href="#reveil">{{tr}}SSPI.Reveil{{/tr}} (<span id="lireveil">0</span>)</a></li>
	  	<li><a href="#out">{{tr}}SSPI.Sortie{{/tr}} (<span id="liout">0</span>)</a></li>
	  {{else}}
	  	<li><a href="#all">{{tr}}SSPI.Encours{{/tr}} / {{tr}}SSPI.Attente{{/tr}} / {{tr}}SSPI.Reveil{{/tr}} / {{tr}}SSPI.Sortie{{/tr}}</a></li>
	  {{/if}}
	  <li style="float:right; font-weight: bold;">
		  <form action="?" name="selection" method="get">
		    <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="tab" value="vw_reveil" />
		    <span id="heure">{{$hour|date_format:$dPconfig.time}}</span> - {{$date|date_format:$dPconfig.longdate}}
	      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
	      <select name="bloc_id" onchange="this.form.submit();">
	        <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
	        {{foreach from=$blocs_list item=curr_bloc}}
	          <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
	            {{$curr_bloc->nom}}
	          </option>
	        {{foreachelse}}
	          <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
	        {{/foreach}}
	      </select>
	    </form>
	  </li>
	</ul>
		
	<hr class="control_tabs" />
	
	{{if $dPconfig.dPsalleOp.CReveil.multi_tabs_reveil == 1}}
		<div id="encours" style="display:none"></div>
		<div id="ops" style="display:none"></div>
		<div id="reveil" style="display:none"></div>
		<div id="out" style="display:none"></div>
	{{else}}
		<table id="all" class="main">
			<tr>
				<th class="title" style="width: 40%">{{tr}}SSPI.Encours{{/tr}} (<span id="liencours">0</span>)</th>
		    <th class="title" style="width: 60%">{{tr}}SSPI.Reveil{{/tr}} (<span id="lireveil">0</span>)</th>
		  </tr>
		  <tr>
				<td id="encours"></td>
		    <td id="reveil"></td>
		  </tr>
		  <tr>
		    <th class="title">{{tr}}SSPI.Attente{{/tr}} (<span id="liops">0</span>)</th>
		    <th class="title">{{tr}}SSPI.Sortie{{/tr}} (<span id="liout">0</span>)</th>
		  </tr>
		  <tr>
				<td id="ops"></td>
				<td id="out"></td>
			</tr>
		</table>
	{{/if}}
{{else}}

<script type="text/javascript">

	Main.add(function () {
	  Calendar.regField(getForm("selection").date, null, {noView: true});
	});

</script>

  <div style="text-align: center">
    <form action="?" name="selection" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="vw_reveil" />
      <span id="heure">{{$hour|date_format:$dPconfig.time}}</span> - {{$date|date_format:$dPconfig.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      <select name="bloc_id" onchange="this.form.submit();">
        <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
        {{foreach from=$blocs_list item=curr_bloc}}
          <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
            {{$curr_bloc->nom}}
          </option>
        {{foreachelse}}
          <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
        {{/foreach}}
      </select>
    </form>
		</div>
		
  {{include file=inc_edit_check_list.tpl personnel=$personnels}}
{{/if}}