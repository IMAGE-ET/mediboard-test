<script type="text/javascript">
  editPrestations = function (sejour_id) {
    var url = new Url("dPplanningOp", "ajax_vw_prestations");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(800);
  }
</script>
{{mb_include module=system template=CMbObject_view}}

{{if $can->edit}}
  <table class="tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit"
          onclick="if (window.editAffectation) { editAffectation('{{$object->_id}}') }">Modifier</button>
        <button type="button" class="search" onclick="editPrestations('{{$object->sejour_id}}')">Prestations</button>
      </td>
    </tr>
  </table>
{{/if}}