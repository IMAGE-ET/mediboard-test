<script type="text/javascript">
  
  var date_init = new Date().toDATE();
	dates = {  
	  limit: {
	    start: date_init,
	    stop: null
	  }
	}
	
	calculDuree = function(date1, date2, oForm, now, prescription_id){
	  var dDate1 = Date.fromDATE(date1); 
	  var dDate2 = Date.fromDATE(date2); 
	  var date = dDate2 - dDate1;
	  nb_days = date / (24 * 60 * 60 * 1000);
	  oForm.duree.value = parseInt(oForm.duree.value,10) + nb_days;
	  submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
	    calculSoinSemaine(now,prescription_id); 
	  } });              		        
	}
	
</script>

<table class="tbl">
  <tr>
    <th>Libelle</th>
    {{foreach from=$dates item=date}}
    <th>
      {{$date|date_format:"%d/%m/%Y"}}
    </th>
    {{/foreach}}
  </tr>
  
  <!-- Affichage des medicaments -->
  {{foreach from=$prescription->_lines.med item=lines_unite_prise name="foreach_line"}}
    {{assign var=prescription_id value=$prescription->_id}}
    {{foreach from=$lines_unite_prise key=unite_prise item=line_med name="foreach_med"}}
      <!-- Si l'unite de prise est bien exprimé en format texte et pas en identifiant de prise -->
       {{if $smarty.foreach.foreach_med.first}}
        {{include file="inc_vw_line_dossier_soin_semaine.tpl" 
                  line=$line_med 
                  dosql=do_prescription_line_medicament_aed 
                  type=med
                  nodebug=true}}    
	    {{/if}}
    {{/foreach}}
  {{/foreach}}
  
	<!-- Affichage des elements -->
	{{foreach from=$prescription->_lines.elt key=name_chap item=elements_chap}}
	 {{foreach from=$elements_chap key=name_cat item=elements_cat}}
	   {{assign var=categorie value=$categories.$name_chap.$name_cat}}
	   {{foreach from=$elements_cat item=_element name="foreach_cat"}}
	     {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 
	        {{if $smarty.foreach.foreach_elt.first}}
	         {{include file="inc_vw_line_dossier_soin_semaine.tpl" 
	                   line=$element 
	                   dosql=do_prescription_line_element_aed 
	                   type=elt
	                   nodebug=true}} 
	        {{/if}}
	      {{/foreach}}
	    {{/foreach}}
	  {{/foreach}}
	{{/foreach}}  
</table>