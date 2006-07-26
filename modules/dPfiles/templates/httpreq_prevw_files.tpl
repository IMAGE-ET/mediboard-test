{{if $file->file_id}}
  {{$file->_view}}<br />
  {{$file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}<br />
  <img src="mbfileviewer.php?file_id={{$file->file_id}}&amp;phpThumb=1&amp;hp=350&amp;wl=450" alt="-" />  
{{else}}
  Selectionnez un fichier
{{/if}}