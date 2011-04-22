{{* $Id: vw_idx_facturecatalogueitem.tpl 11570 2011-03-13 14:05:30Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision: 11570 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
showListCatalogueFacture = function(){
	  var url = new Url("dPfacturation", "ajax_list_catalogueitem");//nom du module, nom du script php qui sera executé
	  url.requestUpdate("vw_list_catalogueitem");
	}
showEditCatalogueFacture = function(catalogue_item){
	  var url = new Url("dPfacturation", "ajax_edit_catalogueitem");//nom du module, nom du script php qui sera executé
	  url.addParam("catalogue_item", catalogue_item);
	  url.requestUpdate("vw_edit_catalogueitem");
	}

Main.add(function(){
	showListCatalogueFacture();
})

</script>

<table class="main">
  <tr>
		<button type="button" class="new" onclick="showEditCatalogueFacture('0',this)">{{tr}}CFacturecatalogueitem-msg-create{{/tr}}</button>
	</tr>
	<tr>
		<td id="vw_list_catalogueitem">
		</td>
		<td style width="40%" id="vw_edit_catalogueitem">
		</td>
	</tr>
</table>