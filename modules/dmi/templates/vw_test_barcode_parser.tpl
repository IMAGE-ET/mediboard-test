{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $isPhastInstalled}}
<script type="text/javascript">
  function parsePhastBarcode(barcode, element, index) {
	  var url = new Url("phast", "ajax_search_barcode");
    url.addParam("barcode"   , barcode);
    url.addParam("index"     , index);
    url.requestUpdate(element);
  }

  window.barcodes = {{$results|@json}};
</script>
{{/if}}

<table class="main tbl">
  <tr>
    <th class="title" colspan="3">Mediboard</th>
    <th class="title">Phast</th>
  </tr>
  {{foreach from=$results item=result key=barcode name=barcode}}
  <tr>
    <td style="width: 25%">{{$barcode}}</td>
    <td style="width: 25%">{{mb_include module="dmi" template="inc_vw_result_barcode" object=$result.good}}</td>
    <td style="width: 25%" class="{{$result.ok|ternary:'ok':'error'}}">{{mb_include module="dmi" template="inc_vw_result_barcode" object=$result.parsed}}</td>
    {{if $isPhastInstalled}}
    <td style="width: 25%" id="phast_barcode_{{$smarty.foreach.barcode.index}}">
      <button type="submit" class="tick notext" onclick="parsePhastBarcode('{{$barcode}}', this.next(), '{{$smarty.foreach.barcode.index}}')">{{tr}}Search{{/tr}}</button>
      <div></div>
    </td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>