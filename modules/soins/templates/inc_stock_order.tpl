{{* $Id: inc_dispensations_list.tpl 6441 2009-06-19 09:47:12Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6441 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$single_line}}
  <script type="text/javascript">
  autoOrder = function(service_id, date_min, date_max) {
    var url = new Url('soins', 'ajax_auto_order');
    url.addParam('service_id', service_id);
    url.addParam('date_min', date_min);
    url.addParam('date_max', date_max);
    url.requestJSON(fillInputs);
  };

  fillInputs = function(data){
    $('list-order').select('input.num[name=quantity]').each(function(e){
      var stock_id = e.form.stock_id.value;
      $V(e, data[stock_id] || 0, true);
    });
  };

  changeOrderPage = function(start) {
    $V(getForm('filter-order').start, start);
  };
  changeOrderPageAlpha = function(letter) {
    var form = getForm('filter-order');
    $V(form.start, 0, false);
    $V(form.letter, letter);
  };

  showCustomOrders = function(){
    var element = $("custom-orders");
    element.show();

    var url = new Url("soins", "httpreq_vw_custom_orders");
    url.addParam("service_id", '{{$service->_id}}');
    url.requestUpdate(element, function(){
      element.unoverflow();
    });
  };

  removeCustomOrder = function(button, delivery_id){
    var form = button.form;
    form.delivery_id.value = delivery_id;
    onSubmitFormAjax(form, showCustomOrders);
  };

  Main.add(function(){
    document.observe("mousedown", function(e){
      if (!Event.element(e).up("#custom-orders")) {
        $("custom-orders").hide();
      }
    });
  });

  {{if $only_service_stocks == 1 && !$endowment_id}}
  Main.add(function(){
    //autoOrder('{{$service->_id}}', '{{$delivrance->_date_min}}', '{{$delivrance->_date_max}}');
  });
  {{/if}}
  </script>
{{/if}}

{{assign var=infinite value=$conf.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$conf.dPstock.CProductStockService.infinite_quantity}}

{{if $single_line}}
  {{foreach from=$stocks item=stock}}
    {{include file="inc_stock_order_line.tpl" nodebug=true}}
  {{/foreach}}
{{else}}
<form name="filter-order" action="?" method="get" onsubmit="checkForm(this); refreshOrders(); return false">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="start" value="{{$start}}" onchange="refreshOrders()" />
  <input type="hidden" name="letter" value="{{$letter}}" onchange="refreshOrders()" />

  <table class="form">
    <tr>
      <th colspan="7" class="category">
        Filtrage des produits
      </th>
    </tr>

    <tr>
      <th style="width: 20%;">
        Lister les commandes effectuées entre
      </th>
      <td class="narrow">
        {{mb_field object=$delivrance field=_date_min form="filter-order" register=true onchange="\$V(this.form.start, 0); refreshOrders()"}}
      </td>

      <td rowspan="2" class="narrow">
        {{if $smarty.now|date_format:'%Y-%m-%d' < $delivrance->_date_min || $smarty.now|date_format:'%Y-%m-%d' > $delivrance->_date_max}}
          <div class="small-info">
            La date courante n'est pas comprise dans l'intervalle spécifié,
            <br />
            les dispensations effectuées ne seront pas affichées.
          </div>
        {{/if}}
      </td>

      <th style="width: 10%;">
        <label for="endowment_id">Dotation</label>
      </th>
      <td>
        {{if $service->_back.endowments|@count}}
          <select name="endowment_id" onchange="$V(this.form.start, 0); $V(this.form.keywords, ''); refreshOrders()" style="max-width: 10em;">
            <option value=""> &ndash; {{tr}}No{{/tr}}</option>
            {{foreach from=$service->_back.endowments item=_endowment}}
              <option value="{{$_endowment->_id}}" {{if $endowment_id == $_endowment->_id}}selected="selected"{{/if}}>{{$_endowment->name}}</option>
            {{/foreach}}
          </select>

          {{if $endowment_id}}
            <button class="print" type="button" onclick="new Url('dPstock','print_endowment').addParam('endowment_id','{{$endowment_id}}').popup(600, 600)">
              Imprimer la dotation
            </button>
          {{/if}}
        {{else}}
          <div class="empty">
            {{tr}}CProductEndowment.none{{/tr}}
          </div>
        {{/if}}
      </td>

      <td>
        {{if !$endowment_id}}
          <input type="hidden" name="only_service_stocks" value="{{$only_service_stocks}}" onchange="$V(this.form.start, 0, false); refreshOrders()" />
          <label style="font-weight: normal;">
            <input type="checkbox" {{if $only_service_stocks == 1}}checked="checked"{{/if}} onchange="$V(this.form.only_service_stocks, this.checked ? 1 : 0)"/>
            Seulement les produits du service
          </label>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>et</th>
      <td>
        {{mb_field object=$delivrance field=_date_max form="filter-order" register=true onchange="\$V(this.form.start, 0); refreshOrders()"}}
      </td>


      {{if !$endowment_id}}
        <th>
          <label for="keywords">Mots clés</label>
        </th>
        <td>
          <input type="text" name="keywords" value="{{$keywords}}" onchange="$V(this.form.start, 0, false); " />
          <button type="button" class="cancel notext" onclick="$V(this.form.keywords, '')">{{tr}}Reset{{/tr}}</button>
        </td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}

      <td>
        {{if !$endowment_id}}
          <input type="hidden" name="only_common" value="{{$only_common}}" onchange="$V(this.form.start, 0, false); refreshOrders()" />
          <label style="font-weight: normal;">
            <input type="checkbox" {{if $only_common == 1}}checked="checked"{{/if}} onchange="$V(this.form.only_common, this.checked ? 1 : 0)" />
            Seulement les produits couramment utilisés
          </label>
        {{/if}}
      </td>
    </tr>

    <tr>
      <td colspan="3"></td>
      <td colspan="3">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=system template=inc_pagination_alpha change_page="changeOrderPageAlpha" current=$letter}}

