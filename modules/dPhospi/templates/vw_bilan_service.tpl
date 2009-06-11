{{if !$hide_filters}}
<script type="text/javascript">
Main.add( function(){
  oCatField = new TokenField(document.filter_prescription.token_cat); 
  
  var cats = {{$cats|@json}};
  $$('input').each( function(oCheckbox) {
    if(cats.include(oCheckbox.value)){
      oCheckbox.checked = true;
    }
  });
  
  getForm("filter_prescription")._dateTime_min.observe("ui:change", resetPeriodes);
  getForm("filter_prescription")._dateTime_max.observe("ui:change", resetPeriodes);
} );

function resetPeriodes() {
  getForm("filter_prescription").select('input[name=periode]').each(function(e) {
    e.checked = false;
  });
}

selectChap = function(name_chap, oField){
  $$('input.'+name_chap).each(function(oCheckbox) { 
    if(!oCheckbox.checked){
      oCheckbox.checked = true;
      oField.add(oCheckbox.value);
    }
  });
}

var periodes = {{$dPconfig.dPprescription.CPrisePosologie.heures|@json}};
selectPeriode = function(element) {
  var form = getForm("filter_prescription");
  var start = form.elements._dateTime_min;
  var end = form.elements._dateTime_max;
  
  var startDate = Date.fromDATETIME($V(start));
  var endDate = Date.fromDATETIME($V(start));
  
  if (element.value == 'matin' || element.value == 'soir' || element.value == 'nuit') {
    startDate.setHours(periodes[element.value].min);

    var dayOffset = 0;
    if (periodes[element.value].max < periodes[element.value].min) {
      dayOffset = 1;
    }
    endDate.setDate(startDate.getDate()+dayOffset);
    endDate.setHours(periodes[element.value].max);
  }
  else {
    startDate.setHours(0);
    startDate.setMinutes(0);
    startDate.setSeconds(0);
    endDate.setTime(startDate.getTime()+24*60*60*1000-1000);
  }
  
  form._dateTime_min_da.value = startDate.toLocaleDateTime();
  form._dateTime_max_da.value = endDate.toLocaleDateTime();
  
  startDate = startDate.toDATETIME(true);
  endDate = endDate.toDATETIME(true);
  
  $V(start, startDate, false);
  $V(end, endDate, false);
}

</script>

