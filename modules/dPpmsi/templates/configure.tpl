{{mb_script module=pmsi script=PMSI}}

<script>
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs-configure', true);
    if (tabs.activeLink.key == "CConfigEtab") {
      Configuration.edit('dPpmsi', ['CGroups', 'CService CGroups.group_id'], $('CConfigEtab'));
    }
    {{if "atih"|module_active}}
      if (tabs.activeLink.key == "Config-UM") {
        PMSI.loadConfigUms('{{$g}}');
      }
    {{/if}}
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#PMSI">{{tr}}PMSI{{/tr}}</a></li>
  <li><a href="#Repair">{{tr}}config_facture_hprim{{/tr}}</a></li>
  <li onmousedown="Configuration.edit('dPpmsi', 'CGroups', $('CConfigEtab'))">
    <a href="#CConfigEtab">{{tr}}CConfigEtab{{/tr}}</a>
  </li>
  {{if "atih"|module_active}}
    <li onmousedown="PMSI.loadConfigUms('{{$g}}')"><a href="#Config-UM">{{tr}}config_atih_um{{/tr}}</a></li>
  {{/if}}
  <li><a href="#importCIM">{{tr}}Import.CIM{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="PMSI" style="display: none;">
{{mb_include template=inc_config_pmsi}}
</div>

<div id="Repair" style="display: none;">
{{mb_include template=inc_config_facture_hprim}}
</div>

<div id="CConfigEtab" style="display: none"></div>

{{if "atih"|module_active}}
  <div id="Config-UM" style="display:none"></div>
{{/if}}

<div id="importCIM" style="display: none">
  {{mb_include template=nomenclature_cim/inc_configure_import_cim}}
</div>
