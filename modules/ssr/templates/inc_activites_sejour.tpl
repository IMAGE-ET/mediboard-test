{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

selectActivite = function(activite) {
	$("trigger-"+activite).setStyle( {borderWidth: "2px", borderColor: "#000"} );
  $$("div.activite").invoke("hide");
  $("activite-"+activite).show();
}

selectTechnicien = function(kine_id) {
  $V(oFormEvenementSSR.therapeute_id, kine_id);	
  $$("button.ressource").invoke("setStyle", {borderWidth: "1px"} );
  $("technicien-"+kine_id).setStyle( {borderWidth: "2px", borderColor: "#000"} );
  PlanningTechnicien.show(kine_id, null, '{{$bilan->sejour_id}}');
	if($V(oFormEvenementSSR.equipement_id)){
	  PlanningEquipement.show($V(oFormEvenementSSR.equipement_id), '{{$bilan->sejour_id}}');
	}
}

selectEquipement = function(equipement_id) {
  $V(oFormEvenementSSR.equipement_id, equipement_id);
	$$("button.equipement").invoke("setStyle", {borderWidth: "1px"} );
  if(equipement_id){
	  $("equipement-"+equipement_id).setStyle( {borderWidth: "2px", borderColor: "#000"} );
		PlanningEquipement.show(equipement_id,'{{$bilan->sejour_id}}');
	} else {
	  PlanningEquipement.hide();
  }
}

selectElement = function(line_id){
$V(oFormEvenementSSR.line_id, line_id);
  $$("button.line").invoke("setStyle", {borderWidth: "1px"} );
  $("line-"+line_id).setStyle( {borderWidth: "2px", borderColor: "#000"} );
  $$("div.cdarrs").invoke("hide");
	$V(getForm("editEvenementSSR").cdarr, '');
	$("cdarrs-"+line_id).show();
}

submitSSR = function(){
  if(!$V(oFormEvenementSSR.cdarr)){
	  alert("Veuillez selectionner un code SSR");
		return false;
	}
  return onSubmitFormAjax(oFormEvenementSSR, { onComplete: function(){
	  if($V(oFormEvenementSSR.equipement_id)){
		  PlanningEquipement.show($V(oFormEvenementSSR.equipement_id),'{{$bilan->sejour_id}}');
    }
	 	Planification.refresh($V(oFormEvenementSSR.sejour_id));
	}} );
}

refreshPlanningsSSR = function(){
  Planification.refreshSejour('{{$bilan->sejour_id}}');
	PlanningTechnicien.show($V(oFormEvenementSSR.therapeute_id), null, '{{$bilan->sejour_id}}');
	if($V(oFormEvenementSSR.equipement_id)){
	  PlanningEquipement.show($V(oFormEvenementSSR.equipement_id),'{{$bilan->sejour_id}}');
	}
}

var oFormEvenementSSR;
Main.add(function(){
  oFormEvenementSSR = getForm("editEvenementSSR");
	selectTechnicien('{{$bilan->kine_id}}');
});
									
</script>

<style type="text/css">

input.time[readonly]  {
  width:50px;
}

</style>

<form name="editEvenementSSR" method="post" action="?" onsubmit="return submitSSR();">
	<input type="hidden" name="m" value="ssr" />
	<input type="hidden" name="dosql" value="do_evenement_ssr_multi_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="sejour_id" value="{{$bilan->sejour_id}}">
	
	{{mb_field hidden=true object=$evenement_ssr field=equipement_id}}
	{{mb_field hidden=true object=$evenement_ssr field=therapeute_id}}
	<input type="hidden" name="line_id" value="" />
  
	<table class="form">
		<tr>
			<th class="title" colspan="10">Boîte à activités</th>
		</tr>
	  <tr>
	    <th>{{mb_label object=$bilan field=kine_id}}</th>
	    <td><strong>{{mb_value object=$bilan field=kine_id}}</strong></td>
	  </tr>
		<tr>
			<th>Activités</th>
			<td>
				{{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
				  {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	  		    {{foreach from=$_lines_by_cat.element item=_line name=category}}
	            {{if $smarty.foreach.category.first}}
			          {{assign var=category value=$_line->_ref_element_prescription->_ref_category_prescription}}
							  <button id="trigger-{{$category->_guid}}" class="search activite" type="button" onclick="selectActivite('{{$category->_guid}}')">
							    {{$category}}
							  </button>
				      {{/if}}
				    {{/foreach}}
				  {{/foreach}}
				{{/foreach}}
	    </td>
		</tr>
		<tr>
	    <th>Eléments</th>
	    <td>
	      {{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	        {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	          {{foreach from=$_lines_by_cat.element item=_line name=category}}
	            {{assign var=element value=$_line->_ref_element_prescription}}
	            {{if $smarty.foreach.category.first}}
	              {{assign var=category value=$element->_ref_category_prescription}}
					      <div class="activite" id="activite-{{$category->_guid}}" style="display: none;">
	            {{/if}}
							
							 <span style="float: right">
              {{if $_line->debut}}
                à partir du {{mb_value object=$_line field="debut"}}
              {{/if}}
              {{if $_line->date_arret}}
                jusqu'au {{mb_value object=$_line field="date_arret"}}
              {{/if}}
              </span>
							<button id="line-{{$_line->_id}}" type="button" class="search line" onclick="selectElement('{{$_line->_id}}');">
	              {{$_line->_view}}
							</button>
							<br />
							{{if $smarty.foreach.category.last}}
							  </div>
	            {{/if}}
	          {{/foreach}}
	        {{/foreach}}
	      {{/foreach}}
	    </td>
	  </tr>
	  <tr>
	  	<th>Codes Cdarr</th>
			<td>
				{{foreach from=$prescription->_ref_prescription_lines_element_by_cat item=_lines_by_chap}}
	        {{foreach from=$_lines_by_chap item=_lines_by_cat}}
	          {{foreach from=$_lines_by_cat.element item=_line}}
						  <div class="cdarrs" id="cdarrs-{{$_line->_id}}" style="display : none;">
						  {{foreach from=$_line->_ref_element_prescription->_back.cdarrs item=_cdarr}}
							  <label title="{{$_cdarr->commentaire}}">
							  	<input type="radio" name="cdarr" value="{{$_cdarr->code}}" /> {{$_cdarr->code}}
							  </label>
							{{/foreach}}
							</div>
						{{/foreach}}
					{{/foreach}}
				{{/foreach}}	
			</td>
		</tr>	
	  <tr>
	    <th>Technicien</th>
	    <td>
	      {{foreach from=$plateau->_ref_techniciens item=_technicien}}
	      <button id="technicien-{{$_technicien->_ref_kine->_id}}" class="search ressource" type="button" onclick="selectTechnicien('{{$_technicien->_ref_kine->_id}}')">
	        {{$_technicien}}
	      </button>
	      {{/foreach}}
	    </td>
	  </tr>
	  <tr>
	    <th>Equipement</th>
	    <td>
	      {{foreach from=$plateau->_ref_equipements item=_equipement}}
	      <button id="equipement-{{$_equipement->_id}}" class="search equipement" type="button" onclick="selectEquipement('{{$_equipement->_id}}');">
	        {{$_equipement}}
	      </button>
	      {{/foreach}}
				<button type="button" class="cancel notext" onclick="selectEquipement('');"></button>
	    </td>
	  </tr>
    <tr>
    	<th style="vertical-align: middle;">Jour</th>
			<td style="text-align: center;">
			  <table>
			  	<tr>
			      {{foreach from=$list_days key=_date item=_day}}
              <td>
                <label>{{$_day}}<br /><input type="checkbox" name="_days[{{$_date}}]" value="{{$_date}}" />
                </label>
              </td>
            {{/foreach}}
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>Heure</th>
      <td>{{mb_field object=$evenement_ssr field="_heure" form="editEvenementSSR"}}</td>
		</tr>
		<tr>
			<th>Durée (min)</th>
      <td>{{mb_field object=$evenement_ssr field="duree" form="editEvenementSSR" increment=1 size=2 step=10}}</td>  
		</tr>
		<tr>
			<td colspan="2" class="button">
				<button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
			</td>
		</tr>
	</table>
</form>