<form name="filter_prescription" action="?" method="get" class="not-printable">
  <input type="hidden" name="token_cat" value="{{$token_cat}}" />     
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="a" value="vw_bilan_service" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="do" value="1" />
  <table class="form">
    <tr>
      <th class="category" colspan="4">Sélection des horaires</th>
    </tr>
    <tr>
      <th>A partir du</th>
      <td class="date">
        {{mb_field object=$prescription field="_dateTime_min" canNull="false" form="filter_prescription" register="true"}}
        <label><input type="radio" name="periode" value="matin" onclick="selectPeriode(this)" {{if $periode=='matin'}}checked="checked"{{/if}} /> Matin</label>
        <label><input type="radio" name="periode" value="soir" onclick="selectPeriode(this)" {{if $periode=='soir'}}checked="checked"{{/if}}/> Soir</label>
        <label><input type="radio" name="periode" value="nuit" onclick="selectPeriode(this)" {{if $periode=='nuit'}}checked="checked"{{/if}}/> Nuit</label>
        <label><input type="radio" name="periode" value="today" onclick="selectPeriode(this)" {{if $periode=='today'}}checked="checked"{{/if}}/> Aujourd'hui</label>
      </td>
      <th>Jusqu'au</th>
      <td class="date">
        {{mb_field object=$prescription field="_dateTime_max" canNull="false" form="filter_prescription" register="true"}}
       </td>
     </tr>
     <tr>
       <th class="category" colspan="4">Sélection des catégories</th>
     </tr>
     <tr>
       <td colspan="4">
         <table>
           <tr>
             <td>
               <strong>Médicaments</strong>
             </td>
             <td>
               <input type="checkbox" value="med" onclick="oCatField.toggle(this.value, this.checked);" />
             </td>
           </tr>
           <tr>
             <td>
               <strong>Injections</strong>
             </td>
             <td>
               <input type="checkbox" value="inj" onclick="oCatField.toggle(this.value, this.checked);" />
             </td>
           </tr>
           <tr>
             <td>
               <strong>Perfusions</strong>
             </td>
             <td>
               <input type="checkbox" value="perf" onclick="oCatField.toggle(this.value, this.checked);" />
             </td>
           </tr>
           {{foreach from=$categories item=categories_by_chap key=name name="foreach_cat"}}
             {{if $categories_by_chap|@count}}
  	           <tr>
  	             <td>
  	               <button type="button" onclick="selectChap('{{$name}}', oCatField);" class="tick">Tous</button>
  	               <strong>{{tr}}CCategoryPrescription.chapitre.{{$name}}{{/tr}}</strong>  
  	             </td>
  	             {{foreach from=$categories_by_chap item=categorie}}
  	               <td style="white-space: nowrap; float: left; width: 10em;">
  	                 <label title="{{$categorie->_view}}">
  	                 <input class="{{$name}}" type="checkbox" value="{{$categorie->_id}}" onclick="oCatField.toggle(this.value, this.checked);"/> {{$categorie->_view}}
  	                 </label>
  	               </td>
  	             {{/foreach}}
  	           </tr>
             {{/if}}
           {{/foreach}}
         </table>
       </td>
    </tr>
    <tr>
      <td style="text-align: center" colspan="4">
        <button class="tick">Filtrer</button>
        {{if $lines_by_patient|@count}}
          <button class="print" type="button" onclick="window.print()">Imprimer les résultats</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
{{/if}}

<table class="tbl">
{{if $lines_by_patient|@count}}
<tr>
  <th colspan="6">Filtres</th>
</tr>
<tr>
  <td colspan="6">
    Horaires sélectionnées: 
    <strong>{{$dateTime_min|date_format:$dPconfig.datetime}}</strong> au <strong>{{$dateTime_max|date_format:$dPconfig.datetime}}</strong>
  </td>
</tr>
<tr>
  <td colspan="6" class="text">
    Catégorie(s) sélectionnée(s):
    {{foreach from=$cat_used item=_cat_view name=cat}}
      <strong>{{$_cat_view}}{{if !$smarty.foreach.cat.last}},{{/if}}</strong>
    {{/foreach}}
  </td>
</tr>
{{/if}}

