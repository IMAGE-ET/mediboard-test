{{if !$printers|@count}}
  Pas d'imprimantes
{{else}}
  <script type="text/javascript">
  $$(".change")[0].toggle();
    printCompteRendu = function(id) {
      var url = new Url("dPcompteRendu", "ajax_print");
      url.addParam("printer_id", id);
      url.addParam("file_id", Thumb.file_id);
      url.requestUpdate("systemMsg");
      $$('.modal')[0].select('.cancel')[0].click();
    }
  </script>
  
  <table class="tbl">
    <tr>
      <th style="width: 50%">
        {{tr}}CPrinter.name{{/tr}}
      </th>
      <td rowspan="{{math equation="x+1" x=$printers|@count}}">
        <div id="state" class="loading"
          style="width: 100%; height: 100%; background-position: 20%; margin-top: 1em; text-align: center; font-weight: bold;">
          {{tr}}CCompteRendu.generating_pdf{{/tr}}
        </div>
      </td>
    </tr>
    {{foreach from=$printers item=_printer}}
    <tr>
      <td style="line-height: 2;">
         <button onclick="printCompteRendu('{{$_printer->_id}}')" class="print printer" disabled="disabled">
           {{$_printer->_view}}
         </a>
      </td>
    </tr>
    {{/foreach}}
  </table>
  
{{/if}}