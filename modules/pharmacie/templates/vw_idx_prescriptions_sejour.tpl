{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPmedicament" script="medicament_selector"}}
{{mb_script module="dPmedicament" script="equivalent_selector"}}
{{mb_script module="dPprescription" script="element_selector"}}
{{mb_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

function markAsSelected(element) {
  $("prescriptions-list").select('.selected').each(function (e) {e.removeClassName('selected')});
  $(element).up(1).addClassName('selected');
}

function updatePatientsListHeight() {
  var vpd = document.viewport.getDimensions(),
      scroller = $("left-column").down(".scroller"),
      pos = scroller.cumulativeOffset();
  scroller.setStyle({height: (vpd.height - pos[1] - 6)+'px'});
}

function updateListPrescrition(){
	var tr_selected = $('prescriptions-list').select('tr.selected')[0];
	var url = new Url("pharmacie", "httpreq_vw_list_prescriptions");
	url.addFormData(getForm("filterForm"));
	url.requestUpdate('prescriptions-list', { onComplete: function(){
	  if(tr_selected){
      $(tr_selected.id).addUniqueClassName('selected');
		}
	} });
}

// Validations de toutes les prescriptions qui ont le meme score
function valideAllPrescriptions(list_prescriptions){
  var oForm = getForm("valideAllPresc");
	
	$H(list_prescriptions).each(function(prescription_id){
	if (!Object.isFunction(prescription_id.value))
	  oForm.insert(DOM.input( {
		      type: 'hidden', 
          name:'prescriptions_pharma['+prescription_id.value+']',
          value: prescription_id.value
      }  )); 
	});
	
	return onSubmitFormAjax(oForm, { onComplete: function(){
	   updateListPrescrition();
	 } } );
}


Main.add(function () {
  updatePatientsListHeight();
	updateListPrescrition();
	setInterval(updateListPrescrition, 300000);
});

</script>


<form name="valideAllPresc" action="?" method="post">
	<input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
  <input type="hidden" name="del" value="0" />
	<input type="hidden" name="mode_pharma" value="1" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
</form>


<table class="main">
  <tr>
    <td colspan="2">
      <form name="filterForm" method="get" action="?">
			  <input type="hidden" name="m" value="{{$m}}" />
	
        <table class="form">
        	<tr>
        	  <th class="title" colspan="6">
        	  	Filtres pour l'affichage des prescriptions de séjour
        	  </th>
					</tr>
          <tr>
			      <th>A partir du</th>
			      <td>  
			        {{mb_field object=$filter_sejour field="_date_entree" form=filterForm canNull=false register=true}}
			      </td>
			      <th>Jusqu'au</th>
			      <td>
			        {{mb_field object=$filter_sejour field="_date_sortie" form=filterForm canNull=false register=true}}
	          </td>
            <td>
              <select name="service_id">
                <option value="">&mdash; Service</option>
                {{foreach from=$services item=_service}}
                <option value="{{$_service->_id}}" {{if $service_id == $_service->_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th>Praticien</th>
            <td>
              <select name="praticien_id">
                <option value="">&mdash; Praticien</option>
                {{foreach from=$praticiens item=_praticien}}
                <option class="mediuser" 
                        style="border-color: #{{$_praticien->_ref_function->color}};" 
                        value="{{$_praticien->_id}}" {{if $praticien_id == $_praticien->_id}}selected="selected"{{/if}}>{{$_praticien->_view}}</option>
                {{/foreach}}
              </select>
            </td>
            <td colspan="2">
              <select name="valide_pharma">
                <option value="0" {{if $valide_pharma == "0"}}selected="selected"{{/if}}>Seulement les non validées</option>
                <option value="1" {{if $valide_pharma == "1"}}selected="selected"{{/if}}>Toutes</option>
              </select>
            </td>
            <td>
              <button class="tick" type="button" onclick="updateListPrescrition();">Filtrer</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
	<tr>
    <td style="width: 270px;" id="left-column">
		  <div style="{{if $smarty.session.browser.name == "msie" && $smarty.session.browser.majorver < 8}}overflow:visible; overflow-x:hidden; overflow-y:auto; padding-right:15px;{{else}}overflow: auto;{{/if}} height: 500px;" class="scroller">
      <table class="tbl" id="prescriptions-list" style="width:270px;">  
			</table>
			</div>
    </td>
    <td>
      <div id="prescription_pharma">
      {{include file="../../dPprescription/templates/inc_vw_prescription.tpl" mode_protocole=0 pharma=1 mode_pack=0}}
			</div>
    </td>
  </tr>
</table>