{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=mod_name value=$exchange->_ref_module->mod_name}}

<script type="text/javascript">
orderColonne = function(order_col, order_way) {
  var form = getForm("filterExchange");
  $V(form.order_col, order_col);
  $V(form.order_way, order_way);
  form.onsubmit();
}
</script>

{{if $total_exchanges != 0}}
  {{mb_include module=system template=inc_pagination total=$total_exchanges current=$page change_page='ExchangeDataFormat.changePage' jumper='10' step=25}}
{{/if}}
<table class="tbl">
  <tr>
    <th class="title" colspan="21">{{tr}}{{$exchange->_class}}{{/tr}} - {{$exchange->_ref_group}} {{if $type}}- {{tr}}{{$mod_name}}-msg-{{$type}}{{/tr}}{{/if}}</th>
  </tr>
  <tr>
    <th></th>
    <th>{{tr}}Actions{{/tr}}</th>
    <th>{{mb_title object=$exchange field=$exchange->_spec->key}}</th>
    <th>{{mb_title object=$exchange field="object_class"}}</th>
    <th>{{mb_title object=$exchange field="object_id"}}</th>
    <th>{{mb_title object=$exchange field="id_permanent"}}</th>
    <th>{{mb_colonne class=$exchange->_class field="date_production" order_col=$order_col order_way=$order_way function=orderColonne}}</th>
    <th>{{mb_title object=$exchange field="sender_id"}}</th>
    <th>{{mb_title object=$exchange field="receiver_id"}}</th>
    <th>{{mb_title object=$exchange field="type"}}</th>
    <th>{{mb_title object=$exchange field="sous_type"}}</th>
    {{if $exchange instanceof CExchangeIHE}}
      <th>{{mb_title object=$exchange field="code"}}</th>
    {{/if}}
    {{if $exchange instanceof CExchangeIHE || $exchange instanceof CEchangeHprim21}}
      <th>{{mb_title object=$exchange field="version"}}</th>
    {{/if}}
    <th>{{mb_colonne class=$exchange->_class field="date_echange" order_col=$order_col order_way=$order_way function=orderColonne}}</th>
    <th>{{mb_title object=$exchange field="statut_acquittement"}}</th>
    <th>{{mb_title object=$exchange field="_observations"}}</th>
    <th>{{mb_title object=$exchange field="message_valide"}}</th>
    <th>{{mb_title object=$exchange field="acquittement_valide"}}</th>
  </tr>
  {{foreach from=$exchanges item=_exchange}}
    <tbody id="exchange_{{$_exchange->_guid}}">
      {{mb_include template=inc_exchange object=$_exchange}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="21" class="empty">
        {{tr}}{{$exchange->_class}}.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>