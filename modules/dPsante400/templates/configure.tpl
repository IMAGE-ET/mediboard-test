<script language="Javascript" type="text/javascript">

function purgeObjects() {
  if (confirm("Merci de confirmer la purge de tous les éléments synchronisés")) {
    var url = new Url;
    url.setModuleAction("dPsante400", "httpreq_purge_objects");
    url.requestUpdate("purgeObjects");
  }
}

</script>

<h2>Purge des données importés</h2>

<div class="big-warning">
  Attention, cette option permet de purger la base de données de tous les éléments 
  synchronisés dupuis une application tierces, A utiliser avec une extrême prudence, 
  car <strong>l'opération est irréversible</strong> !
</div>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  <tr>
    <td>
      <button class="tick" onclick="purgeObjects()">
        Purger tous les objets syncrhonisés
      </button>
    </td>
    <td class="text" id="purgeObjects" />
  </tr>

</table>