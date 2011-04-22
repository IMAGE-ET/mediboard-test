{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision: 7667 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
submitCatalogueFactureItem = function(form) {
	{
    onSubmitFormAjax(form, 
    {
        onComplete: function()
        {
    				showEditCatalogueFacture({{$catalogue_item->facturecatalogueitem_id}});
    				showListCatalogueFacture();
        }
    });
  }
  return false;
}
</script>

<form name="editcataloguefactureitem" action="?m={{$m}}" method="post" onsubmit="return submitCatalogueFactureItem(this)">
	<input type="hidden" name="m" value="dPfacturation" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="@class" value="CFacturecatalogueitem" />
	{{mb_key object=$catalogue_item}}
	
	<table class="form">
		<tr>
			<th class="title" colspan="2">{{tr}}CFacturecatalogueitem-msg-create{{/tr}}</th>
		</tr>
		<tr>
			<th>{{mb_label object=$catalogue_item field="libelle"}} </th>
			<td>{{mb_field object=$catalogue_item field="libelle"}}</td>
		</tr>
		<tr>
	    <th>{{mb_label object=$catalogue_item field="prix_ht"}}</th>
	    <td>{{mb_field object=$catalogue_item field="prix_ht"}}</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$catalogue_item field="taxe"}}</th>
	    <td>{{mb_field object=$catalogue_item field="taxe"}}</td>
	  </tr>
	  <tr>
	    <td class="button" colspan="2">
	      <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
	      <button class="trash" type="button" 
	      				onclick="confirmDeletion(this.form,{ajax: true, typeName:'l\'element',objName:'{{$catalogue_item->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
	    </td>
	  </tr>
	</table>
	
</form>