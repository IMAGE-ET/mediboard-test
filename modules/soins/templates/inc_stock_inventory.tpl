{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th>Stock précédent</th>
    <th>Articles théoriquement utilisés</th>
    <th>Nouveau stock</th>
    <th>Retour à la pharmacie</th>
  </tr>
  {{foreach from=$destockages item=curr_destockage key=code}}
  {{assign var=stock value=$curr_destockage.stock}}
  <tr>
    {{if $stock->_ref_product }}
    <td>
      <div id="tooltip-content-{{$stock->_id}}" style="display: none;">
      {{foreach from=$stock->_ref_logs item=log}}
        {{mb_value object=$log field=date}} par <b>{{$log->_ref_user->_view}}</b><br />
      {{/foreach}}
      </div>
      <span {{if $stock->_ref_logs|@count>0}}onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$stock->_id}}')"{{/if}}>
        {{$stock->_ref_product->_view}}
      </span>
    </td>
    <td>{{$stock->_ref_product->_unit_title}}</td>
    <td style="text-align: center;">{{$stock->quantity+$curr_destockage.nb_produit}}</td>
    <td style="text-align: center;">{{$curr_destockage.nb_produit}}</td>
    <td>
      <form name="destockage-{{$stock->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDestockagesList})"
      {{if $stock->_ref_logs|@count>0 && false}}style="opacity: 0.5" title="Destockage déjà réalisé durant la période indiquée"{{/if}}>
        <input type="hidden" name="m" value="dPstock" /> 
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_stock_service_aed" />
        <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
        {{assign var=id value=$stock->_id}}
        {{mb_field object=$stock field=quantity form="destockage-$id" increment=1 size=3}}
        <button type="submit" class="tick">Valider</button>
      </form>
    </td>
    <td>
      <form name="retour-{{$code}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDestockagesList})">
        <input type="hidden" name="m" value="dPstock" /> 
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
        <input type="hidden" name="_return" value="1" />
        <input type="hidden" name="_code" value="{{$code}}" />
        <input type="hidden" name="date_reception" value="now" />
        <!-- <input type="hidden" name="date_delivery" value="now" /> -->
        {{mb_field object=$stock field=quantity form="retour-$code" increment=1 size=3}}
        <input type="text" name="code" value="" />
        <button type="submit" class="cancel">Retour</button>
      </form>
    </td>
    {{else}}
    <td colspan="10">Le produit ayant comme code CIP <b>{{$code}}</b> n'existe pas dans le stock</td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProduct.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
