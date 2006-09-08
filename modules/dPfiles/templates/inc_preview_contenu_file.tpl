{{if $includeInfosFile}}
  {{if $file->file_type == "text/plain"}}
    <center>
      <div class="previewfile{{if isset($stylecontenu)}} {{$stylecontenu}}{{/if}}">
        <a href="javascript:popFile({{$file->file_id}},{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
        <div>
        {{$includeInfosFile}}
        </div>
        </a>
      </div>
    </center>
  {{else}}
    {{$includeInfosFile}}
  {{/if}}
{{/if}}