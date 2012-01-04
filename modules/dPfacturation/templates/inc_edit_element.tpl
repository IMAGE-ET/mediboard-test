{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
submitFactureItem = function(form) {
	{
    onSubmitFormAjax(form, 
    {
        onComplete: function()
        {
    				showFacture({{$facture->facture_id}});
    				showListFacture();
        }
    });
  }
  return false;
}

showFieldItem = function(catalogue_item_id){
	  //element.up('tr').addUniqueClassName('selected');
	  var url = new Url("dPfacturation", "ajax_edit_element");//nom du module, nom du script php qui sera executé
	  url.addParam("facture_id", "{{$facture->_id}}");
	  url.addParam("catalogue_item_id", catalogue_item_id);
	  url.requestUpdate("vw_element");
	}

updateFactureItem = function(select){
	var value = $V(select);
	showFieldItem(value);
}

</script>

{{if $can->edit}}
<form name="editfactureitem" action="?m={{$m}}" method="post" onsubmit="return submitFactureItem(this)">
	<input type="hidden" name="m" value="dPfacturation" />
	<input type="hidden" name="dosql" value="do_factureitem_aed" />
	<input type="hidden" name="facture_id" value="{{$facture->facture_id}}" />
	<input type="hidden" name="factureitem_id" value="{{$factureitem->factureitem_id}}" />
	<input type="hidden" name="del" value="0" />
	
	<table class="form">
	  <tr>
	    {{if $factureitem->_id}}
	    <th class="title modify" colspan="2">{{tr}}CFactureItem-title-modify{{/tr}} {{$factureitem->_view}}</th>
	    {{else}}
	    <th class="title" colspan="2">{{tr}}CFactureItem-title-create{{/tr}}</th>
	    {{/if}}
	  </tr>
	  {{if !$factureitem->_id}}
	  <tr>
	  	<td colspan="2">
	  		<select name="facture_catalogue_item_id" size="1" onchange="updateFactureItem(this)">
		  		<option value="">&mdash; Choisir un item du catalogue &mdash;</option>
		  		{{foreach from=$listCatalogueItem item=_item}}
		  		  <option value="{{$_item->_id}}" 
		  		         {{if $factureitem->facture_catalogue_item_id == $_item->_id}} selected="selected" {{/if}}>
		  		    {{$_item->libelle}}
		  		  </option>
		  		{{/foreach}}
	    	</select>
	  	</td>
	  </tr>
	   {{/if}} 
	  <tr>
	    <th>{{mb_label object=$factureitem field="libelle"}}</th>
	    <td>
        {{mb_field object=$factureitem field="libelle" form="editfactureitem"
          aidesaisie="validateOnBlur: 0"}}
      </td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$factureitem field="prix_ht"}}</th>
	    <td>{{mb_field object=$factureitem field="prix_ht" increment=true form=editfactureitem}}</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$factureitem field="taxe"}}</th>
	    <td>{{mb_field object=$factureitem field="taxe" increment=true form=editfactureitem}}</td>
	  </tr>
	  <tr>
	  	<th>{{mb_label object=$factureitem field="reduction"}}</th>
	  	<td>{{mb_field object=$factureitem field="reduction" increment=true form=editfactureitem}}</td>
	  </tr>
	  <tr>
	    <td class="button" colspan="2">
	      <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
	     {{if $factureitem->_id}}
	        <button class="trash" type="button" onclick="confirmDeletion(this.form,{ajax: true, typeName:'l\'element',objName:'{{$factureitem->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
	      {{/if}}
	    </td>
	  </tr>        
	</table>
</form>
{{/if}}