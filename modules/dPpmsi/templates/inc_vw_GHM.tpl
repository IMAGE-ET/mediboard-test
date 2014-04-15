<script>

Main.add(function () {
  PMSI.loadExportActes('{{$sejour->_id}}', 'CSejour');
});

</script>

<table class="form">
  
<tr>
  <td id="export_CSejour_{{$sejour->_id}}">
  </td>
</tr>

</table>