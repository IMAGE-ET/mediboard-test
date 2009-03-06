<script type="text/javascript">

function startSyncProducts(category_id){
  if (category_id) {
    var url = new Url;
    url.setModuleAction("dmi", "httpreq_do_sync_products");
    url.addParam("category_id", category_id);
    url.requestUpdate("do_sync_products");
  }
}

</script>

<!-- Variables de configuration -->
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
	<input type="hidden" name="dosql" value="do_configure" />
	<input type="hidden" name="m" value="system" />
	<table class="form">
	  {{assign var="class" value="CDMI"}}
	  {{assign var="var" value="active"}}
	  <tr>
	    <th class="category">Activation de la gestion des DMI dans la prescription</th>
	  </tr>
	  <tr>  
	    <td colspan="6" style="text-align: center">
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
	      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
	      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
	    </td>             
	  </tr>
	  <tr>
	    <td class="button" colspan="100">
	      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
	    </td>
	  </tr>
  </table>
</form>

<table class="form">  
  <tr>
    <th class="category" colspan="2">
      Synchronisation
    </th>
  </tr>
  <tr>
    <td>
      <form name="sync-products" action="" onsubmit="return false">
        {{assign var="class" value="CDMI"}}
        {{assign var="var" value="product_category_id"}}
        <select name="{{$m}}[{{$class}}][{{$var}}]" class="notNull">
          <option value="">{{tr}}CProductCategory.select{{/tr}}</option>
          {{foreach from=$categories_list item=category}}
            <option value="{{$category->_id}}" {{if $category->_id==$dPconfig.$m.$class.$var}}selected="selected"{{/if}}>{{$category->name}}</option>
          {{/foreach}}
        </select>
        <button class="tick" onclick="if (!checkForm(this.form)) return false; startSyncProducts($V(this.form['{{$m}}[{{$class}}][{{$var}}]']));" >Synchroniser les produits du stock</button>
      </form>
    </td>
    <td id="do_sync_products"></td>
  </tr>
</table>