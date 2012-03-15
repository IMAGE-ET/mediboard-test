<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-configure', true);
});
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#params">Paramètres</a></li>
  <li><a href="#actions">Autres actions</a></li>
  <li><a href="#offline">Mode offline</a></li>
</ul>

<hr class="control_tabs" />

<div id="params" style="display: none;">
 {{include file=inc_configure_params.tpl}}
</div>

<div id="actions" style="display: none;">
 {{include file=inc_configure_actions.tpl}}
</div>

<div id="offline" style="display: none;">
  <table class="main tbl">
    <tr>
      <td>
        <a class="button search" href="?m=dPcabinet&amp;a=offline_programme_consult&amp;dialog=1&amp;_aio=1">
          {{tr}}mod-dPcabinet-tab-offline_programme_consult{{/tr}}
        </a>
        <a class="button search" href="?m=dPcabinet&amp;a=vw_journee&amp;dialog=1&amp;offline=1&amp;_aio=1">
          {{tr}}mod-dPcabinet-tab-vw_journee{{/tr}}
        </a>
      </td>
    </tr>
  </table>
</div>