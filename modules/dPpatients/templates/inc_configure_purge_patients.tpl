<script type="text/javascript">

function purgePatients() {
  var url = new Url("dPpatients", "ajax_purge_patients");
  url.addParam("qte", 5);
  url.requestUpdate("purge_patients", repeatPurge);
}

function repeatPurge() {
  if($V($("check_repeat_purge"))) {
    purgePatients();
  }
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
  <tr>
    <th>
      Purge des patients (par 5)
      <button type="button" class="tick" onclick="purgePatients();">
        GO
      </button>
      <br />
      <input type="checkbox" name="repeat_purge" id="check_repeat_purge"/> Relancer automatiquement
    </th>
  </tr>
  <tr>
    <td id="purge_patients">
      <div class="small-info">{{$nb_patients}} patients dans la base</div>
    </td>
  </tr>
</table>