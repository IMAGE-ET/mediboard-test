<h2>
{{if $consult->_id}}
  {{$consult}}
{{elseif $patient->_id}}
  {{$patient}}
{{/if}}
</h2>

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tab-main', false);
});
</script>

<ul id="tab-main" class="control_tabs">
  <li><a href="#antecedents">AntÚcÚdents</a></li>
  <li><a href="#traitements">Traitements</a></li>
</ul>
<hr class="control_tabs" />

{{include file=inc_grid_antecedents.tpl}}
{{include file=inc_grid_traitements.tpl}}

