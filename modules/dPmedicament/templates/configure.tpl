{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var BCBScripts = {
  install: function (sDSN) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_do_bcbscripts");
    url.addParam("action", "install");
    url.addElement(document.DoBCBScripts.password);
    url.requestUpdate("BCBSripts");
  },
  
  test: function (sDSN) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_do_bcbscripts");
    url.addParam("action", "test");
    url.requestUpdate("BCBSripts");
  }
}

</script>

<table class="tbl">

<tr>
  <th class="title" colspan="100">
    {{tr}}BCBScripts{{/tr}}
  </th>
</tr>

<!-- Install -->
<tr>
  <td>
    <button type="button" class="search" onclick="BCBScripts.test();">
      {{tr}}BCBScripts-test{{/tr}}
    </button>
  </td>
  <td id="BCBSripts" rowspan="2" />
</tr>

<tr>
  <td>
    <form name="DoBCBScripts" action="?" method="get">
      <label for="password">{{tr}}BCBScripts_password{{/tr}}</label>
      <input name="password" type="password" />
    </form>
    
    <button type="button" class="search" onclick="BCBScripts.install()">
      {{tr}}BCBScripts-install{{/tr}}
    </button>
    
  </td>
</tr>

</table>


<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <!-- Niveau d'affichage des produits pour la recherche dans les classes ATC -->  
  <tr>
    <th class="category" colspan="100">Configuration recherche ATC</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CBcbClasseATC"}}
    {{assign var="var" value="niveauATC"}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>2</option>
        <option value="3" {{if 3 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>3</option>
        <option value="4" {{if 4 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>4</option>
        <option value="5" {{if 5 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>5</option>
        <option value="6" {{if 6 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>6</option>
      </select>
    </td>
  </tr>
    
  <!-- Niveau d'affichage des produits pour la recherche dans les classes ATC -->  
  {{assign var="class" value=CBcbProduit}}
  <tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    {{assign var=var value=use_cache}}
    <th colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <label for="{{$m}}[{{$class}}][{{$var}}]">Oui</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="1" {{if $dPconfig.$m.$class.$var == "1"}}checked="checked"{{/if}}/> 
      <label for="{{$m}}[{{$class}}][{{$var}}]">Non</label>
      <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="0" {{if $dPconfig.$m.$class.$var == "0"}}checked="checked"{{/if}}/> 
    </td>             
  </tr>

  <tr>
    <th class="category" colspan="100">Configuration recherche BCB</th>
  </tr>
  
  <tr>
    {{assign var="class" value="CBcbClasseTherapeutique"}}
    {{assign var="var" value="niveauBCB"}}
    <th colspan="3">
      <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
        {{tr}}config-{{$m}}-{{$var}}{{/tr}}
      </label>  
    </th>
    <td colspan="3">
      <select name="{{$m}}[{{$class}}][{{$var}}]">
        <option value="1" {{if 1 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>1</option>
        <option value="2" {{if 2 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>2</option>
        <option value="3" {{if 3 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>3</option>
        <option value="4" {{if 4 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>4</option>
        <option value="5" {{if 5 == $dPconfig.$m.$class.$var}} selected="selected" {{/if}}>5</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<h2>Base de données BCB</h2>

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-bcbdsn', true);
});
</script>

<ul id="tabs-bcbdsn" class="control_tabs">
  <li><a href="#conf">Configuration des DSN Muliples</a></li>
  <li><a href="#bcb1">Source de données 1</a></li>
  <li><a href="#bcb2">Source de données 2</a></li>
  <li><a  class="wrong" href="#bcb">Ancienne source de données</a></li>
</ul>

<hr class="control_tabs" />

<div id="conf" style="display: none;">

<form name="Config2" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">
  {{assign var=class value=CBcbObject}}
  {{assign var=var value=dsn}}

  <tr>
    <td class="text" rowspan="4" style="width: 70%">
		  <div class="big-info">
		    L'utilisation de la BCB possède désormais 2 sources de données,
		    pour permettre la mise à jour de la base <strong>sans interruption de service</strong>.
		    <br/>
		    Il suffit pour cela de mettre à jour tous les mois la BCB de la façon suivante :
		    <ol>
		    	<li>Déclencher la mise à jour de la source non utilisée</li>
		    	<li>Positionner Mediboard sur cette source de données</li>
		    	<li>Resynchroniser le livret thérapeutique</li>
		    </ol>
		  </div>
    </td>

    <td colspan="2">
    	{{if $dPconfig.$m.$class.$var == "bcb"}}
    	<div class="small-warning">
	    	Ancienne source de données sélectionnée !
    	</div>
    	{{/if}}
    </td>
  </tr>

  {{include file=inc_config_bcb_state.tpl dsn=bcb1}}
  {{include file=inc_config_bcb_state.tpl dsn=bcb2}}
  
  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>

</div>

<div id="bcb1" style="display: none;">
	{{include file="../../system/templates/configure_dsn.tpl" dsn=bcb1}}
</div>

<div id="bcb2" style="display: none;">
	{{include file="../../system/templates/configure_dsn.tpl" dsn=bcb2}}
</div>

<div id="bcb" style="display: none;">
	<div class="big-warning">
	  Cette source de données ne doit plus être utilisée !
	  <br />
	  Merci de <strong>configurer les deux sources de données alternées</strong> 
	  et de <strong>choisir la plus à jour</strong>.
	</div>
	{{include file="../../system/templates/configure_dsn.tpl" dsn=bcb}}
</div>

<script type="text/javascript">

function startBCBGES(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_do_add_bcbges");
  url.requestUpdate("do_add_bcbges");
}

function startUncaseBCBTables(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_do_uncase_bcb_tables");
  url.requestUpdate("uncase_bcb_tables");
}

function startSyncProducts(category_id){
  if (category_id) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_do_sync_products");
    url.addParam("category_id", category_id);
    url.requestUpdate("do_sync_products");
  }
}

var Livret = {
  import: function () {
	  var url = new Url;
	  url.setModuleAction("dPmedicament", "vw_bcb_import");
	  url.pop(400, 400, "Import de fichier CSV");
	},
	purge: function () {
	  if (confirm("Vous êtes sur le point de vider le Livret Thérapeutique\n\nVoulez-vous poursuivre ?")) {
		  var url = new Url;
		  url.setModuleAction("dPmedicament", "httpreq_purge_livret");
		  url.requestUpdate("livret-purge");
	  }
	},
	synchronize: function(){
	  var url = new Url;
	  url.setModuleAction("dPmedicament", "httpreq_synchronize_livret");
	  url.requestUpdate("livret-synchro");
	}
}


</script>
<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startUncaseBCBTables()">Mettre les tables BCB en majuscules</button></td>
    <td class="text" id="uncase_bcb_tables">
      <div class="big-info">
      	Cette action n'est pas nécessaire (et ne fonctionnera pas) sur les serveurs MS Windows.
      	<br />
      	Ces derniers ne sont pas sensibles à la casse pour les noms de table 
      </div>
    </td>
  </tr>
</table>

<h2>Base de données BCBGES</h2>

{{include file="../../system/templates/configure_dsn.tpl" dsn=bcbges}}

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Status</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startBCBGES()" >Importer la base de données BCB GESTION</button></td>
    <td id="do_add_bcbges"></td>
  </tr>
  <tr>
    <td><button class="tick" onclick="Livret.import()" >Importer un fichier CSV de livrets thérapeutiques</button></td>
    <td></td>
  </tr>
  <tr>
    <td><button class="trash" onclick="Livret.purge()" >Vider le livret thérapeutique</button></td>
    <td id="livret-purge"></td>
  </tr>
  <tr>
    <td>
      <button type="button" class="tick" onclick="Livret.synchronize()">Synchroniser le livret Therapeutique</button>
		</td>
		<td id="livret-synchro">
		  {{if $nb_produit_livret_med != $nb_produit_livret_ges}}
		    <div class="small-warning">
		    Attention, les livrets Thérapeutiques 
		    médicaux ({{$nb_produit_livret_med}} produits) 
		    et de gestion ({{$nb_produit_livret_ges}} produits) 
		    ne sont pas synchronisés
		    </div>
		  {{else}}
		    <div class="small-info">
		    Livrets Thérapeutiques médicaux et de gestion synchronisés
		    et contiennent {{$nb_produit_livret_ges}} produits.
		    </div>
		  {{/if}}
		</td>
  </tr>
  <tr>
    <td>
      <form name="sync-products" action="" onsubmit="return false">
        {{assign var="class" value="CBcbProduitLivretTherapeutique"}}
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