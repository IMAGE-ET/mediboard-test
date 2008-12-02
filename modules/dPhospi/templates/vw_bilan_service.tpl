<script type="text/javascript">
Main.add( function(){
  oCatField = new TokenField(document.filter_prescription.token_cat); 
  
  var cats = {{$cats|@json}};
  $$('input').each( function(oCheckbox) {
    if(cats.include(oCheckbox.value)){
      oCheckbox.checked = true;
    }
  });
} );

selectChap = function(name_chap, oField){
  $$('input.'+name_chap).each(function(oCheckbox) { 
    if(!oCheckbox.checked){
      oCheckbox.checked = true;
      oField.add(oCheckbox.value);
    }
  });
}
</script>

<div class="not-printable">
<form name="filter_prescription" action="?" method="get">
  <input type="hidden" name="token_cat" value="{{$token_cat}}" />     
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="a" value="vw_bilan_service" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="do" value="1" />
 <table class="form">
  <tr>
    <th class="category" colspan="4">S�lection des horaires</th>
  </tr>
  <tr>
    <td>A partir du</td>
    <td class="date">
      {{mb_field object=$prescription field="_dateTime_min" canNull="false" form="filter_prescription" register="true"}}
    </td>
    <td>Jusqu'au</td>
    <td class="date">
      {{mb_field object=$prescription field="_dateTime_max" canNull="false" form="filter_prescription" register="true"}}
     </td>
   </tr>
   <tr>
     <th class="category" colspan="4">S�lection des cat�gories</th>
   </tr>
   <tr>
     <td colspan="4">
       <table>
         <tr>
           <td>
             <strong>M�dicaments</strong>
           </td>
           <td>
             <input type="checkbox" value="med" onclick="oCatField.toggle(this.value, this.checked);" />
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
        <button class="print" type="button" onclick="window.print()">Imprimer les r�sultats</button>
      {{/if}}
    </td>
  </tr>
</table>
</form>
</div>
<table class="tbl">
{{if $lines_by_patient|@count}}
<tr>
  <th colspan="2">Filtres</th>
</tr>
<tr>
  <td>Horaires s�lectionn�es</td><td><strong>{{$dateTime_min|date_format:$dPconfig.datetime}}</strong> au <strong>{{$dateTime_max|date_format:$dPconfig.datetime}}</strong></td>
</tr>
<tr>
  <td>Cat�gorie(s) s�lectionn�e(s)</td>
  <td class="text">
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
      <th colspan="2">
        <span style="float: left">
          {{assign var=chambre value=$chambres.$chambre_id}}
          <strong>Chambre {{$chambre->_view}}</strong>
        </span>
		    <span style="float: right">
		      DE: {{$sejour->_entree|date_format:"%d/%m/%Y"}}<br />
		      DS:  {{$sejour->_sortie|date_format:"%d/%m/%Y"}}
		    </span>
		    <strong>{{$patient->_view}}</strong>
		    N�(e) le {{mb_value object=$patient field=naissance}} - ({{$patient->_age}} ans) - ({{$patient->_ref_constantes_medicales->poids}} kg)
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
			      <th style="width: 250px; border:none;">Libell�</th> 
					  <th style="width: 50px; border:none;">Pr�vues</th>
					  <th style="width: 50px; border:none;">Effectu�es</th>
					  <th style="width: 150px; border:none;">Unit� d'admininstration</th>
					  <th style="border:none;">Commentaire</th>
					</tr>
	      </table>
      </td>
	  </tr>
	  
	  <!-- Affichage specifique aux perfusions -->
    {{if array_key_exists('perf', $prises_by_hour)}}
	    {{assign var=perfusion value=$prises_by_hour.perf}}
	    <tr>
	      <td>Perfusion</td>
	      <td>
	        {{$perfusion->_view}} 
	        {{if $perfusion->time_debut}}
	        � {{mb_value object=$perfusion field="time_debut"}}
	        {{/if}}
	        {{if $perfusion->duree}}
             pendant {{$perfusion->duree}} heures.
          {{/if}}
	        <ul>
	          {{foreach from=$perfusion->_ref_lines item=perf_line}}
              <li>{{$perf_line->_view}}</li> 
 	          {{/foreach}}
	        </ul>
	      </td>
	    </tr>
	  {{/if}}
	  
	  {{foreach from=$prises_by_hour key=hour item=prises_by_type  name="foreach_hour"}}
	    {{if $hour != "perf"}}
		    {{assign var=_date_time value="$date $hour:00:00"}}
		  	{{if $_date_time >= $dateTime_min && $_date_time <= $dateTime_max}}
				  <tr>
				    <td style="width: 20px">{{$hour}}h</td>
			      <td style="width: 100%">
			        <table class="form">         
					      {{foreach from=$prises_by_type key=line_class item=prises name="foreach_unite"}}
						      {{foreach from=$prises key=line_id item=quantite}}
					          {{assign var=line value=$list_lines.$line_class.$line_id}}        
							      <tr>
					            <td style="width: 250px; border:none;">{{$line->_view}}
					            {{if array_key_exists('prevu', $quantite) && array_key_exists('administre', $quantite) && $quantite.prevu == $quantite.administre}}
					              <img src="images/icons/tick.png" alt="Administrations effectu�es" title="Administrations effectu�es" />
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
					      {{/foreach}}  
			        </table>
			      </td>
				  </tr>
			  {{/if}}
		  {{/if}}
	  {{/foreach}}
	{{/foreach}}
  {{/foreach}}
{{/foreach}}
</table>