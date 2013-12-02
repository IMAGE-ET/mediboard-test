<script>
  Main.add(function() {
    Control.Tabs.create('tabs-configure', true);
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#general">Général</a></li>
  <li><a href="#relances">{{tr}}CRelance{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="general" style="display: none">
  {{mb_include template=CFactureEtablissement_config}}
</div>

<div id="relances" style="display: none">
  {{mb_include template=CRelance_config}}
</div>