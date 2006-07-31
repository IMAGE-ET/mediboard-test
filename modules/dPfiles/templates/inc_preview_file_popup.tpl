<table class="form">
  <tr>
    <td class="button">
      {{if $file->file_id}}
        {{$file->_view}} (dans {{if $listCat->nom}}{{$listCat->nom}}{{else}}Aucune Catégorie{{/if}})<br />
        {{$file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}<br />
        <a href="mbfileviewer.php?file_id={{$file->file_id}}"><img src="mbfileviewer.php?file_id={{$file->file_id}}&amp;phpThumb=1&amp;w=600" alt="-" border="0" /></a>
      {{/if}}
    </td>
  </tr>
</table>