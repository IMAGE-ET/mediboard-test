<script>
  Printer = {
    editPrinter: function(id) {
      var url = new Url("dPhospi", "ajax_edit_printer");
      url.addParam("printer_id", id);
      url.requestUpdate("edit_printer");
    },
    refreshList: function(id) {
      var url = new Url("dPhospi", "ajax_list_printers");
      if (id) {
        url.addParam("printer_id", id);
      }
      url.requestUpdate("list_printers");
    },
    after_edit_printer: function(id) {
      Printer.refreshList(id);
      Printer.editPrinter(id);
    }
  };

  Main.add(function() {
    Printer.refreshList();
    Printer.editPrinter('{{$printer_id}}');
  });
</script>

<table class="main">
  <tr>
    <td id="list_printers" style="width: 45%;"></td>
    <!-- Création / Modification de l'imprimante -->
    <td id="edit_printer"></td>
  </tr>
</table>