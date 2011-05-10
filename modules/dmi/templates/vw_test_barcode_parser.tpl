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
  function parsePhastBarcode(barcode, element, index, onComplete) {
	  var url = new Url("phast", "ajax_search_barcode");
    url.addParam("barcode"   , barcode);
    url.addParam("index"     , index);
    url.requestUpdate(element, { onComplete: onComplete || function(){} });
  }
  	
	function parseAllPhastBarcode(row) {
	  var next = row.next('tr');
	  parsePhastBarcode(row.get("barcode"), row.down(".result"), row.get("index"), function(){
		  if (!next) return;
		  parseAllPhastBarcode(next);
		});
	}  

  window.barcodes = {{$results|@json}};
</script>
{{/if}}

<table class="main tbl">
  <tr>
    <th class="title" colspan="3">Mediboard</th>
		{{if $isPhastInstalled}}
	    <th class="title">
	    	Phast
				<button type="button" class="tick notext" onclick="parseAllPhastBarcode(this.up('tr').next())">{{tr}}Search{{/tr}}</button>
			</th>
		{{/if}}
  </tr>
  {{foreach from=$results item=result key=barcode name=barcode}}
  <tr data-index="{{$smarty.foreach.barcode.index}}" data-barcode="{{$barcode}}">
    <td style="width: 25%">{{$barcode}}</td>
    <td style="width: 25%">{{mb_include module="dmi" template="inc_vw_result_barcode" object=$result.good}}</td>
    <td style="width: 25%" class="{{$result.ok|ternary:'ok':'error'}}">{{mb_include module="dmi" template="inc_vw_result_barcode" object=$result.parsed}}</td>
		
    {{if $isPhastInstalled}}
	    <td style="width: 25%" id="phast_barcode_{{$smarty.foreach.barcode.index}}">
	      <button type="submit" class="tick notext" onclick="parsePhastBarcode('{{$barcode}}', this.next(), '{{$smarty.foreach.barcode.index}}')">{{tr}}Search{{/tr}}</button>
	      <div class="result"></div>
	    </td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>