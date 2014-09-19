<script>
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs-configure', true);
    if (tabs.activeLink.key == "CConfigEtab") {
      Configuration.edit('dPpmsi', ['CGroups', 'CService CGroups.group_id'], $('CConfigEtab'));
    }
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#PMSI">{{tr}}PMSI{{/tr}}</a></li>
  <li><a href="#Export">{{tr}}GHS{{/tr}}</a></li>
  <li><a href="#Repair">{{tr}}config_facture_hprim{{/tr}}</a></li>
  <li onmousedown="Configuration.edit('dPpmsi', 'CGroups', $('CConfigEtab'))">
    <a href="#CConfigEtab">Config par établissement</a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="PMSI" style="display: none;">
{{mb_include template=inc_config_pmsi}}
</div>

<div id="Export" style="display: none;">
{{mb_include template=inc_config_ghs}}
</div>

<div id="Repair" style="display: none;">
{{mb_include template=inc_config_facture_hprim}}
</div>

<div id="CConfigEtab" style="display: none"></div>