{{foreach from=$lines_by_patient key=chambre_id item=_lines_by_patient}}
  {{foreach from=$_lines_by_patient key=sejour_id item=prises_by_dates}}
    {{assign var=sejour value=$sejours.$sejour_id}}
    {{assign var=patient value=$sejour->_ref_patient}}
    <tr>
      <th colspan="6">
        <span style="float: left">
          {{assign var=chambre value=$chambres.$chambre_id}}
          <strong>Chambre {{$chambre->_view}}</strong>
        </span>
		    <span style="float: right">
		      DE: {{$sejour->_entree|date_format:"%d/%m/%Y"}}<br />
		      DS:  {{$sejour->_sortie|date_format:"%d/%m/%Y"}}
		    </span>
		    <strong>{{$patient->_view}}</strong>
		    Né(e) le {{mb_value object=$patient field=naissance}} - ({{$patient->_age}} ans) - ({{$patient->_ref_constantes_medicales->poids}} kg)
		    <br />
		    {{assign var=operation value=$sejour->_ref_last_operation}}
		    Intervention: {{$operation->libelle}} le {{$operation->_ref_plageop->date|date_format:"%d/%m/%Y"}}
		    <strong>(I{{if $operation->_compteur_jour >=0}}+{{/if}}{{$operation->_compteur_jour}})</strong>
      </th>
    </tr>
  	{{foreach from=$prises_by_dates key=date item=prises_by_hour name="foreach_date"}}
	  <tr>
	    <td style="width: 20px; border:none;"><strong>{{$date|date_format:"%d/%m/%Y"}}</strong></td>
	    <td colspan="5">
		    <table class="form">
		      <tr>
			      <th style="width: 250px; border:none;">Libellé</th> 
					  <th style="width: 50px; border:none;">Prévues</th>
					  <th style="width: 50px; border:none;">Effectuées</th>
					  <th style="width: 150px; border:none;">Unité d'admininstration</th>
					  <th style="border:none;">Commentaire</th>
					</tr>
	      </table>
      </td>
	  </tr>
	  {{foreach from=$prises_by_hour key=hour item=prises_by_type  name="foreach_hour"}}
		    {{assign var=_date_time value="$date $hour:00:00"}}
		  	{{if $_date_time >= $dateTime_min && $_date_time <= $dateTime_max}}
				  <tr>
				    <td style="width: 20px">{{$hour}}h</td>
			      <td style="width: 100%">
			        <table class="form">         
					      {{foreach from=$prises_by_type key=line_class item=prises name="foreach_unite"}}
						      {{if $line_class == "CPerfusion"}}
						        {{foreach from=$prises key=perfusion_id item=lines}}
						        {{assign var=perfusion value=$list_lines.$line_class.$perfusion_id}}     
						        <tr>
						          <th colspan="5">
						            {{$perfusion->_view}}
						          </th>
						          {{foreach from=$lines key=perf_line_id item=_perf}}
						            {{assign var=perf_line value=$list_lines.CPerfusionLine.$perf_line_id}}
							          <tr>
							            <td style="width: 250px; border:none;">
							              {{$perf_line->_view}}
							              {{if array_key_exists('prevu', $_perf) && array_key_exists('administre', $_perf) && $_perf.prevu == $_perf.administre}}
								              <img src="images/icons/tick.png" alt="Administrations effectuées" title="Administrations effectuées" />
								            {{/if}}
							            </td>
								          <td style="width: 50px; border:none; text-align: center;">
									          {{if array_key_exists('prevu', $_perf)}}
									            {{$_perf.prevu}}
									          {{/if}}
								          </td>
								          <td style="width: 50px; border:none; text-align: center;">
									          {{if array_key_exists('administre', $_perf)}}
									          {{$_perf.administre}}
									          {{/if}}
							            </td>
							            <td style="width: 150px; border:none; text-align: center;">ml</td>
							            <td />
							          </tr>
						          {{/foreach}}
						        </tr>
						        {{/foreach}}
						      {{else}}
							      {{foreach from=$prises key=line_id item=quantite}}
							       {{assign var=line value=$list_lines.$line_class.$line_id}}        
								      <tr>
						            <td style="width: 250px; border:none;">{{$line->_view}}
						            {{if array_key_exists('prevu', $quantite) && array_key_exists('administre', $quantite) && $quantite.prevu == $quantite.administre}}
						              <img src="images/icons/tick.png" alt="Administrations effectuées" title="Administrations effectuées" />
						            {{/if}}
						            </td> 
								        <td style="width: 50px; border:none; text-align: center;">{{if array_key_exists('prevu', $quantite)}}{{$quantite.prevu}}{{else}} -{{/if}}</td>
								        <td style="width: 50px; border:none; text-align: center;">{{if array_key_exists('administre', $quantite)}}{{$quantite.administre}}{{else}}-{{/if}}</td>
								        <td style="width: 150px; border:none;" class="text">
								          {{if $line_class=="CPrescriptionLineMedicament"}}
								            {{$line->_ref_produit->libelle_unite_presentation}}
								          {{else}}
								            {{$line->_unite_prise}}
								          {{/if}}
								        </td>
								        <td class="text" style="border:none;">{{$line->commentaire}}</td>
								      </tr>
								    {{/foreach}} 
							    {{/if}}
					      {{/foreach}}  
			        </table>
			      </td>
				  </tr>
			  {{/if}}
	  {{/foreach}}
	{{/foreach}}
  {{/foreach}}
{{/foreach}}
</table>