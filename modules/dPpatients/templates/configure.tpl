<script type="text/javascript">
  Main.add(function () {
    var tabs = Control.Tabs.create('tabs-configure', true, {
      afterChange: function(container) {
        if (container.id == "CConfigEtabPatient") {
          Configuration.edit('dPpatients', ['CGroups'], $('CConfigEtabPatient'));
        }
      }
    });
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CPatient">{{tr}}CPatient{{/tr}}</a></li>
  <li><a href="#CAntecedent">{{tr}}CAntecedent{{/tr}}</a></li>
  <li><a href="#CConstantesMedicales">{{tr}}CConstantesMedicales{{/tr}}</a></li>
  <li><a href="#CConfigEtabPatient">{{tr}}config-dPatients-object-config{{/tr}}</a></li>
  <li><a href="#configure-maintenance">{{tr}}Maintenance{{/tr}}</a></li>
</ul>

<div id="CPatient" style="display: none;">
  {{mb_include template=CPatient_configure}}
</div>

<div id="CAntecedent" style="display: none;">
  {{mb_include template=CAntecedent_configure}}
</div>

<div id="CConstantesMedicales" style="display: none;">
  {{mb_include template=CConstantesMedicales_configure}}
</div>

<div id="CConfigEtabPatient" style="display: none;"></div>

<div id="configure-maintenance" style="display:none">
  {{mb_include template=inc_configure_actions}}
</div>