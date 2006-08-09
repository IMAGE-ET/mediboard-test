{{if $includeInfosFile}}
  {{if $file->file_type == "text/plain"}}
    <center><div style="text-align:left;ont-family: lucida console;{{if isset($stylecontenu)}}{{$stylecontenu}}{{/if}}">{{$includeInfosFile}}</div></center>
  {{else}}
    {{$includeInfosFile}}
  {{/if}}
{{/if}}