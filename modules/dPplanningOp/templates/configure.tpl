<script>
  Main.add(function() {
    Control.Tabs.create('tabs-configure', true, {afterChange: function(container) {
      if (container.id == "CConfigEtab") {
        Configuration.edit('dPplanningOp', ['CGroups'], $('CConfigEtab'));
      }
    }});
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#configure-mode_easy"  >Aff. DHE Simplifiée         </a></li>
  <li><a href="#configure-COperation" >{{tr}}COperation{{/tr}}     </a></li>
  <li><a href="#configure-CSejour"    >{{tr}}CSejour{{/tr}}        </a></li>
  <li><a href="#configure-CProtocole" >{{tr}}CProtocole{{/tr}}     </a></li>
  <li><a href="#configure-CLibelleOp" >{{tr}}CLibelleOp{{/tr}}     </a></li>
  <li><a href="#configure-CFactureEtablissement">{{tr}}CFactureEtablissement{{/tr}} </a></li>
  <li><a href="#configure-blocage"    >Blocage                     </a></li>
  <li><a href="#configure-CIdSante400">{{tr}}CIdSante400-tag{{/tr}}</a></li>
  <li><a href="#configure-maintenance">{{tr}}Maintenance{{/tr}}    </a></li>
  <li><a href="#CConfigEtab"          >Config par établissement    </a></li>
  <li><a href="#Purge"                >{{tr}}Purge{{/tr}}          </a></li>
</ul>

<div id="configure-mode_easy" style="display: none">
  {{mb_include template=inc_config_mode_easy}}
</div>

<div id="configure-COperation" style="display: none;">
  {{mb_include template=COperation_config}}
</div>

<div id="configure-CSejour" style="display: none;">
  {{mb_include template=CSejour_config}}
</div>

<div id="configure-CProtocole" style="display: none;">
  {{mb_include template=CProtocole_config}}
</div>

<div id="configure-CLibelleOp" style="display: none;">
  {{mb_include template=CLibelleOp_config}}
</div>

<div id="configure-CFactureEtablissement" style="display: none;">
  {{mb_include template=CFactureEtablissement_config}}
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

<div id="CConfigEtab" style="display: none">

<div id="Purge" style="display: none;">
  {{mb_include template=inc_configure_purge_sejours}}
</div>