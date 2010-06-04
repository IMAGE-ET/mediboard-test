{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function startSyncProducts(category_id, object_class){
  if (category_id) {
    var url = new Url("dmi", "httpreq_do_sync_products");
    url.addParam("category_id", category_id);
    url.addParam("object_class", object_class);
    url.requestUpdate("sync_products_"+object_class);
  }
}

function importData(object_class){
  object_class = object_class || "CSociete";
  
  var url = new Url("dmi", "vw_import");
  url.addParam("object_class", object_class);
  url.pop(600, 400, "Import de "+$T(object_class));
}

Main.add(function(){
  new Control.Tabs("dmi-tabs");
});
</script>

<ul class="control_tabs" id="dmi-tabs">
  <li><a href="#tab-CDMI">{{tr}}CDMI{{/tr}}</a></li>
  <li><a href="#tab-CDM">{{tr}}CDM{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<!-- Variables de configuration -->
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
	<input type="hidden" name="dosql" value="do_configure" />
	<input type="hidden" name="m" value="system" />
  
  <table class="form" style="table-layout: fixed;">
    {{assign var=classes value="|"|explode:"CDMI|CDM"}}
    
    {{foreach from=$classes item=class}}
    	<tbody id="tab-{{$class}}" style="display: none;">
        {{mb_include module=system template=inc_config_bool var=active}}
        
        {{if $class == "CDMI"}}
          <tr>
            <th>
              <button class="tick" type="button" onclick="importData('CSociete')">Importer les fournisseurs</button>
            </th>
            <td></td>
          </tr>
          <tr>
            <th>
              <button class="tick" type="button" onclick="importData('CDMI')">Importer les DMI</button>
            </th>
            <td></td>
          </tr>
        {{/if}}
        
        <tr>
          <th>
            {{assign var="var" value="product_category_id"}}
            <select name="{{$m}}[{{$class}}][{{$var}}]" class="notNull">
              <option value="">{{tr}}CProductCategory.select{{/tr}}</option>
              {{foreach from=$categories_list item=category}}
                <option value="{{$category->_id}}" {{if $category->_id==$dPconfig.$m.$class.$var}}selected="selected"{{/if}}>{{$category->name}}</option>
              {{/foreach}}
            </select>
            <button type="button" class="tick" onclick="startSyncProducts($V(this.form['{{$m}}[{{$class}}][{{$var}}]']), '{{$class}}');" >Synchroniser les produits du stock</button>
          </th>
          <td id="sync_products_{{$class}}"></td>
        </tr>
    	</tbody>
    {{/foreach}}
    
    <tr>
      <td colspan="2" class="button">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
  
</form>