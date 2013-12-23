{{if !$printers|@count}}
  Pas d'imprimantes
  {{mb_return}}
{{/if}}
<script>
  printEtiquette = function(id) {
    var url = new Url("hospi", "print_etiquettes");
    url.addParam("object_id", '{{$object_id}}');
    url.addParam("object_class", '{{$object_class}}');
    url.addParam("modele_etiquette_id", '{{$modele_etiquette_id}}');
    url.addParam("printer_id", id);
    url.requestUpdate("systemMsg");
    Control.Modal.close();
  }

  printCompteRendu = function(id) {
    var url = new Url("compteRendu", "ajax_print");
    url.addParam("printer_id", id);
    url.addParam("file_id", Thumb.file_id);
    url.requestUpdate("systemMsg");
    Control.Modal.close();
  }
</script>

<table class="tbl">
  <tr>
    <th {{if !$mode_etiquette}}style="width: 50%"{{/if}}>
      {{tr}}CPrinter{{/tr}}
    </th>
    {{if !$mode_etiquette}}
      <td rowspan="{{math equation="x+1" x=$printers|@count}}">
        <div id="state" class="loading"
          style="width: 100%; height: 100%; background-position: 20%; margin-top: 1em; text-align: center; font-weight: bold;">
          {{tr}}CCompteRendu.generating_pdf{{/tr}}
        </div>
      </td>
    {{/if}}
  </tr>
  {{foreach from=$printers item=_printer}}
  <tr>
    <td style="line-height: 2;">
       <button onclick="
         {{if $mode_etiquette}}
           printEtiquette('{{$_printer->_id}}');
         {{else}}
           printCompteRendu('{{$_printer->_id}}');
         {{/if}}"
         class="print printer" {{if !$mode_etiquette}}disabled="disabled"{{/if}}>
         {{$_printer}}
       </button>
    </td>
  </tr>
  {{/foreach}}
</table>
