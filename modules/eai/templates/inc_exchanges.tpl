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
};

filterColonne = function(input, type) {
  var table = $("exchanges-list");
  table.select(".table-row").invoke("show");

  var term = $V(input);
  if (!term) return;

  table.select(".exchange-"+type).each(function(e) {
    if (!e.getText().like(term)) {
      e.up(".table-row").hide();
    }
  });
};
</script>

{{mb_include module=system template=inc_pagination total=$total_exchanges current=$page change_page='ExchangeDataFormat.changePage' jumper='10' step=25}}

<form name="search-exchange_id" action="" method="get"
      onsubmit="return ExchangeDataFormat.doesExchangeExist('{{$exchange->_class}}', $V($('exchange_id')));" style="float: right; clear: both;">
  <input type="search" id="exchange_id" name="exchange_id" required placeholder="{{tr}}CExchangeDataFormat-exchange_id{{/tr}}" />
  <button type="submit" class="lookup notext">{{tr}}search_exchange_by_id-button{{/tr}}</button>
</form>

<table class="tbl" id="exchanges-list">
  <tr>
    <th class="narrow"></th>
    <th class="narrow"></th>
    <th class="narrow">{{tr}}Actions{{/tr}}</th>
    <th>{{mb_title object=$exchange field=$exchange->_spec->key}}</th>
    <th>{{mb_title object=$exchange field="object_id"}}</th>
    <th>{{mb_title object=$exchange field="id_permanent"}}</th>
    <th>{{mb_colonne class=$exchange->_class field="date_production" order_col=$order_col order_way=$order_way function=orderColonne}}</th>
    <th>
      {{mb_title object=$exchange field="sender_id"}}
      <input type="search" onkeyup="filterColonne(this, 'sender')" size="6" />
    </th>
    <th>
      {{mb_title object=$exchange field="receiver_id"}}
      <input type="search" onkeyup="filterColonne(this, 'receiver')" size="6" />
    </th>
    <th>{{mb_title object=$exchange field="type"}}</th>
    <th>{{mb_title object=$exchange field="sous_type"}}</th>
    {{if $exchange instanceof CExchangeHL7v2}}
      <th>{{mb_title object=$exchange field="code"}}</th>
    {{/if}}
    {{if $exchange instanceof CExchangeHL7v2 || $exchange instanceof CEchangeHprim21 ||$exchange instanceof CExchangeHprimSante}}
      <th>{{mb_title object=$exchange field="version"}}</th>
    {{/if}}
    <th>{{mb_colonne class=$exchange->_class field="date_echange" order_col=$order_col order_way=$order_way function=orderColonne}}</th>
    <th>{{mb_title object=$exchange field="statut_acquittement"}}</th>
    <th>{{mb_title object=$exchange field="_observations"}}</th>
    <th>{{mb_title object=$exchange field="message_valide"}}</th>
    <th>{{mb_title object=$exchange field="acquittement_valide"}}</th>
  </tr>
  {{foreach from=$exchanges item=_exchange}}
    <tbody id="exchange_{{$_exchange->_guid}}" class="table-row">
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