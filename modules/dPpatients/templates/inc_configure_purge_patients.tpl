<script type="text/javascript">

function purgePatients() {
  var url = new Url("dPpatients", "ajax_purge_patients");
  url.addParam("qte", 10);
  url.requestUpdate("purge_patients");
}

</script>

<div class="big-warning">
  La purge des patients est une action irreversible qui supprime aléatoirement
  une partie des dossiers patients de la base de données et toutes les données
  qui y sont associées.
  <strong>
    N'utilisez cette fonctionnalité que si vous savez parfaitement ce que vous faites
  </strong>
</div>
<table class="tbl">
  <th>
    Purge des patients (par 10)
    <button type="button" class="tick" onclick="purgePatients();">
      GO
    </button>
  </th>
  <td id="purge_patients">
    <div class="small-info">{{$nb_patients}} patients dans la base</div>
  </td>
</table>