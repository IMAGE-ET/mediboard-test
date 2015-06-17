{{if !$content}}
  <div class="small-warning">{{tr}}CExchangeSource-msg-Unknow file type{{/tr}}</div>
  {{mb_return}}
{{/if}}

{{if $image}}
  <img src="data:image/{{$extension}};base64,{{$content}}" />
{{else}}
  <pre>{{$content}}</pre>
{{/if}}
