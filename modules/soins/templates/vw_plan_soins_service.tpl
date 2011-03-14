{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
// Refresh du plan de soin
updatePlanSoinsPatients = function(){
  var url = new Url("soins", "ajax_vw_content_plan_soins_service");
	url.addParam("categories_id[]", $V(getForm("selectElts").elts), true);
	url.addParam("date", "{{$date}}");
	url.requestUpdate("content_plan_soins_service");
}

// Selection ou deselection de tous les elements d'une catégorie
selectCategory = function(oCheckboxCat){
  var checked = oCheckboxCat.checked;
	var checkboxs = $('categories').select('input.'+oCheckboxCat.value);
	var count_cat   = checkboxs.length;
	
  checkboxs.each(function(oCheckbox){
	  oCheckbox.checked = checked;
	});
	
	var counter = $("countSelected_"+oCheckboxCat.value);
  counter.update(checked ? count_cat : 0);
  selectTr(counter);
}

// Mise a jour du compteur lors de la selection d'un element
updateCountCategory = function(checkbox, category_guid){
  var counter = $('countSelected_'+category_guid);
	var count = parseInt(counter.innerHTML);
	count = checkbox.checked ? count+1 : count-1;
	counter.update(count);
	selectTr(counter);
}

// Affichage des elements au sein des catégories
toggleElements = function(category_guid){
  $('categories').select('.category_'+category_guid).invoke('toggle');
}

selectTr = function(counter){
  var count = parseInt(counter.innerHTML);
	count ? counter.up("tr").addClassName("selected") : counter.up("tr").removeClassName("selected");
}

Main.add(function(){
  updatePlanSoinsPatients();
});	

</script>

<table class="main">
	<tr>
		<th class="title" colspan="3">Gestion des activités du service {{$service->_view}}</th>
	</tr>
	<tr>
		<td class="narrow">
			<form name="selectElts" action="?" method="get">
				<table class="tbl" id="categories">
					<tr>
						<th class="title">
							<button class="cancel notext" style="float: left">{{tr}}Reset{{/tr}}</button>
	            Activités</th>
					</tr>
				  {{foreach from=$categories key=_chapitre item=_cats_by_chap}}
				    <tr>
				      <th>{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</th>
				    </tr> 
				    {{foreach from=$_cats_by_chap item=_elements}}
				      {{foreach from=$_elements item=_element name=elts}}
				        {{if $smarty.foreach.elts.first}}
				        {{assign var=category value=$_element->_ref_category_prescription}}
				        <tr>
				          <td>
										<input type="checkbox" onclick="selectCategory(this);" value="{{$category->_guid}}" />
			              <strong onclick="toggleElements('{{$category->_guid}}');">
			              	<a href="#" style="display: inline;">{{$category}} (<span id="countSelected_{{$category->_guid}}">0</span>/{{$_elements|@count}})</a>
										</strong>
				          </td>
				        </tr>
				        {{/if}}
				        <tr class="category_{{$category->_guid}}" style="display: none;">
				          <td style="text-indent: 2em;">
				            <label>
											<input type="checkbox" name="elts" value="{{$_element->_id}}" class="{{$category->_guid}}" onclick="updateCountCategory(this, '{{$category->_guid}}');" />
					            {{$_element}}
										</label>
				          </td>
				        </tr>
				      {{/foreach}}
				    {{/foreach}}
				  {{/foreach}}			
				</table>
      </form>
		</td>
		<td id="content_plan_soins_service"></td>
  </tr>
</table>