{{mb_include module=system template=CMbObject_view}}

{{if $can->edit}}
  <table class="tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit"
          onclick="if (window.editAffectation) { editAffectation('{{$object->_id}}') }"> Modifier</button>
      </td>
    </tr>
  </table>
{{/if}}