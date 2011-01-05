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

launchConvert = function() {
  var url = new Url();
  url.addParam("m", "dPstock");
  url.addParam("dosql", "do_convert_product");
  url.requestUpdate("convert-log", {method: "post"});
}

function importLPP(get_version) {
  var url = new Url("dmi", "httpreq_do_import_lpp");
  if (get_version) {
    url.addParam("get_version", 1);
    url.requestUpdate("lpp-version");
  }
  else {
    url.requestUpdate("lpp-import");
  }
}

Main.add(function(){
  new Control.Tabs("dmi-tabs");
});
</script>

<ul class="control_tabs" id="dmi-tabs">
  <li><a href="#tab-CDMI">{{tr}}CDMI{{/tr}}</a></li>
  <li><a href="#tab-CDM">{{tr}}CDM{{/tr}}</a></li>
  <li><a href="#tab-product-codes">Codes produits</a></li>
  <li><a href="#tab-LPP">Base LPP</a></li>
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
    
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          </td>
        </tr>
        
        {{if $class == "CDMI"}}
          <tr>
            <th colspan="2" class="category">Importation</th>
          </tr>
          <tr>
            <td></td>
            <td>
              <ol>
                <li><button class="tick" type="button" onclick="importData('CSociete')">Importer les fournisseurs</button></li>
                <li><button class="tick" type="button" onclick="importData('CDMI')">Importer les DMI</button></li>
                <li><button class="tick" type="button" onclick="importData('CProductReference')">Importer les références (+ familles)</button></li>
              </ol>
            </td>
          </tr>
          <tr>
            <th colspan="2" class="category">Synchronisation</th>
          </tr>
        {{/if}}
        
        <tr>
          <th>
            {{assign var="var" value="product_category_id"}}
            <select name="{{$m}}[{{$class}}][{{$var}}]" class="notNull">
              <option value="">{{tr}}CProductCategory.select{{/tr}}</option>
              {{foreach from=$categories_list item=category}}
                <option value="{{$category->_id}}" {{if $category->_id==$conf.$m.$class.$var}}selected="selected"{{/if}}>{{$category->name}}</option>
              {{/foreach}}
            </select>
            <button type="button" class="tick" onclick="startSyncProducts($V(this.form['{{$m}}[{{$class}}][{{$var}}]']), '{{$class}}');" >Synchroniser les produits du stock</button>
          </th>
          <td id="sync_products_{{$class}}"></td>
        </tr>
    	</tbody>
    {{/foreach}}

    <tbody id="tab-product-codes">
      <tr>
        <th colspan="2" class="category">Conversion de codes produits</th>
      </tr>
      <tr>
        <th>
          <button type="button" class="tick" onclick="launchConvert();">Lancer conversion produits Physiol</button>
        </th>
        <td>
          <div id="convert-log"></div>
        </td>
      </tr>
    </tbody>
  </table>
  
</form>

<div id="tab-LPP" style="display: none;">
  {{mb_include module=system template=configure_dsn dsn=lpp}}
  
  <h2>Import de la base de données LPP</h2>
  
  <table class="tbl" style="table-layout: fixed;">
    <tr>
      <th>{{tr}}Action{{/tr}}</th>
      <th>{{tr}}Status{{/tr}}</th>
    </tr>
    
    <tr>
      <td><button class="tick" onclick="importLPP(true)" >Vérifier la dernière version disponible</button></td>
      <td id="lpp-version"></td>
    </tr>
    
    <tr>
      <td><button class="tick" onclick="importLPP()" >Importer la base de données LPP</button></td>
      <td id="lpp-import"></td>
    </tr>
  </table>
</div>
