<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create("stats_hospi");
    refreshUSCPO();
  });
  
  refreshUSCPO = function(date_min, date_max, service_id) {
    var url = new Url("dPhospi", "ajax_vw_stats_uscpo");
    if (date_min && date_max) {
      url.addParam("date_min", date_min);
      url.addParam("date_max", date_max);
    }
    if (!Object.isUndefined(service_id)) {
      url.addParam("service_id", service_id);
    }
    url.requestUpdate("uscpo");
    
  }
</script>

<ul id="stats_hospi" class="control_tabs">
  <li>
    <a href="#uscpo" onmousedown="refreshUSCPO()">USCPO prévue/réalisée</a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="uscpo" style="display: none;"></div>
