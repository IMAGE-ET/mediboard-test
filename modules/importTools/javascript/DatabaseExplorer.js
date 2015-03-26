DatabaseExplorer = {
  displayTableData: function(dsn, table, start, count, order_column, order_way, where_column, where_value) {
    var url = DatabaseExplorer.makeTableDataUrl(dsn, table, start, count, order_column, order_way, where_column, where_value);
    url.requestUpdate("table-data");

    var table_row = $('col-'+dsn+'-'+table);
    if (table_row) {
      table_row.addUniqueClassName('selected');
    }

    return false;
  },

  makeTableDataUrl: function(dsn, table, start, count, order_column, order_way, where_column, where_value) {
    var url = new Url("importTools", "ajax_vw_table_data");
    url.addParam("dsn", dsn);
    url.addParam("table", table);
    url.addParam("start", start);
    url.addNotNullParam("count", count);
    url.addNotNullParam("order_column", order_column);
    url.addNotNullParam("order_way", order_way);
    url.addNotNullParam("where_column", where_column);
    url.addNotNullParam("where_value", where_value);
    return url;
  },

  displayTableDataWhere: function(dsn, table, count, where_column, where_value) {
    var url = DatabaseExplorer.makeTableDataUrl(dsn, table, 0, count, null, null, where_column, where_value);
    url.addParam("tooltip", 1);
    url.requestModal(500, 500);

    return false;
  },

  displayTableDistinctData: function(dsn, table, column) {
    var url = new Url("importTools", "ajax_vw_table_distinct_data");
    url.addParam("dsn", dsn);
    url.addParam("table", table);
    url.addParam("column", column);
    url.requestModal(400, 600);

    return false;
  },

  toggleHidden: function(dsn, b) {
    $("table-"+dsn).toggleClassName("show_hidden", b);
  },

  toggleEmpty: function(dsn, b) {
    $("table-"+dsn).toggleClassName("show_empty", b);
  }
};
