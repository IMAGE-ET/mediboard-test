<script language="JavaScript" type="text/javascript">
{literal}

function startCIM10() {
  var CIM10Url = new Url;
  CIM10Url.setModuleAction("dPcim10", "httpreq_do_add_cim10");
  CIM10Url.requestUpdate("cim10");
}

{/literal}
</script>

<h2>Import de la base de données CCAM V2</h2>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Status</th>
</tr>
  
<tr>
  <td><button onclick="startCIM10()">Importer la base de données CIM10</button></td>
  <td id="cim10" />
</tr>

</table>