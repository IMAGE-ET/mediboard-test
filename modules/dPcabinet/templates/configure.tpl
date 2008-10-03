<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-configure', true);
});
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#params">Paramètres</a></li>
  <li><a href="#logicmax">LogicMax</a></li>
  <li><a href="#actions">Autres actions</a></li>
</ul>

<hr class="control_tabs" />

<div id="params">
 {{include file=inc_configure_params.tpl}}
</div>

<div id="logicmax" style="display: none;">
	{{include file="../../system/templates/configure_dsn.tpl" dsn=logicmax}}
</div>

<div id="actions" style="display: none;">
 {{include file=inc_configure_actions.tpl}}
</div>
