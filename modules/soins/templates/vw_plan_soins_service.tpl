{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPprescription" script="plan_soins"}}
{{mb_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

// Refresh du plan de soin
updatePlanSoinsPatients = function(){
  if($('content_plan_soins_service')){
    var url = new Url("soins", "ajax_vw_content_plan_soins_service");
	  url.addParam("categories_id[]", $V(getForm("selectElts").elts), true);
	  url.addParam("date", "{{$date}}");
	  url.requestUpdate("content_plan_soins_service");
	}
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

resetCheckbox = function() {
	($('categories').select('input[#checkbox]')).each(function(oCheckbox){
    oCheckbox.checked = null;
  });
	$('categories').select('tr').each(function(tr){
    tr.removeClassName('selected');
  });
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

addTransmission = function(sejour_id, user_id, transmission_id, object_id, object_class, libelle_ATC, update_plan_soin) {
  var url = new Url("dPhospi", "ajax_transmission");
  url.addParam("sejour_id", sejour_id);
  url.addParam("user_id", user_id);
  url.addParam("update_plan_soin", update_plan_soin);
  
  if (transmission_id != undefined) {
    url.addParam("transmission_id", transmission_id);
  }
  if (object_id != undefined && object_class !=undefined) {
    url.addParam("object_id",    object_id);
    url.addParam("object_class", object_class);
  }
  if (libelle_ATC != undefined) {
    url.addParam("libelle_ATC", libelle_ATC);
  }
  url.requestModal(600, 400);
}

addCibleTransmission = function(sejour_id, object_class, object_id, libelle_ATC, update_plan_soin) {
  addTransmission(sejour_id, '{{$app->user_id}}', null, object_id, object_class, libelle_ATC, update_plan_soin);
}

Main.add(function(){
  updatePlanSoinsPatients();
	Calendar.regField(getForm("updateActivites").date);
});	

</script>

<form name="adm_multiple" action="?" method="get">
  <input type="hidden" name="_administrations">
</form>

<form name="click" action="?" method="get">
  <input type="hidden" name="nb_decalage" value="{{$nb_decalage}}" />
</form>

<form name="addPlanif" action="" method="post">
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administration_id" value="" />
  <input type="hidden" name="planification" value="1" />
  <input type="hidden" name="administrateur_id" value="" />
  <input type="hidden" name="dateTime" value="" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="unite_prise" value="" />
  <input type="hidden" name="prise_id" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="" />
  <input type="hidden" name="original_dateTime" value="" />
</form>

<table class="main">
	<tr>
		<th class="title" colspan="3">
			<form name="updateActivites" action="?" method="get">
				<input type="hidden" name="m" value="{{$m}}" />
				<input type="hidden" name="tab" value="{{$tab}}" />
        
			  Gestion des activités du service 
				<select name="service_id" onchange="this.form.submit();">
					<option value="">&mdash; Service</option>
				  {{foreach from=$services item=_service}}
					  <option value="{{$_service->_id}}" {{if $_service->_id == $service->_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
					{{/foreach}}
				</select>
				le
				<input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
		  </form>
		</th>
	</tr>
	
	{{if $service->_id}}
	<tr>
		<td style="width: 20%;">
			<form name="selectElts" action="?" method="get">
				<!-- Checkbox vide permettant d'eviter que le $V considere qu'il faut retourner true ou false s'il n'y a qu'une seule checkbox -->
				<input type="checkbox" name="elts" value="" style="display: none;"/>
                      
				<table class="tbl" id="categories">
					<tr>
						<th class="title">
							<button type="button" class="cancel notext" style="float: left" onclick="resetCheckbox();">{{tr}}Reset{{/tr}}</button>
	            Activités
						</th>
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
				  {{foreachelse}}
					<tr>
						<td class="empty">Aucune activité</td>
					</tr>
					{{/foreach}}			
				</table>
      </form>
		</td>
		<td id="content_plan_soins_service">
		</td>
  </tr>
	{{else}}
	<tr>
		<td>
			<div class="small-info">
				Veuillez sélectionner un service pour accéder à la gestion des activités de celui-ci.
			</div>
		</td>
	</tr>
	{{/if}}
</table>