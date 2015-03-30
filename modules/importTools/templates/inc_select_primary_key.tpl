<ul>
  {{foreach from=$db_info.tables key=_table_name item=_table}}
    <li>
      <strong>{{$_table_name}}</strong> {{if $_table.title}} - <em>{{$_table.title}}</em>{{/if}}
      <ul>
      {{foreach from=$_table.columns key=_column_name item=_column}}
        {{if $_column.Key == "PRI"}}
          <li>
            <a href="#1" onclick="DatabaseExplorer.saveForeignKey('{{$dsn}}', '{{$table}}', '{{$column}}', '{{$_table_name}}.{{$_column_name}}')">
              {{if $_column_name == $column}}
                <span style="background: red; color: #ffffff;">{{$_column_name}}</span>
              {{else}}
                {{$_column_name}}
              {{/if}}
            </a>
          </li>
        {{/if}}
      {{/foreach}}
      </ul>
    </li>
  {{/foreach}}
</ul>