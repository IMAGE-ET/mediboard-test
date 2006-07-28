{{if $file->file_id}}
  {{$file->_view}}<br />
  {{$file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}<br />
  <a href="javascript:popFile({{$file->file_id}})"><img src="mbfileviewer.php?file_id={{$file->file_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450" alt="-" border="0" />  </a>
{{else}}
  Selectionnez un fichier
{{/if}}