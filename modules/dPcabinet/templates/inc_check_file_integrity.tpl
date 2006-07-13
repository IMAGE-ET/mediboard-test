<h2>{{$files|@count}} objects fichiers</h2>
<table class="tbl">
  <tr>
    <th>Classe d'objet</th>
    <th>Id d'object</th>
    <th>Nom de fichier</th>
    <th>Chemin du fichier</th>
  </tr>
  {{foreach from=$files item=curr_file}}
  <tr>
    <td>{{$curr_file->file_class}}</td>
    <td>{{$curr_file->file_object_id}}</td>
    <td>{{$curr_file->file_name}}</td>
    <td>{{$curr_file->_file_path}}</td>
  </tr>
  {{/foreach}}
</table>