<script type="text/javascript">
Main.add(function () {
  PairEffect.initGroup("serviceEffect");
  Control.Tabs.create('tabs-chambres', true);
});
</script>

<ul id="tabs-chambres" class="control_tabs">
  <li><a href="#services">{{tr}}CService{{/tr}}</a></li>
  <li><a href="#chambres">{{tr}}CChambre{{/tr}}</a></li>
  <li><a href="#UF">{{tr}}CUniteFonctionnelle{{/tr}}</a></li>
  <li><a href="#prestations">{{tr}}CPrestation{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<div id="services" style="display: none;">
  {{include file="inc_vw_idx_services.tpl"}}
</div>

<div id="chambres" style="display: none;">
  {{include file="inc_vw_idx_chambres.tpl"}}
</div>

<div id="UF" style="display: none;">
  {{include file="inc_vw_idx_ufs.tpl"}}
</div>

<div id="prestations" style="display: none;">
  {{include file="inc_vw_idx_prestations.tpl"}}
</div>