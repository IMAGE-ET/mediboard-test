<form name="editImport" method="post" enctype="multipart/form-data"
  action="?m=dPprescription&a=ajax_import_protocole&dialog=1">
  <input type="hidden" name="praticien_id" value="{{$praticien_id}}" />
  <input type="hidden" name="function_id" value="{{$function_id}}" />
  <input type="hidden" name="group_id" value="{{$group_id}}" />
  <table class="form">
    <tr>
      <th class="category">{{tr}}CPrescription.choose_file{{/tr}}</th>
    </tr>
    <td>
      <input type="file" name="datafile" size="40">
    </td>
    </tr>
    <tr>
      <td>
        <button class="tick">Importer</button>
      </td>
    </tr>
  </table>
</form>
