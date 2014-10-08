<h3>{{$table}}</h3>

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
            {{$_row.$_col}}
          {{else}}
            {{assign var=_encoded value=$_row.$_col|smarty:nodefaults|base64_encode}}

            <span style="float: right; margin-left: 3px;">
              <a href="data:image/png;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
                PNG
              </a>
              <a href="data:image/jpeg;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
                JPEG
              </a>
              <a href="data:application/pdf;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
                PDF
              </a>
            </span>

            <a href="data:text/plain;base64,{{$_encoded}}" target="_blank" style="display: inline-block;">
              [Blob {{$_row.$_col|strlen}}o]
            </a>
          {{/if}}
        </td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>