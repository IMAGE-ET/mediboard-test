<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-configure', true);
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#configure-mode_easy">Aff. DHE Simplifiée</a></li>
  <li><a href="#configure-COperation">{{tr}}COperation{{/tr}}</a></li>
  <li><a href="#configure-CSejour">{{tr}}CSejour{{/tr}}</a></li>
  <li><a href="#configure-blocage">Blocage</a></li>
  <li><a href="#configure-CIdSante400">{{tr}}CIdSante400-tag{{/tr}}</a></li>
  <li><a href="#configure-maintenance">{{tr}}Maintenance{{/tr}}</a></li>
  <li><a href="#config-sae-base">{{tr}}config-sae-base{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="configure-mode_easy" style="display: none">
  {{mb_include template=inc_config_mode_easy}}
</div>

<div id="configure-COperation" style="display: none;">
  {{mb_include template=COperation_config}}
</div>

<div id="configure-CSejour" style="display: none;">
  {{mb_include template=CSejour_config}}
</div>

<div id="configure-blocage" style="display: none;">
  {{mb_include template=inc_config_blocage}}
</div>

<div id="configure-CIdSante400" style="display: none;">
  {{mb_include template=inc_config_etiquette}}
</div>

<div id="configure-maintenance" style="display:none">
  {{mb_include template=inc_config_actions}}
</div>

<div id="config-sae-base" style="display: none;">
  {{mb_include template=inc_config_base_sae}}
</div>