{{if $tooltip}}
  <h2>
    {{$table}}
    &ndash;
    {{if $where_column}}
      WHERE {{$where_column}} = '{{$where_value}}'
    {{/if}}
  </h2>


  <table class="tbl" style="width: auto;">
    {{foreach from=$columns key=_col item=_col_info}}
      <tr>
        <th style="text-align: left;">{{$_col}}</th>
        <td>
          {{if $_col_info.Key == "PRI"}}
            <i class="fa fa-key" style="color: #ae0040;"></i>
          {{elseif $_col_info.Key == "MUL"}}
            <i class="fa fa-link" onclick="DatabaseExplorer.selectPrimaryKey('{{$dsn}}', '{{$table}}', '{{$_col}}')" style="cursor: pointer;"></i>
          {{/if}}
        </td>
        <td style="color: #999;"><code>{{$_col_info.datatype}}</code></td>
        <td>
          {{mb_include module=importTools template=inc_display_value value=$rows.0.$_col col_info=$_col_info}}
        </td>
      </tr>
    {{/foreach}}
  </table>
{{else}}

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
  {{mb_include module=system template=inc_pagination total=$total step=$count current=$start change_page=changePage jumper=10}}
</div>

<table class="tbl" style="width: auto;">
  <tr>
    {{foreach from=$columns key=_col item=_col_info}}
      <th title="{{$_col_info.datatype}}" style="padding: 2px 4px;">
        {{assign var=_new_order_way value="ASC"}}
        {{if $order_way == "ASC"}}
          {{assign var=_new_order_way value="DESC"}}
        {{/if}}

        <a class="{{$_col}} {{if $order_column == $_col}}sorted {{$order_way}}{{else}}sortable{{/if}}"
           onclick="DatabaseExplorer.displayTableData('{{$dsn}}', '{{$table}}', 0, null, '{{$_col}}', '{{$_new_order_way}}')">
          {{$_col}}
        </a>

        {{if $_col_info.Key == "PRI"}}
          <i class="fa fa-key" style="color: #ae0040;"></i>
        {{elseif $_col_info.Key == "MUL"}}
          <i class="fa fa-link" onclick="DatabaseExplorer.selectPrimaryKey('{{$dsn}}', '{{$table}}', '{{$_col}}')" style="cursor: pointer;"></i>
        {{/if}}

        <button class="lookup notext" onclick="DatabaseExplorer.displayTableDistinctData('{{$dsn}}', '{{$table}}', '{{$_col}}')"></button>
      </th>
    {{/foreach}}
  </tr>

  {{foreach from=$rows item=_row}}
    <tr>
      {{foreach from=$columns key=_col item=_col_info}}
        <td {{if $_col_info.Key == "MUL"}} style="background: rgba(127,180,127,0.2);" {{/if}}>
          {{mb_include module=importTools template=inc_display_value value=$_row.$_col col_info=$_col_info}}
        </td>
      {{/foreach}}
    </tr>
  {{/foreach}}
</table>
{{/if}}