{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="transmissions">
 

<script type="text/javascript">

toggleTrans = function(trans_class){
	$('list_transmissions').select('tr').each(function(element){
	  trans_class ? (element.hasClassName(trans_class) ?  element.show() : element.hide()) : element.show();
	});
}

{{if @$addTrans}}

refreshTransmission = function(){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("sejour_id", '{{$sejour_id}}');
  url.addParam("addTrans", true);
  url.addParam("with_filter", '{{$with_filter}}');
  url.requestUpdate("transmissions");
}

delCibleTransmission = function() {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, '');
  $V(oForm.object_id, '');
  $V(oForm.libelle_ATC, '');
  oDiv.innerHTML = "";
}

function updateFieldsCible(selected) {
  oForm = document.forms['editTrans'];
  Element.cleanWhitespace(selected);
  if(isNaN(selected.id)){
    $V(oForm.libelle_ATC, selected.id);
  } else {
    $V(oForm.object_id, selected.id);
    $V(oForm.object_class, 'CCategoryPrescription');  
  }
  $('cibleTrans').update(selected.innerHTML.stripTags()).show();
  $V(oForm.cible, '');
}

Main.add(function () {
  var url = new Url("dPprescription", "httpreq_cible_autocomplete");
  url.autoComplete("editTrans_cible", "cible_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsCible
  } );
});

{{/if}}

</script>


<table class="form">
  {{if @$addTrans}}
  <tr>
    <th colspan="6" class="title">
    Ajout d'une transmission
    </th>
  </tr>
  <tr>
	   <td colspan="6">
		    <form name="editTrans" action="?" method="post">
		      Recherche <input name="cible" type="text" value="" />
			    <div style="display:none; width: 350px;" class="autocomplete" id="cible_auto_complete"></div> 
			    <div id="cibleTrans" style="font-style: italic;" onclick="delCibleTransmission();"></div>
			    
		      <input type="hidden" name="dosql" value="do_transmission_aed" />
		      <input type="hidden" name="del" value="0" />
		      <input type="hidden" name="m" value="dPhospi" />
		      <input type="hidden" name="object_class" value="" />
		      <input type="hidden" name="object_id" value="" />
		      <input type="hidden" name="libelle_ATC" value="" />
		      <input type="hidden" name="sejour_id" value="{{$sejour_id}}" />
		      <input type="hidden" name="user_id" value="{{$app->user_id}}" />
		      <input type="hidden" name="date" value="now" />
		      <div style="float: right">
			    <select name="_helpers_text" size="1" onchange="pasteHelperContent(this);" class="helper">
			      <option value="">&mdash; Aide</option>
			      {{html_options options=$transmission->_aides.text.no_enum}}
			    </select>
			    <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CTransmissionMedicale', this.form.text, null, null, null, null, {{$user_id}})">{{tr}}New{{/tr}}</button><br />      
		    </div>
		      {{mb_field object=$transmission field="degre"}}
		      {{mb_field object=$transmission field="type" typeEnum=radio}}
		      <br />
		      {{mb_field object=$transmission field="text"}}
		      <br />
		      <button type="button" class="add" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: refreshTransmission } );">{{tr}}Add{{/tr}}</button>
	      </form>
	    </td>
    </tr>
  {{/if}}
  {{if $ajax || $dialog}}
  <tr>
    <th colspan="7" class="title">
	    <select name="selCible" onchange="toggleTrans(this.value);" style="float: right">
	      <option value="">&mdash; Toutes les cibles</option>
	      {{foreach from=$cibles item=cibles_by_type}}
	        {{foreach from=$cibles_by_type item=_cible}}
	          <option value="{{$_cible}}">{{$_cible|capitalize}}</option>
	        {{/foreach}}
	      {{/foreach}}
	    </select>
	    Observations et Transmissions
    </th>
  </tr>
  {{/if}}
</table>

<table class="tbl">
  <tr>
    <th>
      {{if $with_filter == "1"}}
        {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way function="tri_transmissions"}}
      {{else}}
         {{mb_label class="CSejour" field="patient_id"}}
      {{/if}}
    </th>
    <th>
      {{if $with_filter == "1"}}
        {{mb_colonne class="CAffectation" field="lit_id" order_col=$order_col order_way=$order_way function="tri_transmissions"}}
      {{else}}
        {{mb_label class="CAffectation" field="lit_id"}}
      {{/if}}
    </th>
    <th>{{tr}}Type{{/tr}}</th>
    <th>{{tr}}User{{/tr}}</th>
    <th>
      {{if $with_filter == "1"}}
      {{mb_colonne class="CTransmissionMedicale" field="date" order_col=$order_col order_way=$order_way function="tri_transmissions"}}    
    {{else}}
      {{mb_label class="CTransmissionMedicale" field="date"}}
    {{/if}}
    </th>
    <th>{{tr}}Hour{{/tr}}</th>
    <th>Cible</th>
    <th>{{mb_title class=CTransmissionMedicale field=text}}</th>
  </tr>
  <tbody id="list_transmissions">
  {{assign var=date value=""}}
  {{foreach from=$trans_and_obs item=_objects_by_date}}
	  {{foreach from=$_objects_by_date item=_object}}
			{{include file=../../dPhospi/templates/inc_line_suivi.tpl 
				_suivi=$_object
				show_patient=true
				without_del_form=true
				nodebug=true
			}}
		{{/foreach}}
	{{foreachelse}}
	<tr>
	  <td colspan="8">Aucune transmission</td>
	</tr>
{{/foreach}}
  </tbody>
</table>

</div>