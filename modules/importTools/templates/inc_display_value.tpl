{{if strpos($col_info.Type,'blob') === false}}
  {{if $value !== null}}
    {{if $col_info.is_text}}
      <code style="background: rgba(180,180,255,0.3)">{{$value}}</code>
    {{else}}
      {{if $col_info.foreign_key}}
        {{assign var=_fk value="."|explode:$col_info.foreign_key}}
        <a href="#1" onclick="return DatabaseExplorer.displayTableDataWhere('{{$dsn}}', '{{$_fk.0}}', 0, '{{$_fk.1}}', '{{$value}}')">
          {{$value}}
        </a>
      {{else}}
        {{$value}}
      {{/if}}
    {{/if}}
  {{else}}
    <span class="empty" style="color: #ccc;">NULL</span>
  {{/if}}
{{else}}
  {{if $value|strlen === 0}}
    <span class="empty">[Empty blob]</span>
  {{else}}
    {{assign var=_encoded value=$value|smarty:nodefaults|base64_encode}}

    <a href="data:image/png;base64,{{$_encoded}}" target="_blank" style="display: inline-block;"
       onmouseover="ObjectTooltip.createDOM(this, DOM.img({src: 'data:image/png;base64,{{$_encoded}}', style: 'max-wdth: 400px'}))">
      PNG
    </a>
    <a href="data:image/jpeg;base64,{{$_encoded}}" target="_blank" style="display: inline-block;"
       onmouseover="ObjectTooltip.createDOM(this, DOM.img({src: 'data:image/jpeg;base64,{{$_encoded}}', style: 'max-wdth: 400px'}))">
      JPEG
    </a>
    <a href="data:application/pdf;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
      PDF
    </a>
    <a href="data:application/rtf;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
      RTF
    </a>
    <a href="data:text/plain;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
      [Blob {{$value|strlen}}o]
    </a>
  {{/if}}
{{/if}}