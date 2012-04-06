{{mb_script module=dPbloc script=blocage}}

<script type="text/javascript">
  viewBlocages = function(blocage_id) {
    var url = new Url("dPbloc", "ajax_vw_blocages");
    if (blocage_id) {
      url.addParam("blocage_id", blocage_id);
    }
    url.requestUpdate("blocages");
  }
  
  viewReplanifications = function(date_replanif) {
    var url = new Url("dPbloc", "ajax_vw_replanifications");
    if (date_replanif) {
      url.addParam("date_replanif", date_replanif);
    }
    url.requestUpdate("replanifs");
  }
  Main.add(function() {
    Control.Tabs.create("tabs_blocage", true);
    var tab_name = Control.Tabs.loadTab("tabs_blocage");
    if (tab_name == "blocages" || !tab_name) {
      viewBlocages('{{$blocage_id}}');
    }
    else {
      viewReplanifications('{{$date_replanif}}');
    }
  });
</script>

<ul id="tabs_blocage" class="control_tabs">
  <li onmousedown="viewBlocages()"><a href="#blocages">Blocages</a></li>
  <li onmousedown="viewReplanifications()"><a href="#replanifs">Replanification</a></li>
</ul>

<hr class="control_tabs" />

<div id="blocages" style="display: none"></div>
<div id="replanifs" style="display: none"></div>
