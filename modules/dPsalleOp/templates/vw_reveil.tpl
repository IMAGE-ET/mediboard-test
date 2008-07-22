<script type="text/javascript">

function pageMain() {
  
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
  
}
</script>

	<!-- Tabulations -->

	<ul id="main_tab_group" class="control_tabs">
	  <li><a href="#ops">{{tr}}SSPI.Attente{{/tr}} (<span id="liops">0</span>)</a></li>
	  <li><a href="#reveil">{{tr}}SSPI.Reveil{{/tr}} (<span id="lireveil">0</span>)</a></li>
	  <li><a href="#out">{{tr}}SSPI.Sortie{{/tr}} (<span id="liout">0</span>)</a></li>
	  <li style="float:right">
		  <form action="?" name="selection" method="get">
		    <input type="hidden" name="m" value="{{$m}}" />
		      <strong><span id="heure">{{$hour|date_format:"%Hh%M"}}</span> - {{$date|date_format:"%A %d %B %Y"}}
		      </strong>
		    <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />    
	    </form>
	  </li>

	</ul>
<hr class="control_tabs" />

<div id="ops" style="display:none"></div>
<div id="reveil" style="display:none"></div>
<div id="out" style="display:none"></div>
    
<script type="text/javascript">
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab=vw_reveil&date=");
</script>
      