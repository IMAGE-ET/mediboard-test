{{* $id: $ *}}

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#sources">{{tr}}config-exchange-source{{/tr}}</a></li>
  <li><a href="#general">{{tr}}General{{/tr}}</a></li>
  <li><a href="#actions">{{tr}}Actions{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="sources" style="display: none;">
  {{mb_include template=inc_configure_sources}}
</div>

<div id="general" style="display: none;">
  {{mb_include template=inc_configure_general}}
</div>

<div id="actions" style="display: none;">
  {{include file=inc_configure_actions.tpl}}	
</div>
