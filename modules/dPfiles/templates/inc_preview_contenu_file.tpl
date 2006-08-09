{{if $includeInfosFile}}
  {{if $file->file_type == "text/plain"}}
    <center><pre{{if isset($stylecontenu)}} style="{{$stylecontenu}}"{{/if}}>{{$includeInfosFile}}</pre></center>
  {{else}}
    {{$includeInfosFile}}
  {{/if}}
{{/if}}