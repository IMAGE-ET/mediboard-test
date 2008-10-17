{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}

<tr>
   {{if $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
	   {{if $line->_class_name == "CPrescriptionLineMedicament"}}
       <th class="text" rowspan="{{$prescription->_nb_produit_by_cat.$name_cat}}">{{$line->_ref_produit->_ref_ATC_2_libelle}}</th>
	   {{else}}
	     <th rowspan="{{$prescription->_nb_produit_by_cat.$name_cat}}">{{$categorie->_view}}</th>
	   {{/if}}
   {{/if}}
   
   </th>
   <td style="width: 1%; white-space: nowrap;">
     <a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$line_class}}', object_id: {{$line->_id}} } })">
       {{$line->_view}}
     </a>
   </td>
   {{foreach from=$dates item=date name="foreach_date"}}
     <td style="{{if $date < $line->debut || $line->_fin_reelle && $date > $line->_fin_reelle|date_format:'%Y-%m-%d'}}background-color: #ddd;{{/if}}width: 20%; text-align: center" class="date"> 		          
     
     
	    {{if $date < $line->debut || $line->_fin_reelle && $date > $line->_fin_reelle|date_format:'%Y-%m-%d'}}
	      - 
	    {{else}}
	      {{assign var=nb_administre value="0"}}
	      {{if is_array($line->_administrations_by_line) && array_key_exists($date, $line->_administrations_by_line)}}
	        {{assign var=nb_administre value=$line->_administrations_by_line.$date}}
	      {{/if}}
	      {{assign var=nb_prevue value="0"}}
	      {{if @$line->_quantity_by_date.$date.total}}
	        {{assign var=nb_prevue value=$line->_quantity_by_date.$date.total}} 
	      {{/if}}
	      <span class="administration
			              {{if $nb_prevue > 0}}
					            {{if $nb_administre}}
						            {{if $nb_administre == $nb_prevue}} administre
						            {{elseif $nb_administre != $nb_prevue}} administration_partielle
						            {{/if}}
					            {{else}}
					              {{if $date < $now}} non_administre
					              {{else}} a_administrer
					              {{/if}}
						          {{/if}}
					          {{/if}}">
	        <!-- Affichage des administrations effectuée et des prises prevues -->
		      {{$nb_administre}} / {{$nb_prevue}}
	    </span>    
    {{/if}}
    <br />
   
     {{if $line->debut == $date || $line->_fin == $date}}
      <form name="modifDates-{{$line_class}}-{{$line_id}}-{{$date}}" action="?" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}"/>
        <input type="hidden" name="dosql" value="{{$dosql}}" />
        <input type="hidden" name="duree" value="{{$line->duree}}" />
     {{/if}}  
     
     <!-- Affichage de la date de debut si date courante -->
     {{if $date == $line->debut}}
       {{mb_field object=$line field="debut" canNull=false form=modifDates-$line_class-$line_id-$date}}
       <button class="tick notext" type="button" 
               onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ 
         		        calculSoinSemaine('{{$now}}','{{$prescription->_id}}'); } });"></button>
       
         <script type="text/javascript">	                
          // Preparation du formulaire
					prepareForm(document.forms['modifDates-{{$line_class}}-{{$line_id}}-{{$date}}']);
					Main.add( function(){
					  Calendar.regField('modifDates-{{$line_class}}-{{$line_id}}-{{$date}}', "debut", false, dates);
					} );
		</script>
     {{/if}}
     
     {{assign var=line_fin value=$line->_fin}}
     <!-- Affichage de la date de fin si date courante -->
     {{if ($date == $line->_fin) && ($line->debut != $line->_fin)}}
       {{mb_field object=$line field="_fin" canNull=false form=modifDates-$line_class-$line_id-$date}}
         <button class="tick notext" type="button" onclick="calculDuree('{{$line_fin}}', this.form._fin.value, this.form, '{{$now}}', '{{$prescription->_id}}');"></button>
         <script type="text/javascript">   
          // Preparation du formulaire
					prepareForm(document.forms['modifDates-{{$line_class}}-{{$line_id}}-{{$date}}']);
					Main.add( function(){
					  Calendar.regField('modifDates-{{$line_class}}-{{$line->_id}}-{{$date}}', "_fin", false, dates);
					} );
		</script>
     {{/if}}
     
    {{if $line->debut == $date || $line->_fin == $date}}
      </form>
    {{/if}}
    </td>
  {{/foreach}}
</tr>