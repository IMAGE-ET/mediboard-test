{{if $file->file_id}}
  {{$file->_view}}<br />
  {{$file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}<br />
  {{if $file->_nb_pages}}
    {{if $page_prev !== null}}
    <a class="button" href="javascript:ZoomFileAjax({{$file->file_id}},{{$page_prev}});"><img align="top" src="modules/{{$m}}/images/prev.png" alt="Page précédente" /> Page précédente</a>
    {{/if}}
    {{$pageEnCours}}
    {{if $page_next}}
    <a class="button" href="javascript:ZoomFileAjax({{$file->file_id}},{{$page_next}});">Page suivante <img align="top" src="modules/{{$m}}/images/next.png" alt="Page suivante" /></a>
    {{/if}}
  {{/if}}<br />
  <a href="javascript:popFile({{$file->file_id}})"><img src="mbfileviewer.php?file_id={{$file->file_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" alt="-" border="0" />  </a>
{{else}}
  Selectionnez un fichier
{{/if}}