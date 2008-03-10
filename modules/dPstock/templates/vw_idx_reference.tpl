<script type="text/javascript">
function pageMain() {
  PairEffect.initGroup("productToggle", { bStartVisible: true });
}
</script>
<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="tab" value="vw_idx_reference" />
        <label for="category_id" title="Choisissez une catégorie">Catégorie</label>
        <select name="category_id" onchange="this.form.submit()">
          <option value="-1" >&mdash; Choisir une catégorie &mdash;</option>
        {{foreach from=$list_categories item=curr_category}} 
          <option value="{{$curr_category->category_id}}" {{if $curr_category->category_id == $category->category_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
      </form>
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
            <td>{{$curr_reference->quantity}}</td>
            <td>{{$curr_reference->price|string_format:"%.2f"}}</td>
            <td>{{$curr_reference->_unit_price|string_format:"%.2f"}}</td>
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
      {{if $reference->product_id}}<input type="hidden" name="product_id" value="{{$reference->product_id}}" />{{/if}}
      {{if $reference->societe_id}}<input type="hidden" name="societe_id" value="{{$reference->societe_id}}" />{{/if}}
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
            <a href="?m={{$m}}&amp;tab=vw_idx_product&amp;product_id={{$reference->_ref_product->_id}}" title="Voir ou modifier le produit">
              <b>{{$reference->_ref_product->_view}}</b>
            </a><br />
            {{$reference->_ref_product->description|nl2br}}
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