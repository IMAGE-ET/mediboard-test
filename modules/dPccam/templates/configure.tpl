<script language="JavaScript" type="text/javascript">

function startCCAM() {
  var CCAMUrl = new Url;
  CCAMUrl.setModuleAction("dPccam", "httpreq_do_add_ccam");
  CCAMUrl.requestUpdate("ccam");
}

function startNGAP(){
  var NGAPUrl = new Url;
  NGAPUrl.setModuleAction("dPccam", "httpreq_do_add_ngap");
  NGAPUrl.requestUpdate("ngap");
}

</script>

<h2>Import de la base de données CCAM</h2>
<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startCCAM()" >Importer la base de données CCAM</button></td>
    <td id="ccam"></td>
  </tr>
</table>

<h2>Import de la base de codes NGAP</h2>
<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startNGAP()" >Importer la base de codes NGAP</button></td>
    <td id="ngap"></td>
  </tr>
</table>
