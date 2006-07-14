<h2>Contrôle d'intégrité des documents</h2>

<table class="tbl">
  <tr>
    <th class="title" colspan="4">Bilan d'intégrité</th>
  </tr>
  <tr>
    <th>Nombre de documents</th>
    <th>Nombre de documents sans fichiers</th>
    <th>Nombre de fichiers</th>
    <th>Nombre de fichiers sans documents</th>
  </tr>
  <tr>
    <td>{{$docsCount}}</td>
    <td>{{$docsWithoutFileCount}}</td>
    <td>{{$filesCount}}</td>
    <td>{{$filesWithoutDocCount}}</td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      Détail des documents sans fichiers
      {{if $docsWithoutFileCount > $show}}
      ({{$show}} premiers documents sur {{$docsWithoutFileCount}})
      {{/if}}
    </th>
  </tr>
  <tr>
    <th>Classe d'objet</th>
    <th>Id d'object</th>
    <th>Nom réel du fichier</th>
    <th>Chemin du fichier</th>
  </tr>
  {{foreach from=$docsWithoutFileTruncated item=curr_doc}}
  
  <tr>
    <td>{{$curr_doc->file_class}}</td>
    <td>{{$curr_doc->file_object_id}}</td>
    <td>{{$curr_doc->file_name}}</td>
    <td>{{$curr_doc->_file_path}}</td>
  </tr>
  {{/foreach}}
</table>

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      Détail des fichiers sans documents
      {{if $filesWithoutDocCount > $show}}
      ({{$show}} premiers fichiers sur {{$filesWithoutDocCount}})
      {{/if}}
    </th>
  </tr>
  <tr>
    <th>Classe d'objet</th>
    <th>Id d'object</th>
    <th>Nom réel du fichier</th>
    <th>Chemin du fichier</th>
  </tr>
  {{foreach from=$filesWithoutDocTruncated item=curr_file}}
  <tr>
    <td>{{$curr_file.fileObjectClass}}</td>
    <td>{{$curr_file.fileObjectId}}</td>
    <td>{{$curr_file.fileName}}</td>
    <td>{{$curr_file.filePath}}</td>
  </tr>
  {{/foreach}}
</table>