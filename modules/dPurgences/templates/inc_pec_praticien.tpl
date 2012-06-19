{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{** 
  * Permet un accès la prise en charge UPATOU, la crée si elle n'existe pas
  * 
  * @param $listPrats array|CMediusers Praticiens disponibles
  * @param $rpu CRPU Résumé de passage aux urgences
  *}}

{{assign var=sejour  value=$rpu->_ref_sejour}}
{{assign var=consult value=$rpu->_ref_consult}}

<script type="text/javascript">
  checkPraticien = function(oForm){
    var prat = oForm.prat_id.value;
    if (prat == ""){
      alert("Veuillez sélectionner un praticien");
      return false;
    }
    return true;
  }
</script>

{{if $consult}}
  {{if ($sejour->type != "urg" && !$sejour->UHCD) ||  $rpu->mutation_sejour_id}}
    <strong>{{mb_value object=$sejour field=type}}</strong>
  	<br/>
  	<a class="button search" title="Voir le dossier complet du patient" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$sejour->patient_id}}">
  	  Dossier Complet
  	</a>
  
  {{else}}
  	{{if !$consult->_id}}
  		{{if !$sejour->sortie_reelle || $conf.dPurgences.pec_after_sortie}}
  			{{if $can->edit}}
          {{main}}
            var form = getForm("createConsult-{{$rpu->_id}}");
            var field = form._datetime;
            var dates = {
              limit: {
                start: '{{$sejour->entree|iso_date}}',
                stop: '{{$sejour->sortie|iso_date}}'
              }
            };
            
            var datepicker = Calendar.regField(field, dates);
            var view = datepicker.element;
            view.style.width = "16px";
            
            datepicker.icon.observe("click", function(){
              view.style.width = null;
            });
            
          {{/main}}
          
    			<form name="createConsult-{{$rpu->_id}}" method="post" action="?" onsubmit="return checkForm(this);" class="prepared">
    			  <input type="hidden" name="dosql" value="do_consult_now" />
    			  <input type="hidden" name="m" value="dPcabinet" />
    			  <input type="hidden" name="del" value="0" />
    			  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
    			  <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}" />   
            <input type="hidden" name="date_at" value="{{$rpu->date_at}}" />
    			  
    				<div style="white-space: nowrap;">
    	        <select name="prat_id" class="ref notNull" style="width: 10em;">
    	          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$listPrats selected=$sejour->praticien_id}}
    	        </select>
    	        <input type="hidden" name="_datetime" value="" class="dateTime" />
    				</div>
    			  
    			  <button type="submit" class="new" onclick="return checkPraticien(this.form)">Prendre en charge</button>
    			</form>
  			{{else}}
  			  &mdash;
  			{{/if}}
  		{{else}}
  		  <em>{{tr}}CRPU-ATU-missing{{/tr}}</em>
  		{{/if}}
  	{{else}}
  	  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult->_ref_praticien}}
  		{{if $can->edit}}
  		<br />
  		<a class="button search" title="Prise en charge" href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
  		  Voir prise en charge
  		</a>
  		{{/if}}
  	{{/if}}
  {{/if}}
{{/if}}
