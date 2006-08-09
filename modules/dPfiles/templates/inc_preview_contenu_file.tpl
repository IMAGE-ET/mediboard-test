{{if $includeInfosFile}}
  {{if $file->file_type == "text/plain"}}
    <center><div style="font-family: lucida console;{{if isset($stylecontenu)}}{{$stylecontenu}}{{/if}}">{{$includeInfosFile}}</div></center>
  {{else}}
    {{$includeInfosFile}}
  {{/if}}
{{/if}}