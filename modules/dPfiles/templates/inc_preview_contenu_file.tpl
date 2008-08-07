{{if $includeInfosFile}}
  {{assign var="link" value=0}}
  <center>
    <div class="previewfile {{$stylecontenu}}">
        {{if $dialog && $fileSel->_class_name=="CFile"}}
          {{assign var="link" value=1}}
          <a href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->file_id}}" title="Télécharger le fichier">
        {{elseif !$dialog}}
          {{assign var="link" value=1}}
          <a href="#popFile" onclick="popFile('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}',{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
        {{/if}}
        <div>
        {{$includeInfosFile|smarty:nodefaults}}
        </div>
        {{if $link}}
          </a>
        {{/if}}
    </div>
  </center>
{{/if}}