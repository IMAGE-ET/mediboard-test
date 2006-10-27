{{if $includeInfosFile}}
  {{assign var="link" value=0}}
  <center>
    <div {{if $fileSel->_class_name=="CFile"}}style="font-family: lucida console;"{{/if}} class="previewfile{{if $stylecontenu}} {{$stylecontenu}}{{/if}}">
        {{if $popup && $fileSel->_class_name=="CFile"}}
          {{assign var="link" value=1}}
          <a href="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->file_id}}" title="Télécharger le fichier">
        {{elseif !$popup}}
          {{assign var="link" value=1}}
          <a href="javascript:popFile('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}',{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
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