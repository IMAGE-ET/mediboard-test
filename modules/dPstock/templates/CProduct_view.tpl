{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var url = new Url("dPstock", "httpreq_vw_product_consumption_graph");
  url.addParam("product_id", {{$object->_id}});
  url.requestUpdate("product-consumption-{{$object->_id}}");
})
</script>

{{mb_include module=system template=CMbObject_view}}

<div id="product-consumption-{{$object->_id}}"></div>

{{if $object->canEdit()}}
  <table class="main tbl">
    <tr>
      <td class="button">
        <button type="button" class="edit" onclick="location.href='?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$object->_id}}'">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
    </tr>
  </table>
{{/if}}
