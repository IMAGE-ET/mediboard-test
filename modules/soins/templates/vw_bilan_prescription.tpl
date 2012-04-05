{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

viewListPrescription = function(){
  var url = new Url("soins", "httpreq_vw_bilan_list_prescriptions");
	var oFilterForm = getForm("bilanPrescriptions");
  url.addParam('type_prescription', $V(oFilterForm.type_prescription));
	url.addParam('signee', $V(oFilterForm.signee));
  url.addParam('prat_bilan_id', $V(oFilterForm.prat_bilan_id));
  url.addParam('_date_entree_prevue', $V(oFilterForm._date_entree_prevue));
  url.addParam('_date_sortie_prevue', $V(oFilterForm._date_sortie_prevue));
  url.requestUpdate("list_prescriptions");
}
	
loadSejour = function(sejour_id) {
  var url = new Url("system", "httpreq_vw_complete_object");
  url.addParam("object_class","CSejour");
  url.addParam("object_id",sejour_id);
  url.requestUpdate('bilan_sejour');
}

Main.add(function () {
  viewListPrescription();
  Control.Tabs.create('bilanTabs');
});

</script>

{{if "dPmedicament"|module_active}}
{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="element_selector"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

<table class="main">
  <tr>
    <td colspan="2">
      <form name="bilanPrescriptions" action="?" method="get">
	      <table class="form">
	        <tr>
	          <th class="title" colspan="6">Critères de recherche</th>
	        </tr>
	        <tr>
	          <td>
	            Type de prescription
	            <select name="type_prescription">
	              <option value="sejour" {{if $type_prescription == "sejour"}}selected="selected"{{/if}}>Séjour</option>
	              <option value="sortie_manquante" {{if $type_prescription == "sortie_manquante"}}selected="selected"{{/if}}>Sortie manquante</option>
	              <option value="externe" {{if $type_prescription == "externe"}}selected="selected"{{/if}}>Externe</option>
	            </select>
	          </td>
	          <td>
	            <select name="signee">
	              <option value="0" {{if $signee == "0"}}selected="selected"{{/if}}>Non signées</option>
	              <option value="all" {{if $signee == "all"}}selected="selected"{{/if}}>Toutes</option>
	            </select>
	          </td>
	          <td>
			       <select name="prat_bilan_id">
			          <option value="">&mdash; Sélection d'un praticien</option>
				        {{foreach from=$praticiens item=praticien}}
				        <option class="mediuser" 
				                style="border-color: #{{$praticien->_ref_function->color}};" 
				                value="{{$praticien->_id}}"
				                {{if $praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
				        </option>
				        {{/foreach}}
				      </select>
	          </td>
	          <td>
	            A partir du
	            {{mb_field object=$sejour field="_date_entree_prevue" form="bilanPrescriptions" register="true" canNull=false}}
	          </td>
	          <td>
	            jusqu'au
	            {{mb_field object=$sejour field="_date_sortie_prevue" form="bilanPrescriptions" register="true" canNull=false}}
	          </td>
	          <td>
	            <button class="button tick" type="button" onclick="viewListPrescription();">Filtrer</button>
	          </td>
	        </tr>
	      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td style="width: 150px" id="list_prescriptions"></td>
    <td>
			<ul id="bilanTabs" class="control_tabs">
				<li><a href="#prescription_sejour">Prescription</a></li>
				<li><a href="#bilan_sejour">Séjour</a></li>
      </ul>
			<hr class="control_tabs" />
			<div id="prescription_sejour" style="display: none;">
				<div class="small-info">
	          Veuillez sélectionner un patient pour visualiser sa prescription de séjour.
	      </div>
			</div>
      <div id="bilan_sejour" style="display: none;">
			  <div class="small-info">
          Veuillez sélectionner un patient pour visualiser les informations sur son séjour.
        </div>
			</div>
    </td>
  </tr>
</table>