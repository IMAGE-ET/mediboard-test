<script>
  DatabaseExplorer = {
    displayTableData: function(dsn, table, start, count) {
      var url = new Url("importTools", "ajax_vw_table_data");
      url.addParam("dsn", dsn);
      url.addParam("table", table);
      url.addParam("start", start);
      url.addNotNullParam("count", count);
      url.requestUpdate("table-data");

      $('col-'+dsn+'-'+table).addUniqueClassName('selected');

      return false;
    },

    toggleHidden: function(dsn, b) {
      $("table-"+dsn).toggleClassName("show_hidden", b);
    },

    toggleEmpty: function(dsn, b) {
      $("table-"+dsn).toggleClassName("show_empty", b);
    }
  }
</script>

<style>
  .show_hidden .hidden {
    display: table-row !important;
  }
  .show_empty .empty {
    display: table-row !important;
  }
</style>

<table class="layout">
  <tr>
    <td class="narrow" style="width: 200px; border-right: 1px solid #999 !important; vertical-align: top;">
      {{foreach from=$databases item=_db key=_dsn}}
        <h3>{{$_dsn}}</h3>

        <label><input type="checkbox" value="show_hidden" onclick="DatabaseExplorer.toggleHidden('{{$_dsn}}', this.checked)" /> Cachés </label>
        <label><input type="checkbox" value="show_empty"  onclick="DatabaseExplorer.toggleEmpty('{{$_dsn}}', this.checked)" /> Vides </label>

        <div style="overflow-y: scroll; height: 800px;">
          <table class="main tbl" id="table-{{$_dsn}}">
            <tr>
              <th></th>
              <th>Desc</th>
              <th>Nb</th>
            </tr>
            {{foreach from=$_db.tables item=_table}}
              <tr class="{{if !$_table.display}} hidden {{/if}} {{if $_table.count == 0}} empty {{/if}}" id="col-{{$_dsn}}-{{$_table.name}}">
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
    <td id="table-data" style="vertical-align: top; overflow: scroll;"></td>
  </tr>
</table>