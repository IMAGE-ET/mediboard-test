<script language="JavaScript" type="text/javascript">
{literal}

function startCCAM() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("dPccam", "httpreq_do_add_ccam");
  CCAMUrl.requestUpdate("ccam");
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
  <td><button onclick="startCCAM()" >Importer la base de données CCAM</button></td>
  <td id="ccam"></td>
</tr>

</table>