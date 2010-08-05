{{if $fileSel && $fileSel->_id}}
  <h4>{{$fileSel->_view}}</h4>
  
  {{if $fileSel->_class_name=="CFile"}}
    {{$fileSel->file_date|date_format:$dPconfig.datetime}}<br />
  {{/if}}

  {{if $fileSel->_class_name == "CFile" && $fileSel->_nb_pages && !$acces_denied}}
  <!-- Déplacement dans les pages -->
    
    <button type="button" {{if $page_prev === null}}disabled="disabled"{{/if}} title="Page précédente" onclick="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', '{{$page_prev}}');">
    	<img src="images/icons/prev.png" />
    </button>
    
    {{if $fileSel->_nb_pages && $fileSel->_nb_pages>=2}}
    <select name="_num_page" onchange="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', this.value);">
      {{foreach from=$arrNumPages item=currPage}}
        <option value="{{$currPage-1}}" {{if $currPage-1==$sfn}}selected="selected" {{/if}}>
          {{$currPage}} / {{$fileSel->_nb_pages}}
        </option>
      {{/foreach}}
    </select>
    {{elseif $fileSel->_nb_pages}}
      Page {{$sfn+1}} / {{$fileSel->_nb_pages}}
    {{/if}}
    
    <button type="button" {{if !$page_next}}disabled="disabled"{{/if}} title="Page suivante" onclick="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', '{{$page_next}}');">
    	<img src="images/icons/next.png" />
    </button>
  {{/if}}
  
  <hr />
  
  <a href="#popFile" onclick="popFile('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}',{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
    {{if $includeInfosFile}}
      {{include file="../../dPfiles/templates/inc_preview_contenu_file.tpl"}}
    {{else}}
      <img class="preview" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}$amp;ra={{$fileSel->_rotate}}" title="Afficher le grand aperçu" border="0" />
    {{/if}}
  </a>
{{else}}
  <div class="small-info">
  Sélectionnez un document pour en avoir un aperçu.
  </div>
{{/if}}