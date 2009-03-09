<script type="text/javascript">

function viewDossierSoin(sejour_id){
  oForm = document.viewSoin;
  oForm.sejour_id.value = sejour_id;
  oForm.submit();
}
       
function viewLegendPancarte(){
  var url = new Url;
  url.setModuleAction("soins", "vw_legende_pancarte");
  url.popup(300, 400, "Légende de la pancarte");
}
       
function showHidePatients(patient_id){
  $('viewTransmissions').select('tr.trans_or_obs').each(function(tr){
    if(!patient_id){
      tr.show();
    } else {
      if(tr.hasClassName(patient_id)){
	      tr.show();
	    } else {
	      tr.hide();
	    }
    }
  });
  
  key_trans = patient_id ? (nb_trans[patient_id] || 0) : nb_trans['total'];
	key_observ = patient_id ? (nb_observ[patient_id] || 0) : nb_observ['total'];
	
	$('nb_trans').update(key_trans);
	$('nb_observ').update(key_observ);  
}

var nb_trans;
var nb_observ;

nb_trans = {{$nb_trans|@json}};
nb_observ = {{$nb_observ|@json}};

Main.add(function () {
  var tab_sejour = Control.Tabs.create('tab-pancarte', false);
  
  
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

<ul id="tab-pancarte" class="control_tabs">
  <li><a href="#viewPancarte">Pancarte {{$service->_view}}</a></li>
  <li><a href="#viewTransmissions">Transmissions (<span id="nb_trans">{{$nb_trans.total}}</span>) et Observations (<span id="nb_observ">{{$nb_observ.total}}</span>)</a></li>
</ul>
<hr class="control_tabs" />

<button type="button" class="search" onclick="viewLegendPancarte();" style="float: right;">Légende</button>
      
{{assign var=images value="CPrescription"|static:"images"}}
<div id="viewPancarte" style="display: none;">
<table class="form">
  <tr>
    <th class="category">
      Pancarte du service
	    <form name="selService" action="?" method="get">
	      <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="tab" value="vw_pancarte_service" />
		    <select name="service_id" onchange="this.form.submit();">
		      <option value="">&mdash; Choix d'un service</option>
		      {{foreach from=$services item=_service}}
		        <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
		      {{/foreach}}
		    </select>
	    </form>
    </th>
  </tr>
</table>
<table class="tbl">
  <tr>
    <th rowspan="2">Patient</th>
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
         {{$_prescription->_ref_patient->_view}}
				 </a>
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
              
          		  {{if @$tab.$_prescription_id.$_date_hour}}
          		    <div style="border: 1px solid #BBB; height: 16px;"
          		         onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-prises-{{$_prescription_id}}-{{$_date_reelle}}-{{$_heure_reelle}}");'>
        		       {{foreach from=$tab.$_prescription_id.$_date_hour key="chapitre" item=quantites}}
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
		          		    {{foreach from=$tab.$_prescription_id.$_date_hour key="chapitre" item=quantites}}
		          		      <tr>
		          		        <th colspan="5">
		          		          <img src="{{$images.$chapitre}}" /><strong> {{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}}</strong>
		          		        </th>
		          		      </tr>
		          		      
		          		      {{if $chapitre == "perf"}}
		          		        {{assign var=perfusions value=$quantites}}
		          		        {{foreach from=$perfusions item=_perfusion}}
		          		      	<tr>
		          		      	  <td>
		          		      	   {{if $_perfusion->_debut|date_format:'%Y-%m-%d %H:00:00' == $_date_hour}}
				          		      	 {{if $_perfusion->_debut|date_format:'%Y-%m-%d %H:00:00' == $_perfusion->_debut_adm|date_format:'%Y-%m-%d %H:00:00'}}
			          		      	     <img src="images/icons/tick.png" alt="" title="" />
			          		      	   {{/if}}
			          		      	 {{else}}
			          		      	  {{if $_perfusion->_fin|date_format:'%Y-%m-%d %H:00:00' == $_perfusion->_fin_adm|date_format:'%Y-%m-%d %H:00:00'}}
			          		      	    <img src="images/icons/tick.png" alt="" title="" />
			          		      	  {{/if}}
			          		      	 {{/if}} 
		          		      	  </td>
		          		      	  <td colspan="4">
			          		      	  {{if $_perfusion->_debut|date_format:'%Y-%m-%d %H:00:00' == $_date_hour}}
			          		      	  Début 
			          		      	  {{else}}
			          		      	  Fin 
			          		      	  {{/if}} 
			          		      	  {{$_perfusion->_view}} {{if $_perfusion->duree}}(Durée: {{$_perfusion->duree}} h){{/if}}
			          		      	  <br />
			          		      	  Produits
			          		      	  <ul>
			          		      	  {{foreach from=$_perfusion->_ref_lines item=_perf_line}}
			          		      	    <li>{{$_perf_line->_view}}</li>
			          		      	  {{/foreach}}
			          		      	  </ul>
			          		      	  {{if $_perfusion->_debut_adm || $_perfusion->_fin_adm}}
			          		      	    Dates réelles:
			          		      	  {{/if}}
			          		      	  <ul>
			          		      	  {{if $_perfusion->_debut_adm}}
			          		      	    <li>Début: {{mb_value object=$_perfusion field=_debut_adm}}</li>
			          		      	  {{/if}}
			          		      	  {{if $_perfusion->_fin_adm}}
			          		      	    <li>Fin: {{mb_value object=$_perfusion field=_fin_adm}}</li>
			          		      	  {{/if}}
			          		      	  </ul>
		          		      	  </td>
		          		      	</tr>  
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
			          		            {{if $quantite_prevue == $quantite_adm}}
			          		              <img src="images/icons/tick.png" alt="" title="" />
			          		            {{/if}}
			          		          </td>
			          		          <td>
			          		            {{assign var=line value=$lines.$chapitre.$line_id}}
			          		             {{if $line->_class_name == "CPrescriptionLineMedicament"}}
			          		               {{$line->_ucd_view}}
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
	{{/foreach}}
</table>
</div>

<div id="viewTransmissions" style="display: none;">
	<table class="tbl">
	  <tr>
	    <th colspan="6" class="title">
	    	<select name="selPatient" onchange="showHidePatients(this.value);" style="float: right;">
		      <option value="">&mdash; Tous les patients ({{$nb_trans.total}} - {{$nb_observ.total}})</option>
		      {{foreach from=$patients item=_patient}}
		        {{assign var=patient_id value=$_patient->_id}}
		        <option value="{{$_patient->_id}}">{{$_patient->_view}} ({{$nb_trans.$patient_id}} - {{$nb_observ.$patient_id}})</option>
		      {{/foreach}}
		    </select>
		    Transmissions à partir du {{$date_min|date_format:$dPconfig.datetime}}
	    </th>
	  </tr>
	</table>
  {{include file="../../dPprescription/templates/inc_vw_transmissions.tpl"}}
</div>