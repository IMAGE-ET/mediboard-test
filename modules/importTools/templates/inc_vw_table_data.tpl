<h3>
  {{$table}}
  <select onchange="DatabaseExplorer.displayTableData('{{$dsn}}', '{{$table}}', 0, $V(this))">
    {{foreach from=$counts item=_count}}
      <option value="{{$_count}}" {{if $count == $_count}}selected{{/if}}>{{$_count}}</option>
    {{/foreach}}
  </select>
</h3>

<script>
  changePage = function(start){
    DatabaseExplorer.displayTableData('{{$dsn}}', '{{$table}}', start, 50);
  }
</script>

<div style="max-width: 600px">
  {{mb_include module=system template=inc_pagination total=$total step=$count current=$start change_page=changePage}}
</div>

<table class="tbl" style="width: auto;">
  <tr>
    {{foreach from=$columns key=_col item=_col_info}}
      <th title="{{$_col_info.datatype}}" style="padding: 2px 4px;">{{$_col}}</th>
    {{/foreach}}
  </tr>

  {{foreach from=$rows item=_row}}
    <tr>
      {{foreach from=$columns key=_col item=_col_info}}
        <td>
          {{if strpos($_col_info.Type,'blob') === false}}
            {{if $_row.$_col !== null}}
              {{if $_col_info.is_text}}
                <code style="background: rgba(180,180,255,0.3)">{{$_row.$_col}}</code>
              {{else}}
                {{$_row.$_col}}
              {{/if}}
            {{else}}
              <span class="empty" style="color: #ccc;">NULL</span>
            {{/if}}
          {{else}}
            {{if $_row.$_col|strlen === 0}}
              <span class="empty">[Empty blob]</span>
            {{else}}
              {{assign var=_encoded value=$_row.$_col|smarty:nodefaults|base64_encode}}

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
                [Blob {{$_row.$_col|strlen}}o]
              </a>
            {{/if}}
          {{/if}}
        </td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>