{{include file="../../system/templates/configure_dsn.tpl" dsn=cim10}}

<script type="text/javascript">

function startCIM10() {
  var url = new Url;
  url.setModuleAction("dPcim10", "httpreq_do_add_cim10");
  url.requestUpdate("cim10");
}

function diffCIM10Atih() {
  var url = new Url;
  url.setModuleAction("dPcim10", "httpreq_diff_cim10_atih");
  url.requestUpdate("cim10_diff");
}

</script>

<h2>Import de la base de données CIM10</h2>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Status</th>
</tr>
  
<tr>
  <td><button class="tick" onclick="startCIM10()">Importer la base de données CIM10</button></td>
  <td id="cim10" />
</tr>

<tr>
  <td><button class="tick" onclick="diffCIM10Atih()">Différenciel entre la CIM10 et les modifications de l'ATIH</button></td>
  <td id="cim10_diff" />
</tr>

</table>