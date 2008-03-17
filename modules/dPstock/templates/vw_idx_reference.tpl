{{mb_include_script module="dPstock" script="product_selector"}}

<script type="text/javascript">
function pageMain() {
  PairEffect.initGroup("productToggle", { bStartVisible: true });
}
</script>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      {{include file="inc_category_selector.tpl"}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0">
        Nouvelle réference
      </a>

    {{if $category->category_id}}
    <h3>{{$category->_view}}</h3>
      <table class="tbl">
        <tr>
          <th>Fournisseur</th>
          <th>Quantité</th>
          <th>Prix</th>
          <th>P.U.</th>
        </tr>
        
        <!-- Products list -->
        {{foreach from=$category->_ref_products item=curr_product}}
        <tr id="product-{{$curr_product->_id}}-trigger">
          <td colspan="4">
            <a style="display: inline; float: right; font-weight: normal;" href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id=0&amp;product_id={{$curr_product->_id}}">
              Nouvelle référence
            </a>
            {{$curr_product->_view}} ({{$curr_product->_ref_references|@count}} références)
          </td>
        </tr>
        <tbody class="productToggle" id="product-{{$curr_product->_id}}">
        
        <!-- Références list of this Product -->
        {{foreach from=$curr_product->_ref_references item=curr_reference}}
          <tr {{if $curr_reference->_id == $reference->_id}}class="selected"{{/if}}>
            <td><a href="?m={{$m}}&amp;tab=vw_idx_reference&amp;reference_id={{$curr_reference->_id}}" title="Voir ou modifier la référence">{{$curr_reference->_ref_societe->_view}}</a></td>
            <td>{{mb_value object=$curr_reference field=quantity}}</td>
            <td>{{mb_value object=$curr_reference field=price}}</td>
            <td>{{mb_value object=$curr_reference field=_unit_price}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="4">Aucune réference pour ce produit</td>
          </tr>
        {{/foreach}}
        </tbody>
      {{foreachelse}}
        <tr>
          <td colspan="4">Aucun produit dans cette catégorie</td>
        </tr>
      {{/foreach}}
      </table>
    {{/if}}
    </td>


    <td class="halfPane">
      {{if $can->edit && $reference->product_id || $reference->societe_id}}
      <form name="edit_reference" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_reference_aed" />
	    <input type="hidden" name="reference_id" value="{{$reference->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $reference->_id}}
          <th class="title modify" colspan="2">Modification de la référence {{$reference->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'une référence</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="societe_id"}}</th>
          <td><select name="societe_id" class="{{$reference->_props.societe_id}}">
            <option value="">&mdash; Choisir un Fournisseur</option>
            {{foreach from=$list_societes item=curr_societe}}
              <option value="{{$curr_societe->societe_id}}" {{if $reference->societe_id == $curr_societe->_id}} selected="selected" {{/if}} >
              {{$curr_societe->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="product_id"}}</th>
          <td>
            <input type="hidden" name="product_id" value="{{$reference->product_id}}" class="{{$reference->_props.product_id}}" />
            <input type="text" name="product_name" value="{{$reference->_ref_product->name}}" size="30" readonly="readonly" ondblclick="ProductSelector.init()" />
            <button class="search" type="button" onclick="ProductSelector.init()">Chercher</button>
            <script type="text/javascript">
            ProductSelector.init = function(){
              this.sForm = "edit_reference";
              this.sId   = "product_id";
              this.sView = "product_name";
              this.pop({{$reference->product_id}});
            }
            </script>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="quantity"}}</th>
          <td>{{mb_field object=$reference field="quantity"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$reference field="price"}}</th>
          <td>{{mb_field object=$reference field="price"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $reference->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la référence',objName:'{{$reference->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>