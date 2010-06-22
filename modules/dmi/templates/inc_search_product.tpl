{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
getTypeValue = function(button){
  var checkbox = $(button).up().down('input[name=type]');
  return checkbox.checked ? checkbox.value : "";
}

getQtyValue = function(button){
  var input = $(button).up().down('input[name=quantity]');
  return input.value;
}

Main.add(function(){
  $("dmi_quantity").addSpinner({min: 1});
});
</script>

<div style="text-align: center; outline: 2px solid #999; margin: 0.8em; padding: 0.5em;">
  <button style="float: right;" type="button" class="trash"
          onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}', 1, getTypeValue(this), getQtyValue(this))">
    Déstérilisé
  </button>
  
  <button style="float: left;" type="button" class="submit" 
          onclick="addDMI('{{$product->_id}}','{{$product_order_item_reception->_id}}', 0, getTypeValue(this), getQtyValue(this))">
    Poser
  </button>
  
  <label>
    <input type="checkbox" name="type" value="loan" {{if $product->_dmi_type == "loan"}}checked="checked"{{/if}} /> 
    {{tr}}CPrescriptionLineDMI.type.loan{{/tr}}
  </label>
  
  <label style="margin-left: 2em;">
    {{tr}}CPrescriptionLineDMI-quantity-court{{/tr}}
    {{mb_field object=$prescription_line_dmi field=quantity id=dmi_quantity size=2}}
  </label>
</div>