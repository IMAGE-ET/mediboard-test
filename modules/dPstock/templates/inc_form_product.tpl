<script type="text/javascript">
	
Main.add(function () {
  filterReferences(getForm("filter-products"));
  Control.Tabs.create("tabs-stocks-references", true);
  Control.Tabs.create("product-conditionnement-tabs", false);
  
  var editForm = getForm("edit_product");
  if (!$V(editForm.unit_quantity) && !$V(editForm.unit_title)) {
    toggleFractionnedAdministration.defer(editForm, false);
  }
  else {
    $V(editForm._toggle_fractionned, true);
  }
});

function toggleFractionnedAdministration(form, use) {
  var quantity = $(form.unit_quantity);
  quantity.up("table").select(".arrows").invoke("setVisible", use);
  quantity.disabled = !use;
  quantity.readOnly = !use;
  if (!use) $V(quantity, "");
  
  var title = $(form.unit_title);
  title.up("div").select(".dropdown-trigger").invoke("setVisible", use);
  title.disabled = !use;
  title.readOnly = !use;
  if (!use) $V(title, "");
}

function changePage(start) {
  $V(getForm("filter-products").start, start);
}

function filterReferences(form) {
  var url = new Url("dPstock", "httpreq_vw_products_list");
  url.addFormData(form);
  url.requestUpdate("list-products");
  return false;
}

</script>

<form name="edit_product" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_product_aed" />
<input type="hidden" name="product_id" value="{{$product->_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    {{if $product->_id}}
    <th class="title modify text" colspan="2">{{$product->name}}</th>
    {{else}}
    <th class="title text" colspan="2">{{tr}}CProduct-title-create{{/tr}}</th>
    {{/if}}
  </tr>   
  <tr>
    <th style="width: 1%;">{{mb_label object=$product field="name"}}</th>
    <td>{{mb_field object=$product field="name" size=50}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="category_id"}}</th>
    <td><select name="category_id" class="{{$product->_props.category_id}}">
      <option value="">&mdash; {{tr}}CProductCategory.select{{/tr}}</option>
      {{foreach from=$list_categories item=curr_category}}
        <option value="{{$curr_category->_id}}" {{if $product->category_id == $curr_category->_id || $list_categories|@count==1}} selected="selected" {{/if}} >
        {{$curr_category->_view}}
        </option>
      {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="societe_id"}}</th>
    <td>{{mb_field object=$product field="societe_id" form="edit_product" autocomplete="true,1,50,false,true" style="width: 15em;"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="code"}}</th>
    <td>{{mb_field object=$product field="code"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="description"}}</th>
    <td>{{mb_field object=$product field="description"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="classe_comptable"}}</th>
    <td>{{mb_field object=$product field="classe_comptable"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="renewable"}}</th>
    <td>{{mb_field object=$product field="renewable"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$product field="cancelled"}}</th>
    <td>{{mb_field object=$product field="cancelled"}}</td>
  </tr>
  
  <tr>
    <td colspan="2">
      <ul id="product-conditionnement-tabs" class="control_tabs">
        <li><a href="#conditionnement">{{tr}}CProduct-packaging{{/tr}}</a></li>
        <li><a href="#composition" {{if !$product->unit_title && !$product->unit_quantity}}class="empty"{{/if}}>{{tr}}Composition{{/tr}}</a></li>
      </ul>
      <hr class="control_tabs" />
    </td>
  </tr>

  <tbody id="conditionnement" style="display: none;">
    <tr>
      <th>{{mb_label object=$product field="quantity"}}</th>
      <td>{{mb_field object=$product field="quantity" form="edit_product" increment=true size=4}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$product field="item_title"}}</th>
      <td>{{mb_field object=$product field="item_title" form="edit_product"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$product field="packaging"}}</th>
      <td>{{mb_field object=$product field="packaging" form="edit_product"}}</td>
    </tr>
  </tbody>

  <tbody id="composition" style="display: none;">
    <tr>
      <th></th>
      <td>
        <label>
          <input type="checkbox" name="_toggle_fractionned" onclick="toggleFractionnedAdministration(this.form, this.checked)" /> 
          Permettre l'administration fractionnée
        </label>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$product field="unit_quantity"}}</th>
      <td>{{mb_field object=$product field="unit_quantity" form="edit_product" increment=true size=4}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$product field="unit_title"}}</th>
      <td>{{mb_field object=$product field="unit_title" form="edit_product"}}</td>
    </tr>
  </tbody>
  
  <tr>
    <td class="button" colspan="2">
      {{if $product->_id}}
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$product->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}
      </button>
      
      <!-- purge: a supprimer pour le 27/01/2010 -->
      <input type="hidden" name="_purge" value="0" />
      <script type="text/javascript">
       confirmPurge = function(form) {
         if (confirm("ATTENTION : Vous êtes sur le point de supprimer un produit, ainsi que tous les objets qui s'y rattachent")) {
           form._purge.value = 1;
           confirmDeletion(form,  {
             typeName:'le produit',
             objName:'{{$product->_view|smarty:nodefaults|JSAttribute}}'
           } );
         }
       }
      </script>
      <button type="button" class="cancel" onclick="confirmPurge(this.form)">
        {{tr}}Purge{{/tr}}
      </button>
      <!-- /purge -->
      
      {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>

</table>

</form>