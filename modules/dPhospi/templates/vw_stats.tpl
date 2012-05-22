<script type="text/javascript">
  Main.add(function() {
    var tabs = Control.Tabs.create("stats_hospi", true);
    refreshStats(tabs.activeLink.key);
  });
  
  refreshStats = function(type, date_min, date_max, service_id, options) {
    var url = new Url("hospi", "ajax_vw_stats_" + type);
    if (date_min && date_max) {
      url.addParam("date_min", date_min);
      url.addParam("date_max", date_max);
    }
    if (!Object.isUndefined(service_id)) {
      url.addParam("service_id", service_id);
    }
    url.requestUpdate(type);
  }
  
  filtreOccupation = function() {
    var oForm = getForm("filter_occupation"); 
    var url = new Url("hospi", "ajax_vw_stats_occupation");
    if (oForm.elements["display_stat[ouvert]"].checked) {
      url.addParam("display_stat[ouvert]",  1);
    }
    if (oForm.elements["display_stat[prevu]"].checked) {
      url.addParam("display_stat[prevu]", 1);
    }
    if (oForm.elements["display_stat[reel]"].checked) {
      url.addParam("display_stat[reel]", 1);
    }
    if (oForm.elements["display_stat[entree]"].checked) {
      url.addParam("display_stat[entree]", 1);
    }
    url.requestUpdate("occupation");
  }
  
  listOperations = function(date, service_id) {
    var url = new Url("hospi", "ajax_stat_list_operations");
    url.addParam("date", date);
    url.addParam("service_id", service_id);
    url.requestUpdate("list_operations_uscpo");
  }
  
  viewLegend = function() {
    var url = new Url("hospi", "ajax_stat_legend");
    url.requestModal();
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