<hr />

{{mb_include module=system template=inc_pagination change_page="changeOrderPage"
    total=$count_stocks current=$start step=25}}

<table class="tbl">
  <tr>
    <th class="narrow">Code</th>
    <th></th>
    <th>Unité de dispensation</th>
    <th style="width: 30%">Commande</th>
    <th>Déjà effectuées</th>
    {{if !$infinite_service}}
    <th colspan="3" class="narrow">
      Stock du service
    </th>
    {{/if}}
  </tr>
  {{foreach from=$stocks item=stock}}
    <tbody id="stock-{{$stock->_id}}" style="width: 100%">
      {{include file="inc_stock_order_line.tpl" nodebug=true}}
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CProductStockGroup.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <td></td>
    <td class="empty">
      <input type="text" name="comments" size="40" id="other-product" />
      (Nom / description du produit désiré, s'il n'est pas proposé)
    </td>
    <td></td>
    <td>
      <form name="form-create-order" action="?" method="post" onsubmit="$V(this.elements.comments, $('other-product').value); return onSubmitFormAjax(this, refreshLists)">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_delivery_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="date_dispensation" value="now" />
        <input type="hidden" name="service_id" value="{{$service->_id}}" />
        <input type="hidden" name="order" value="1" />
        <input type="hidden" name="delivery_id" value="" />
        <input type="hidden" name="comments" value="" class="notNull" />

        <input type="text" name="quantity" value="1" size="3" />

        <button type="submit" class="tick notext singleclick compact">Faire la demande</button>

        <script type="text/javascript">
          getForm("form-create-order").quantity.addSpinner({min:0});
        </script>
      </form>
    </td>
    <td>
      <button type="button" class="down notext compact" onclick="showCustomOrders(this)">Voir les demandes en cours</button>

      <form name="form-delete-order" action="?" method="post">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_delivery_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="delivery_id" value="" />

        <div style="position: relative; right: 2em;">
          <div id="custom-orders" class="tooltip"
               style="right: 0; max-width: 300px; display: none;"></div>
        </div>
      </form>
    </td>
    {{if !$infinite_service}}
      <td colspan="3"></td>
    {{/if}}
  </tr>
</table>

{{/if}}