{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=eai script=exchange_data_format ajax=true}}

{{assign var=mod_name value=$exchange->_ref_module->mod_name}}

<script type="text/javascript">
  orderColonne = function (order_col, order_way) {
    var form = getForm("filterExchange");
    $V(form.order_col, order_col);
    $V(form.order_way, order_way);
    form.onsubmit();
  };

  filterColonne = function (input, type) {
    var table = $("exchanges-list");
    table.select(".table-row").invoke("show");

    var term = $V(input);
    if (!term) {
      return;
    }

    table.select(".exchange-" + type).each(function (e) {
      if (!e.getText().like(term)) {
        e.up(".table-row").hide();
      }
    });
  };

  togglePrint = function (status) {
    $("exchanges-list").select("input[name=exchange_checkbox]").each(function (elt) {
      elt.checked = status ? "checked" : "";
    });
  };

  sendMessageForElementsSelected = function () {
    var elements_selected = [];
    $("exchanges-list").select("input[name=exchange_checkbox]:checked").each(function (elt) {
      var tbody = elt.up('tbody');
      elements_selected.push(tbody.get("exchange"));
    });
    elements_selected.reverse();
    elements_selected.each(function (guid) {
      ExchangeDataFormat.sendMessage(guid);
    });
  }

</script>

{{mb_include module=system template=inc_pagination total=$total_exchanges current=$page change_page='ExchangeDataFormat.changePage' jumper='10' step=25}}

<table class="layout" style="width: 100%">
  <tr>
    <td class="narrow">
      <button class="tick" onclick="sendMessageForElementsSelected();">
        {{tr}}ExchangeDataFormat-action-Send selection{{/tr}}
      </button>
    </td>
    <td style="text-align: right">
      <form name="search-exchange_id" action="" method="get"
            onsubmit="return ExchangeDataFormat.doesExchangeExist('{{$exchange->_class}}', $V($('exchange_id')));">
        <input type="search" id="exchange_id" name="exchange_id" required placeholder="{{tr}}CExchangeDataFormat-exchange_id{{/tr}}" size="25"/>
        <button type="submit" class="lookup notext">{{tr}}search_exchange_by_id-button{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>

<table class="tbl" id="exchanges-list">
  <tr>
    <th class="narrow"></th>
    <th class="narrow"></th>
    <th class="narrow"><input type="checkbox" onclick="togglePrint(this.checked);" /></th>
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
    <tbody id="exchange_{{$_exchange->_guid}}" class="table-row" data-exchange="{{$_exchange->_guid}}">
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