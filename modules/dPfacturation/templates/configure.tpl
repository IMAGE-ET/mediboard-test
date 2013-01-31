<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs-configure', true);
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#configure-CFactureCabinet"> {{tr}}CFactureCabinet{{/tr}}</a></li>
  <li><a href="#configure-CFactureEtablissement" >{{tr}}CFactureEtablissement{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="configure-CFactureCabinet" style="display: none">
  {{mb_include template=CFactureCabinet_config}}
</div>

<div id="configure-CFactureEtablissement" style="display: none">
  {{mb_include template=CFactureEtablissement_config}}
</div>