{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<script type="text/javascript">
showElementFacture = function(factureItem_id, facture_id){
  //element.up('tr').addUniqueClassName('selected');
  var url = new Url("dPfacturation", "ajax_edit_element");//nom du module, nom du script php qui sera executé
  url.addParam("factureItem_id", factureItem_id);
  url.addParam("facture_id", facture_id);
  url.requestUpdate("vw_element");
}
</script>

<table class="tbl main">
	<tr>
	  <th class="title" colspan="100">
	    {{mb_include module=system template=inc_object_notes object=$facture}}
	    {{tr}}CFactureItem.all{{/tr}}
	  </th>
	</tr>
	<tr>
	   <th>{{tr}}CFactureItem{{/tr}}</th>
	   <th>{{tr}}CFactureItem-prix_ht{{/tr}}</th>
	   <th>{{tr}}CFactureItem-reduction{{/tr}}</th>
	   <th>{{tr}}CFactureItem-taxe{{/tr}}</th>
	   <th>{{tr}}CFactureItem-_ttc{{/tr}}</th>
	</tr>
	{{foreach from=$facture->_ref_items item=_item}}
	  <tr>
	    <td class="text">
	    	<a href="#1" onclick="showElementFacture('{{$_item->_id}}')" title="{{tr}}CFactureItem-title-modify{{/tr}}">
              {{$_item->libelle}}
        </a>
        </td>
	    <td>{{mb_value object=$_item field="prix_ht"}}</td>
	    <td>{{mb_value object=$_item field="reduction"}}</td>
	    <td>{{mb_value object=$_item field="taxe"}}</td>
	    <td>{{mb_value object=$_item field="_ttc"}}</td>
	  </tr>
	{{foreachelse}}
	  <tr>
	   	<td class="button" colspan="4">{{tr}}CFacture-back-items.empty{{/tr}}</td>
	  </tr>
	{{/foreach}}
	  <tr>
	     <th colspan="4">{{tr}}Total{{/tr}}</th>
		 <td>{{mb_value object=$facture field="_total"}}</td>
	  </tr>       
</table>