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
  url.addParam('type', $V(oFilterForm.type));
	url.addParam('signee', $V(oFilterForm.signee));
  url.addParam('praticien_id', $V(oFilterForm.praticien_id));
  url.addParam('_date_min', $V(oFilterForm._date_min));
  url.addParam('_date_max', $V(oFilterForm._date_max));
  url.requestUpdate("list_prescriptions", { waitingText: null } );
}
	

Main.add(function () {
  viewListPrescription();
});

</script>


{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<table class="main">
  <tr>
    <td colspan="2">
      <form name="bilanPrescriptions" action="?" method="get">
	      <table class="form">
	        <tr>
	          <th class="category" colspan="6">Critères de recherche</th>
	        </tr>
	        <tr>
	          <td>
	            Type de prescription
	            <select name="type">
	              <option value="sejour" {{if $type == "sejour"}}selected="selected"{{/if}}>Séjour</option>
	              <option value="sortie_manquante" {{if $type == "sortie_manquante"}}selected="selected"{{/if}}>Sortie manquante</option>
	              <option value="externe" {{if $type == "externe"}}selected="selected"{{/if}}>Externe</option>
	            </select>
	          </td>
	          <td>
	            <select name="signee">
	              <option value="0" {{if $signee == "0"}}selected="selected"{{/if}}>Non signées</option>
	              <option value="all" {{if $signee == "all"}}selected="selected"{{/if}}>Toutes</option>
	            </select>
	          </td>
	          <td>
			       <select name="praticien_id">
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
	          <td class="date">
	            A partir du
	            {{mb_field object=$sejour field="_date_min" form="bilanPrescriptions" register="true" canNull=false}}
	          </td>
	          <td class="date">
	            jusqu'au
	            {{mb_field object=$sejour field="_date_max" form="bilanPrescriptions" register="true" canNull=false}}
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
    <td id="prescription_sejour">
      <div class="big-info">
        Selectionnez une prescription sur la gauche pour la visualiser 
      </div>
    </td>
  </tr>
</table>