{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function viewDossierSoin(sejour_id){
  var oForm = document.viewSoin;
  oForm.sejour_id.value = sejour_id;
  oForm.submit();
}
       
function viewLegendPancarte(){
  var url = new Url("soins", "vw_legende_pancarte");
  url.popup(300, 400, "Légende de la pancarte");
}
       
function viewTransmissions(service_id, user_id, degre, observations, transmissions, refresh, order_col, order_way){
  var url = new Url("soins", "httpreq_vw_transmissions_pancarte");
  url.addParam("service_id", service_id);
  url.addParam("user_id", user_id);
  url.addParam("degre", degre);
  url.addParam("date", "{{$date}}");
  url.addParam("date_min", "{{$date_min}}");
  url.addParam("observations", observations?1:0);
  url.addParam("transmissions", transmissions?1:0);
  url.addParam("refresh", refresh);
  if(order_col && order_way){
	  url.addParam("order_col", order_col);
	  url.addParam("order_way", order_way);
  }
  if(user_id || degre || refresh){
    url.requestUpdate("_transmissions");
  } else {
    url.requestUpdate("viewTransmissions");
  }
}       

Main.add(function () {
  var tab_sejour = Control.Tabs.create('tab-pancarte', false);
  viewTransmissions($V(document.selService.service_id), null, null, '1', '1');
});
                 
</script>

<form name="viewSoin" method="get" action="?">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="tab" value="vw_idx_sejour" />
  <input type="hidden" name="sejour_id" value="" />
  <input type="hidden" name="date" value="{{$date}}" />
  <input type="hidden" name="mode" value="1" />
  <input type="hidden" name="_active_tab" value="dossier_soins" /> 
</form>

<button type="button" class="search" onclick="viewLegendPancarte();" style="float: right;">Légende</button>
<div style="text-align: center">
	<form name="selService" action="?" method="get">
		<input type="hidden" name="m" value="{{$m}}" />
		<input type="hidden" name="tab" value="vw_pancarte_service" />
		<select name="service_id" onchange="this.form.submit();">
		  <option value="">&mdash; Choix d'un service</option>
		  {{foreach from=$services item=_service}}
		    <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
		  {{/foreach}}
		</select>
		le
		{{mb_field object=$filter_line field="debut" register=true form=selService onchange="this.form.submit();"}}
  </form>
</div>
			
<ul id="tab-pancarte" class="control_tabs">
  <li><a href="#viewPancarte">Pancarte {{$service->_view}}</a></li>
  <li><a href="#viewTransmissions">Transmissions</a></li>
</ul>
<hr class="control_tabs" />

{{assign var=images value="CPrescription"|static:"images"}}
<div id="viewPancarte" style="display: none;">
<table class="form">
  <tr>
    <th class="category">
      Pancarte du service {{$service->_view}}
	    
    </th>
  </tr>
</table>
<table class="tbl">
  <tr>
    <th rowspan="2" style="width: 1%;">Patient</th>
    <th rowspan="2" style="width: 1%;">Lit</th>
    <th rowspan="2" style="width: 1%;">Praticien</th>
      {{foreach from=$tabHours key=_date item=_hours_by_moment}}
        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
          <th class="{{$_date}}-{{$moment_journee}}"
              colspan="{{if $moment_journee == 'soir'}}{{$count_soir}}{{/if}}{{if $moment_journee == 'nuit'}}{{$count_nuit}}{{/if}}{{if $moment_journee == 'matin'}}{{$count_matin}}{{/if}}">
	            <strong>{{$moment_journee}} du {{$_date|date_format:"%d/%m"}}</strong>
		    {{/foreach}} 
	    {{/foreach}}
  </tr>
	<tr>
		{{foreach from=$tabHours key=_date item=_hours_by_moment}}
      {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
         {{foreach from=$_dates key=_date_reelle item=_hours}}
           {{foreach from=$_hours key=_heure_reelle item=_hour}}
             <th>{{$_hour}}h</th>   
	        {{/foreach}}
	      {{/foreach}}
	    {{/foreach}} 
    {{/foreach}}	
	</tr>
	{{foreach from=$prescriptions item=_prescription}}
	  {{assign var=_prescription_id value=$_prescription->_id}}
	  <tr>
	    <td style="width: 10%;" class="text">
       	<span style="float: left; padding-right: 5px;">
        {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$_prescription->_ref_patient size=20 nodebug=true}} 
         </span>
         <a href="#1" onclick="viewDossierSoin('{{$_prescription->_ref_object->_id}}')">
           {{assign var=sejour value=$_prescription->_ref_object}}
           <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}">
             {{$_prescription->_ref_patient->_view}}
           </span>
				 </a>
	    </td>
	    <td>
	      {{$_prescription->_ref_object->_ref_last_affectation->_ref_lit->_view}}
	    </td>
	    <td>
	      <div class="mediuser" style="border-color: #{{$_prescription->_ref_praticien->_ref_function->color}};">
	      <label title="{{$_prescription->_ref_praticien->_view}}">
	      {{$_prescription->_ref_praticien->_shortview}}
	      </label>
	      </div>
	    </td>
	    {{foreach from=$tabHours key=_date item=_hours_by_moment}}
        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
          {{foreach from=$_dates key=_date_reelle item=_hours}}
            {{foreach from=$_hours key=_heure_reelle item=_hour}}
              {{assign var=_date_hour value="$_date_reelle $_heure_reelle"}}						    				
              <td style="text-align: center; width: 1%;">
          		  {{if @$pancarte.$_prescription_id.$_date_hour}}
          		    <div style="border: 1px solid #BBB; height: 16px;"
          		         onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-prises-{{$_prescription_id}}-{{$_date_reelle}}-{{$_heure_reelle}}");'>
        		       
        		       {{if @$new.$_prescription_id.$_date_hour}}
         		         <img src="images/icons/ampoule.png" />
         		       {{/if}}
         		       {{if @$urgences.$_prescription_id.$_date_hour}}
         		         <img src="images/icons/ampoule_urgence.png" />
         		       {{/if}}
         		      
          		     {{foreach from=$pancarte.$_prescription_id.$_date_hour key="chapitre" item=quantites}}
          		       <img src="{{$images.$chapitre}}" 
          		       {{if @$alertes.$_prescription_id.$_date_hour.$chapitre == '1'}}
          		         style="background-color: #FB4; height: 100%;"
          		       {{else}}
          		         style="background-color: #B2FF9B; height: 100%;"
          		       {{/if}}/>
          		      {{/foreach}}
          		    </div>
          		    
          		    <div id="tooltip-content-prises-{{$_prescription_id}}-{{$_date_reelle}}-{{$_heure_reelle}}" style="display:none;">
	          		    <table class="tbl">
	          		      <tr>
	          		        <th colspan="5" class="title">
	          		          {{$_prescription->_ref_patient->_view}} - {{$_date_hour|date_format:$dPconfig.datetime}}
	          		        </th>
	          		      </tr>
          		        <tr>
          		          <th colspan="2">Libelle</th>
          		          <th>Prévue</th>
          		          <th>Administrée</th>
          		          <th>Unité</th>
          		        </tr>
		          		    {{foreach from=$pancarte.$_prescription_id.$_date_hour key="chapitre" item=quantites}}
		          		      <tr>
		          		        <th colspan="5">
		          		          <img src="{{$images.$chapitre}}" /><strong> {{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}}</strong>
		          		        </th>
		          		      </tr>
		          		      {{if $chapitre == "perf"}}
		          		        {{foreach from=$quantites key=perfusion_id item=quantites_by_perf}}
		          		          <tr>
		          		            <th colspan="5">
		          		              {{assign var=perfusion value=$list_lines.perf.$perfusion_id}}
		          		              {{$perfusion->_view}}
		          		            </th>
		          		          </tr>
			          		        {{foreach from=$quantites_by_perf key=perf_line_id item=_quantites}}
			          		            {{assign var=quantite_prevue value=0}}
			          		            {{assign var=quantite_adm value=0}}
		         		            		{{if array_key_exists('prevue', $_quantites)}}
			          		              {{assign var=quantite_prevue value=$_quantites.prevue}}
			          		            {{/if}}
			          		            {{if array_key_exists('adm', $_quantites)}}
			          		              {{assign var=quantite_adm value=$_quantites.adm}}
			          		            {{/if}}  
			          		            <tr> 
			          		              <td colspan="2">
			          		                {{assign var=perf_line value=$list_lines.perf_line.$perf_line_id}}
			          		                {{$perf_line->_ucd_view}} ({{$perf_line->_posologie}})   
			          		                {{if $quantite_prevue == $quantite_adm}}<img src="images/icons/tick.png" alt="" title="" />{{/if}}
			          		              </td>
			                            <td>{{$quantite_prevue}}</td>
			          		              <td>{{$quantite_adm}}</td>
			       											<td>{{$perf_line->_unite_administration}}</td>
			       										</tr>
			          		        {{/foreach}}
		          		        {{/foreach}}
		          		      {{else}}
			          		      {{foreach from=$quantites key=line_id item=_quantite}}
			          		      {{assign var=quantite_prevue value=0}}
			          		      {{assign var=quantite_adm value=0}}
			          		      <tr>
			          		        {{if array_key_exists('prevue', $_quantite)}}
			          		      	  {{assign var=quantite_prevue value=$_quantite.prevue}}
			          		      	{{/if}}
			          		      	{{if array_key_exists('adm', $_quantite)}}
			          		      	  {{assign var=quantite_adm value=$_quantite.adm}}
			          		      	{{/if}} 	
			          		        {{if $quantite_prevue || $quantite_adm}}
			          		          <td>
			          		            {{if $quantite_prevue == $quantite_adm}}<img src="images/icons/tick.png" alt="" title="" />{{/if}}
			          		            {{if array_key_exists('new', $_quantite)}}<img src="images/icons/ampoule.png" alt="" title="" />{{/if}}
			          		            {{if array_key_exists('urgence', $_quantite)}}<img src="images/icons/ampoule_urgence.png" alt="" title="" />{{/if}}
			          		          </td>
			          		          <td>
			          		             {{assign var=line value=$list_lines.$chapitre.$line_id}}
			          		             {{if $line->_class_name == "CPrescriptionLineMedicament"}}
			          		               {{$line->_ucd_view}}<br />
			          		               <span style="opacity: 0.5; font-size:0.8em;">
			          		                 {{$line->_forme_galenique}}
			          		               </span>
			          		             {{/if}}
			          		             {{if $line->_class_name == "CPrescriptionLineElement"}}
				          		             {{$line->_view}}
				          		          {{/if}}
			          		          </td>
				          		        <td style="text-align: center;">{{$quantite_prevue}}</td>
				          		        <td style="text-align: center;">{{$quantite_adm}}</td>
				          		        <td>
				          		          {{if $line->_class_name == "CPrescriptionLineMedicament"}}
			          		              {{$line->_unite_administration}}
			          		             {{/if}}
			          		             {{if $line->_class_name == "CPrescriptionLineElement"}}
				          		           {{$line->_unite_prise}}
				          		          {{/if}}
				          		        </td>
				          		      {{/if}}
				          		      </tr>
				          		    {{/foreach}}
			          		     {{/if}}
		          		    {{/foreach}}
		          		  </table>
          		    </div>  
          		  {{/if}}
		          </td>
		        {{/foreach}}
		      {{/foreach}}
		    {{/foreach}} 
	    {{/foreach}}	
	  </tr>
	{{foreachelse}}
	  <tr>
	  	<td colspan="30">
	  		Aucune prise
	  	</td>
	  </tr>
	{{/foreach}}
</table>
</div>

<div id="viewTransmissions" style="display: none;">
</div>