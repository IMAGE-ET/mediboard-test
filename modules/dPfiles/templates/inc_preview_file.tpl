{{if $file->file_id}}
  <strong>{{$file->_view}}</strong><br />
  {{$file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}<br />
  {{if $file->_nb_pages && !$acces_denied}}
    {{if $page_prev !== null}}
    <a class="button" href="javascript:ZoomFileAjax({{$file->file_id}},{{$page_prev}});"><img align="top" src="modules/{{$m}}/images/prev.png" alt="Page précédente" /> Page précédente</a>
    {{/if}}
    
    {{if $file->_nb_pages && $file->_nb_pages>=2}}
      <select name="_num_page" onchange="javascript:ZoomFileAjax({{$file->file_id}},this.value);">
      {{foreach from=$arrNumPages item=currPage}}
      <option value="{{$currPage-1}}" {{if $currPage-1==$sfn}}selected="selected" {{/if}}>
      Page {{$currPage}} / {{$file->_nb_pages}}
      </option>
      {{/foreach}}
      </select>
    {{elseif $file->_nb_pages}}
      Page {{$sfn+1}} / {{$file->_nb_pages}}
    {{/if}}
            
    {{if $page_next}}
    <a class="button" href="javascript:ZoomFileAjax({{$file->file_id}},{{$page_next}});">Page suivante <img align="top" src="modules/{{$m}}/images/next.png" alt="Page suivante" /></a>
    {{/if}}
  {{/if}}<br />
    {{if $includeInfosFile}}
    {{assign var="stylecontenu" value="previewfileMinus"}}
    {{include file="inc_preview_contenu_file.tpl"}}
    {{else}}
    <a href="javascript:popFile({{$file->file_id}},{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
    <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->file_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" title="Afficher le grand aperçu" border="0" />
    </a>  
    {{/if}}
{{else}}
  Selectionnez un fichier
{{/if}}