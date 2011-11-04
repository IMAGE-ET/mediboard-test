{{mb_script module=dPprescription script=ressource}}

<script type="text/javascript">
  Main.add(function() {
    Ressource.refreshList();
    Ressource.edit('{{$ressource_soin_id}}');
  });
</script>
<button type="button" class="new" onclick="Ressource.edit();">{{tr}}CRessourceSoin-new{{/tr}}</button>
  
</button>
<table class="main">
  <tr>
    <td style="width: 50%;" id="list_ressources"></td>
    <td style="width: 50%;" id="edit_ressource"></td>
  </tr>
  
</table>
