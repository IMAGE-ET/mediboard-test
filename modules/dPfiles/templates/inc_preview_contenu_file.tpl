{{if $includeInfosFile}}
  {{if $file->file_type == "text/plain"}}
    <center><div style="white-space: normal;text-align:left;font-family: lucida console;{{if isset($stylecontenu)}}{{$stylecontenu}}{{/if}}">{{$includeInfosFile}}</div></center>
  {{else}}
    {{$includeInfosFile}}
  {{/if}}
{{/if}}