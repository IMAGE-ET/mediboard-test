<script type="text/javascript">

function checkIntegrity() {
  var url = new Url;
  url.setModuleAction("dPfiles", "httpreq_check_file_integrity");
  url.requestUpdate("checkIntegrity");
  //url.redirect();  
}

</script>

<h2>Intégrité de la table de fichiers attachés</h2>

<button class="search" onclick="checkIntegrity()">
  vérifier l'intégrité
</button>

<div id="checkIntegrity" />
