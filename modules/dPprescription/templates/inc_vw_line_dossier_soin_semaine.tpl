{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}

<tr>
   <td style="width: 1%; white-space: nowrap;">
     {{$line->_view}}
   </td>
   {{foreach from=$dates item=date name="foreach_date"}}
     <td style="{{if $date >= $line->debut && $date <= $line->_fin}}background-color: #ddd;{{/if}}width: 20%; text-align: center" class="date"> 		          
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
         		        calculSoinSemaine('{{$now}}','{{$prescription_id}}'); } });"></button>
       
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
         <button class="tick notext" type="button" onclick="calculDuree('{{$line_fin}}', this.form._fin.value, this.form, '{{$now}}', '{{$prescription_id}}');"></button>
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
    
    <!-- Affichage des administrations effectuée par rapport aux administrations prévues -->
    {{if is_array($line->_administrations_by_line) && array_key_exists($date, $line->_administrations_by_line)}}
      {{$line->_administrations_by_line.$date}}
    {{else}}
      0
    {{/if}}
    /
    {{if array_key_exists($date, $prescription->_list_prises.$type) && array_key_exists($line_id, $prescription->_list_prises.$type.$date)}}
     {{$prescription->_list_prises.$type.$date.$line_id.total}}
    {{else}}
      0
    {{/if}}    
    </td>
  {{/foreach}}
</tr>