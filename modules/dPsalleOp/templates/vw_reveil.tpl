
{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}
<script type="text/javascript">

Main.add(function () {
  new Control.Tabs('main_tab_group');
  var opsUpdater = new Url;
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_reveil_ops");
  opsUpdater.addParam("date", "{{$date}}");
  opsUpdater.periodicalUpdate('ops', { frequency: 90 });
  
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_reveil_reveil");
  opsUpdater.addParam("date","{{$date}}");
  opsUpdater.requestUpdate("reveil", {waitingText: null});
  
  opsUpdater.setModuleAction("dPsalleOp", "httpreq_reveil_out");
  opsUpdater.addParam("date","{{$date}}");
  opsUpdater.requestUpdate("out", {waitingText: null});
  
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_reveil&date=");
});
</script>

	<!-- Tabulations -->
	<ul id="main_tab_group" class="control_tabs">
	  {{if $dPconfig.dPsalleOp.CReveil.multi_tabs_reveil == 1}}
	  	<li><a href="#ops">{{tr}}SSPI.Attente{{/tr}} (<span id="liops">0</span>)</a></li>
	  	<li><a href="#reveil">{{tr}}SSPI.Reveil{{/tr}} (<span id="lireveil">0</span>)</a></li>
	  	<li><a href="#out">{{tr}}SSPI.Sortie{{/tr}} (<span id="liout">0</span>)</a></li>
	  {{else}}
	  	<li><a href="#all">{{tr}}SSPI.Attente{{/tr}} / {{tr}}SSPI.Reveil{{/tr}} / {{tr}}SSPI.Sortie{{/tr}}</a></li>
	  {{/if}}
	  <li style="float:right; font-weight: bold;">
		  <form action="?" name="selection" method="get">
		    <input type="hidden" name="m" value="{{$m}}" />
		      <span id="heure">{{$hour|date_format:"%Hh%M"}}</span> - {{$date|date_format:"%A %d %B %Y"}}
		    <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />    
	    </form>
	  </li>
	</ul>
	
<hr class="control_tabs" />

{{if $dPconfig.dPsalleOp.CReveil.multi_tabs_reveil == 1}}
<div id="ops" style="display:none"></div>
<div id="reveil" style="display:none"></div>
<div id="out" style="display:none"></div>
{{else}}
<table id="all" class="main">
	<tr>
		<th class="title" style="width: 40%">{{tr}}SSPI.Attente{{/tr}} (<span id="liops">0</span>)</th>
    <th class="title" style="width: 60%">{{tr}}SSPI.Reveil{{/tr}} (<span id="lireveil">0</span>)</th>
  </tr>
  <tr>
		<td id="ops" rowspan="3"></td>
    <td id="reveil"></td>
  </tr>
  <tr>
    <th class="title">{{tr}}SSPI.Sortie{{/tr}} (<span id="liout">0</span>)</th>
  </tr>
  <tr>
		<td id="out"></td>
	</tr>
</table>
{{/if}}
      