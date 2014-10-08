<script>
  DatabaseExplorer = {
    displayTableData: function(dsn, table) {
      var url = new Url("importTools", "ajax_vw_table_data");
      url.addParam("dsn", dsn);
      url.addParam("table", table);
      url.requestUpdate("table-data");

      $('col-'+dsn+'-'+table).addUniqueClassName('selected');

      return false;
    }
  }
</script>

<table class="layout">
  <tr>
    <td class="narrow" style="width: 200px; border-right: 1px solid #999 !important; vertical-align: top;">
      {{foreach from=$databases item=_db key=_dsn}}
        <h3>{{$_dsn}}</h3>

        <div style="overflow-y: scroll; height: 800px;">
          <table class="main tbl">
            <tr>
              <th></th>
              <th>Desc</th>
              <th>Nb</th>
            </tr>
            {{foreach from=$_db.tables item=_table}}
              <tr {{if !$_table.display || $_table.count == 0}} class="hidden" {{/if}} id="col-{{$_dsn}}-{{$_table.name}}">
                <td>
                  <a href="#1" onclick="return DatabaseExplorer.displayTableData('{{$_dsn}}', '{{$_table.name}}')">
                    {{$_table.name}}
                  </a>
                </td>
                <td>
                  {{if $_table.title}}
                    <em>{{$_table.title}}</em>
                  {{/if}}
                </td>
                <td class="compact">
                  {{$_table.count}}
                </td>
              </tr>
            {{/foreach}}
          </table>
        </div>
      {{/foreach}}
    </td>
    <td id="table-data" style="vertical-align: top;"></td>
  </tr>
</table>