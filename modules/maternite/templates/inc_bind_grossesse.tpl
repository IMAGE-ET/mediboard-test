<script type="text/javascript">
  Main.add(function() {
    Grossesse.refreshList('{{$parturiente_id}}', '{{$object_guid}}', '{{$grossesse_id_form}}');
  });
</script>

<button class="new" onclick="Grossesse.editGrossesse(0, '{{$parturiente_id}}')" style="float: left;">Nouvelle grossesse</button>

<table class="main">
  <tr>
    <td id="list_grossesses" style="width: 50%"></td>
    <td id="edit_grossesse"></td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="tick" onclick="Grossesse.bindGrossesse(); Control.Modal.close();">Sélectionner</button>
    </td>
  </tr>
</table>
