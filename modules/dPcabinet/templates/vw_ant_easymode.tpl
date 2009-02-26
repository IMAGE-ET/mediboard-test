<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab-main', false);
});
</script>

<ul id="tab-main" class="control_tabs">
  <li><a href="#antecedents">Antécédents</a></li>
  <li><a href="#traitements">Traitements</a></li>
</ul>
<hr class="control_tabs" />

{{include file=inc_grid_antecedents.tpl}}
{{include file=inc_grid_traitements.tpl}}

