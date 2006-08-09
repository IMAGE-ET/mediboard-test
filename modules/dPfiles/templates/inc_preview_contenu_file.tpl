{{if $includeInfosFile}}
  {{if $file->file_type == "text/plain"}}
    <center><div class="previewfile{{if isset($stylecontenu)}} {{$stylecontenu}}{{/if}}">{{$includeInfosFile}}</div></center>
  {{else}}
    {{$includeInfosFile}}
  {{/if}}
{{/if}}