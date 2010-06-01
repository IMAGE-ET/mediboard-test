{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="mediusers" script="color_selector"}}

<script type="text/javascript">

ColorSelector.init = function(form_name, color_view){
  this.sForm  = form_name;
  this.sColor = "color";
	this.sColorView = color_view;
  this.pop();
}

Main.add( function(){
	refreshListCategories('{{$category_prescription_id}}', true);
	{{if $category_prescription_id}}
    refreshListElement('{{$element_prescription_id}}','{{$category_prescription_id}}', true);
	{{/if}}
	{{if $element_prescription_id}}
    refreshListCdarr('{{$element_prescription_to_cdarr_id}}','{{$element_prescription_id}}');
	{{/if}}
});

// Refresh Categories
refreshFormCategory = function(category_prescription_id){
  var url = new Url("dPprescription", "httpreq_vw_form_category");
	url.addParam("category_prescription_id", category_prescription_id);
	url.requestUpdate("category-form");
}

refreshListCategories = function(category_prescription_id, without_refresh_element){
  var url = new Url("dPprescription", "httpreq_vw_list_categories");
  url.addParam("category_prescription_id", category_prescription_id);
  url.requestUpdate("categories-list", { onComplete: function(){
	  refreshFormCategory(category_prescription_id);
		if(!without_refresh_element){
		  refreshListElement(null, category_prescription_id);
		}
	} } );
}

refreshListCategoriesCallback = function(category_prescription_id){
  refreshListCategories(category_prescription_id);
}

onSelectCategory = function(category_prescription_id, selected_tr){
	refreshFormCategory(category_prescription_id);
	refreshListElement(null, category_prescription_id);
	refreshListCdarr();
	$('categories-list').select('tr').invoke('removeClassName', 'selected'); 
	if(selected_tr){
	  selected_tr.addClassName('selected');
	}
}

// Refresh elements
refreshListElement = function(element_prescription_id, category_prescription_id, without_refresh_cdarr){
  var url = new Url("dPprescription", "httpreq_vw_list_element");
	url.addParam("category_prescription_id", category_prescription_id);
	url.addParam("element_prescription_id", element_prescription_id);
	url.requestUpdate("elements-list", { onComplete: function(){
	  refreshFormElement(element_prescription_id, category_prescription_id);
		if(!without_refresh_cdarr){
		  refreshListCdarr(null, element_prescription_id);
		}
	} } );
}

refreshFormElement = function(element_prescription_id, category_prescription_id, mode_duplication){
  var url = new Url("dPprescription","httpreq_vw_form_element");
	url.addParam("category_prescription_id", category_prescription_id);
	url.addParam("element_prescription_id", element_prescription_id);
	url.addParam("mode_duplication", mode_duplication);
	url.requestUpdate("element-form");
}

onSelectElement = function(element_prescription_id, category_prescription_id, selected_tr){
  refreshFormElement(element_prescription_id, category_prescription_id);
  refreshListCdarr(null, element_prescription_id);
	$('elements-list').select('tr').invoke('removeClassName', 'selected'); 
	if(selected_tr){
    selected_tr.addUniqueClassName('selected');
  }
}

// Refresh Cdarrs
refreshListCdarr = function(element_prescription_to_cdarr_id, element_prescription_id){
  var url = new Url("dPprescription", "httpreq_vw_list_cdarr");
	url.addParam("element_prescription_to_cdarr_id", element_prescription_to_cdarr_id);
  url.addParam("element_prescription_id", element_prescription_id);
	url.requestUpdate("cdarrs-list", { onComplete: refreshFormCdarr.curry(element_prescription_to_cdarr_id, element_prescription_id) });
}

refreshFormCdarr = function(element_prescription_to_cdarr_id, element_prescription_id){
  var url = new Url("dPprescription", "httpreq_vw_form_cdarr");
  url.addParam("element_prescription_to_cdarr_id", element_prescription_to_cdarr_id);
  url.addParam("element_prescription_id", element_prescription_id);
  url.requestUpdate("cdarr-form");
}

onSelectCdarr = function(element_prescription_to_cdarr_id, element_prescription_id, selected_tr){
  refreshFormCdarr(element_prescription_to_cdarr_id, element_prescription_id);
  $('cdarrs-list').select('tr').invoke('removeClassName', 'selected'); 
  if(selected_tr){
    selected_tr.addUniqueClassName('selected');
  }
}

</script>

<table class="main">
	<tr>
    <td id="categories-list" style="width: 50%;"></td>
		<td id="category-form"></td>
	</tr>
  <tr>
    <td id="elements-list"></td>
    <td id="element-form"></td>
  </tr>
  <tr>
    <td id="cdarrs-list"></td>
    <td id="cdarr-form"></td>
  </tr>
</table>