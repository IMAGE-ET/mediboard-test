<script type="text/javascript">

function doAction(sAction) {
  var url = new Url;
  url.setModuleAction("dPinterop", "httpreq_do_cfg_action");
  url.addParam("action", sAction);
  url.requestUpdate(sAction);
}

</script>

<h2>Création et remplissage des la base des GHS / GHM</h2>

<table class="tbl">

<tr>
  <th class="category">Action</th>
  <th class="category">Status</th>
</tr>

<tr>
  <td onclick="doAction('extractFiles');">
  	<button class="tick">Installer le schema HPRIM 'ServeurActes'</button>
  </td>
  <td class="text" id="extractFiles" />
</tr>

</table>