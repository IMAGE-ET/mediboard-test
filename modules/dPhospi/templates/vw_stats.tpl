<script type="text/javascript">
  Main.add(function() {
    var tabs = Control.Tabs.create("stats_hospi", true);
    refreshStats(tabs.activeLink.key);
  });
  
  refreshStats = function(type, date_min, date_max, service_id) {
    var url = new Url("dPhospi", "ajax_vw_stats_" + type);
    if (date_min && date_max) {
      url.addParam("date_min", date_min);
      url.addParam("date_max", date_max);
    }
    if (!Object.isUndefined(service_id)) {
      url.addParam("service_id", service_id);
    }
    url.requestUpdate(type);
  }
  
  listOperations = function(date, service_id) {
    var url = new Url("dPhospi", "ajax_stat_list_operations");
    url.addParam("date", date);
    url.addParam("service_id", service_id);
    url.requestUpdate("list_operations_uscpo");
  }
</script>

<ul id="stats_hospi" class="control_tabs">
  <li>
    <a href="#uscpo" onmousedown="refreshStats('uscpo')">USCPO prévue / réalisée</a>
  </li>
  <li>
    <a href="#occupation" onmousedown="refreshStats('occupation')">Occupation prévue / réalisée</a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="uscpo" style="display: none;"></div>
<div id="occupation" style="display: none;"></div>
