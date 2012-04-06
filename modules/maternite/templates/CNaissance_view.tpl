{{mb_include module=system template=CMbObject_view}}

{{assign var=naissance value=$object}}

{{mb_script module=maternite script=naissance ajax=1}}
<table class="form">
  <tr>
    <td class="button">
      <button class="edit" onclick="Naissance.edit('{{$naissance->_id}}')">
        {{tr}}Edit{{/tr}}
      </button>
    </th>
  </tr>
</table>