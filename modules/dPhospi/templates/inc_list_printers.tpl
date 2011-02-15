<script type="text/css">
updateSelected = function(id) {
  removeSelected();
  var printer = $("printer-" + id);
  printer.addClassName("selected");
}

removeSelected = function() {
  var printer = $$(".oprinter.selected")[0];
  if (printer) {
    printer.removeClassName("selected");
  }
}
</script>

<button class="new" onclick="removeSelected(); Printer.editPrinter(0)">{{tr}}Create{{/tr}}</button>

<table class="tbl printerlist">
  <tr>
    <th class="title">
      {{tr}}CPrinter.list{{/tr}}
    </th>
  </tr>
  {{foreach from=$printers item=_printer}}
    <tr id='printer-{{$_printer->_id}}' class="oprinter {{if $_printer->_id == $printer_id}}selected{{/if}}">
      <td>
        <a href="#1" onclick="Printer.editPrinter('{{$_printer->_id}}'); updateSelected('{{$_printer->_id}}');">
         {{$_printer->_view}}
        </a>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td>
        {{tr}}CPrinter.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>