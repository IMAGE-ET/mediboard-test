<script type="text/javascript">
  Main.add(function() {
    Blocage.refreshList('{{$blocage_id}}');
    {{if $blocage_id}}
      Blocage.edit('{{$blocage_id}}');
    {{/if}}
  });
</script>

<button type="button" class="new" onclick="Blocage.updateSelected(); Blocage.edit('0')">Nouveau blocage</button>

<table class="main">
  <tr>
    <td id="list_blocages"></td>
    <td id="edit_blocage" style="width: 50%"></td>
  </tr>
</table>
