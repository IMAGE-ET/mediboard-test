{{if $lines|@count}}
	<script type="text/javascript">
		editForm = getForm("editLine");
		addForm = getForm('addLineSSR');
		
	  removeLineSSR = function(line_id){
	    $V(editForm.del, '1');
	    $V(editForm.prescription_line_element_id, line_id);
	    return onSubmitFormAjax(editForm);
	  }	  
	  		  
	  stopLineSSR = function(line_id){
		  $V(editForm.del, '0');
      $V(editForm.prescription_line_element_id, line_id);
			$V(editForm.date_arret, '{{$current_date}}');
      return onSubmitFormAjax(editForm);
		}	
		
		resetField = function(){
		  $V(addForm.debut, '');
      $V(editForm.date_arret, '');
		}
		  
		submitAndClose = function(){
		  return onSubmitFormAjax(addForm, { onComplete: function() { 
				updateListLines(); 
				resetField(); 
				modalWindow.close(); 
			} } );
		}
	</script>
	{{if $warning}}
  La ligne de prescription suivante est déjà présente :
  <br />
  <strong style="padding-left: 2em;">{{$element}}</strong>
  <br />
	<div class="button">
	  <button onclick="stopLineSSR('{{$last_line->_id}}'); $V(addForm.debut, '{{$current_date}}'); submitAndClose();" class="tick">Ajouter à la suite</button>
		<button onclick="modalWindow.close();" class="cancel">{{tr}}Cancel{{/tr}}</button>
	</div>
	{{else}}
	Vous etes sur le point d'ajouter la ligne de prescription :
	<br />
	<strong style="padding-left: 2em;">{{$element}}</strong>
	<br />
  <div class="button">
		<button onclick="removeLineSSR('{{$last_line->_id}}'); submitAndClose();" class="tick">Remplacer la ligne existante</button>
		<button onclick="stopLineSSR('{{$last_line->_id}}'); $V(addForm.debut, '{{$current_date}}'); submitAndClose();" class="tick">Ajouter à la suite</button>
		<button onclick="modalWindow.close();" class="cancel">{{tr}}Cancel{{/tr}}</button>
  </div>
  {{/if}}
{{/if}}