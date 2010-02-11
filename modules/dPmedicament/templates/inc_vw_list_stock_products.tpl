{{mb_include module=system template=inc_pagination total=$total current=$start change_page='changePageProducts' step=20}}

<table class="main tbl">
  <tr>
    <th style="width: 0.1%;"></th>
    <th>{{mb_title class=CProduct field=name}}</th>
    <th>Dans le LT</th>
  </tr>
  {{foreach from=$list_products item=_product}}
    <tr>
      <td>
        <form action="?m=dPmedicament" method="post" name="addProduit-{{$_product->_id}}" onsubmit="return onSubmitFormAjax(this, {onComplete: filterProducts.curry(this)})">
          <input type="hidden" name="m" value="dPmedicament" />
          <input type="hidden" name="dosql" value="do_produit_livret_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="group_id" value="{{$g}}"/>
          <input type="hidden" name="code_cip" value="{{$_product->code}}"/>
          
          {{if $_product->_is_valid}}
            <button type="submit" class="{{$_product->_in_livret|ternary:none:add}} notext" {{if $_product->_in_livret}}disabled="disabled"{{/if}}></button>
          {{/if}}
        </form>
      </td>
      <td>{{mb_value object=$_product field=name}}</td>
      <td>{{tr}}bool.{{$_product->_in_livret}}{{/tr}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProduct.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
