{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("apply-dmi");
  
  if(document.selPraticienLine) {
    form.praticien_id.value = document.selPraticienLine.praticien_id.value;
  }
});
</script>

<form name="apply-dmi" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: reloadListDMI })">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_dmi_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_dmi_id" value="" />
  <input type="hidden" name="order_item_reception_id" value="{{$lot->_id}}" />
  <input type="hidden" name="product_id" value="{{$product->_id}}" />
  <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
  <input type="hidden" name="operation_id" value="{{$operation_id}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="date" value="now" />
  <input type="hidden" name="signed" value="0" />
  <input type="hidden" name="septic" value="" />

  <table class="layout" style="outline: 2px solid #999; margin: 0.8em; padding: 0.5em; width: 100%; table-layout: fixed;">
    <tr style="white-space: nowrap;">
      <td style="width: 40%; text-align:center;">
        <strong style="font-size: 1.4em; ">
          {{$lot->_ref_order_item->_ref_reference->_ref_product}}
        </strong>
        <br />[{{$lot->code}}]
        {{if $lot->lapsing_date}}
          &mdash; {{mb_value object=$lot field=lapsing_date}}
        {{/if}}
      </td>
      <td style="width: 10%; text-align:center;">
        <button type="button" class="submit" 
            onclick="this.form.septic.value=0;this.form.onsubmit();">
          Poser
        </button>
      </td>
      <td style="width: 10%; text-align:center;">
        <label>
          <input type="checkbox" name="type" value="loan" {{if $dmi->type == "loan"}}checked="checked"{{/if}} /> 
          {{tr}}CPrescriptionLineDMI.type.loan{{/tr}}
        </label>
      </td>
      <td style="width: 10%; text-align:center;">
        <label style="margin-left: 2em;">
          {{tr}}CPrescriptionLineDMI-quantity-court{{/tr}}
          {{mb_field class=CPrescriptionLineDMI field=quantity size=2 increment=true form="apply-dmi" value=1}}
        </label>
      </td>
      <td style="width: 10%; text-align:center;">
        <button type="button" class="trash"
                onclick="this.form.septic.value=1;this.form.onsubmit();">
          Déstérilisé
        </button>
      </td>
    </tr>
  </table>
</form>