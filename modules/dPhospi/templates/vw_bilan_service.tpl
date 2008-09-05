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

// Fonction permettant de modifier le tokenField lors de la selection des checkboxs
changeBox = function(oCheckbox, cat_id, oTokenField){
  if(oCheckbox.checked){
    oTokenField.add(cat_id);
  } else {
    oTokenField.remove(cat_id);
  }
}
	
</script>

<form name="filter_prescription" action="?" method="get">
  <input type="hidden" name="token_cat" value="{{$token_cat}}" />     
 
 <table class="form">
  <tr>
    <th class="category">Horaire</th>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="m" value="dPhospi" />
      <input type="hidden" name="a" value="vw_bilan_service" />
      <input type="hidden" name="dialog" value="1" />
      De {{mb_field object=$prescription field="_filter_time_min" form="filter_prescription"}}
      à {{mb_field object=$prescription field="_filter_time_max" form="filter_prescription"}}
     </td>
   </tr>
   <tr>
     <th class="category">Categorie</th>
   </tr>
   <tr>
     <td>
       <table>
         <tr>
           <td>
             <strong>Médicaments</strong>
           </td>
           <td>
             <input type="checkbox" value="med" onclick="changeBox(this,'med',oCatField)" />
           </td>
         </tr>
         {{foreach from=$categories item=categories_by_chap key=name name="foreach_cat"}}
           <tr>
             <td><strong>{{tr}}CCategoryPrescription.chapitre.{{$name}}{{/tr}}</strong></td>
             {{foreach from=$categories_by_chap item=categorie}}
               <td class="text">
                 <input type="checkbox" value="{{$categorie->_id}}" onclick="changeBox(this,'{{$categorie->_id}}',oCatField)"/> {{$categorie->_view}}
               </td>
             {{/foreach}}
           </tr>
         {{/foreach}}
       </table>
     </td>
  </tr>
  <tr>
    <td style="text-align: center">
      <button class="tick">Filtrer</button>
    </td>
  </tr>
</table>
</form>
<table class="tbl">
  <tr>
    <th>Libelle</th>
    <th>Prises</th>
  </tr>
	{{foreach from=$prises key=patient_id item=lines_by_patient_class_hour}}
	  {{assign var=patient value=$patients.$patient_id}}
	  <tr>
		 <th colspan="2">{{$patient->_view}}</th>
		</tr>
	  {{foreach from=$lines_by_patient_class_hour key=hour item=lines_by_patient_class}}
		  {{foreach from=$lines_by_patient_class key=_class item=lines_by_patient name=foreach_hour}}	  
			  {{if $smarty.foreach.foreach_hour.first}}
			  <tr>
	        <th colspan="2">{{$hour}}</th>
	      </tr>
	      {{/if}}
			  {{foreach from=$lines_by_patient key=line_id item=prises_by_patient}}
			  {{assign var=produit value=$lines_produit.$_class.$line_id}}
			  <tr>
			    <td>{{$produit->_view}}</td>
				  <td>
				  {{foreach from=$prises_by_patient item=prise name="view_prise"}}
				    {{if $prise->_type == "moment"}}
				      {{$prise->_view}}
				    {{else}}
				      {{$prise->_short_view}}
				    {{/if}}
				    {{if !$smarty.foreach.view_prise.last}}, {{/if}}
				  {{/foreach}}
				  </td>
			  </tr>
			  {{/foreach}}
		  {{/foreach}}
	  {{/foreach}}
	{{foreachelse}}
	<tr>
	  <td colspan="2">Aucune prise</td>
	</tr>
	{{/foreach}}
</table>
