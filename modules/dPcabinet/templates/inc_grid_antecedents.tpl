<script type="text/javascript">
function addAntecedent(rques, type, appareil, input) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editAntFrm'];
    if (oForm) {
      oForm.rques.value = rques;
      oForm.type.value = type;
      oForm.appareil.value = appareil;
      window.opener.onSubmitAnt(oForm);
      
      //input.checked = 'checked';
      input.disabled = 'disabled';
      
      $(input).up('td').setStyle({cursor: 'default', opacity: 0.3});
    }
  }
  return false;
}

var oFormAntFrmGrid;
 
Main.add(function () {
  Control.Tabs.create('tab-antecedents', false);
	
	var oFormAnt = window.opener.document.editAntFrm;
	oFormAntFrmGrid = document.editAntFrmGrid;
	$V(oFormAntFrmGrid._patient_id,  oFormAnt._patient_id.value);
	if(oFormAnt._sejour_id){
    $V(oFormAntFrmGrid._sejour_id,  oFormAnt._sejour_id.value);
	}
});

</script>

<!-- Antécédents -->
{{assign var=numCols value=4}}
{{math equation="100/$numCols" assign=width format="%.1f"}}
<table id="antecedents" class="main" style="display: none;">
  <tr>
    <td colspan="3">
			<form name="editAntFrmGrid" action="?m=dPcabinet" method="post" onsubmit="return window.opener.onSubmitAnt(this);">
			  <input type="hidden" name="m" value="dPpatients" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="dosql" value="do_antecedent_aed" />
			  <input type="hidden" name="_patient_id" value="" />
			  <input type="hidden" name="_sejour_id" value="" />
			
			  <input type="hidden" name="_hidden_rques" value="" />
			  <input type="hidden" name="rques" onchange="this.form.onsubmit();"/>
			 
			 <input type="hidden" name="type" />
			 <input type="hidden" name="appareil" />
       
			  {{mb_label object=$antecedent field=_search}}
			  {{mb_field object=$antecedent field=_search size=25 class="autocomplete"}}
			  {{mb_include_script module=dPcompteRendu script=aideSaisie}}
			  <script type="text/javascript">
			    Main.add(function() {
			      prepareForm(document.editAntFrmGrid);
			      new AideSaisie.AutoComplete("editAntFrmGrid" , "rques", "type", "appareil", "_search", "CAntecedent", "{{$user_id}}");
			    } );
			  </script>
      </form>
    </td>	
  </tr>
  <tr>
    <td style="width: 0.1%; vertical-align: top;">
      <ul id="tab-antecedents" class="control_tabs_vertical">
      {{foreach from=$antecedent->_count_rques_aides item=count key=type}}
        <li>
          <a href="#antecedents_{{$type}}" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CAntecedent.type.{{$type}}{{/tr}}
        		{{if $count}}
        		<small>({{$count}})</small>
						{{/if}}
        	</a>
        </li>
      {{/foreach}}
      </ul>
    </td>
    
    <td>
      {{foreach from=$antecedent->_count_rques_aides item=count key=type}}
      {{if $count}}
	      <table class="main tbl" id="antecedents_{{$type}}" style="display: none;">
	        {{foreach from=$aides_antecedent.$type item=_aides key=appareil}}
	        <tr>
	          <th colspan="1000">
	          	 <button style="float: right" class="add notext" onclick="$('textarea-ant-{{$type}}-{{$appareil}}').toggle(); this.toggleClassName('remove').toggleClassName('add')">Ajouter</button>
							{{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}}
						</th>
	        </tr>
					<tr id="textarea-ant-{{$type}}-{{$appareil}}" style="display: none;">
					  <td colspan="1000">
					  	<form name="addAnt-{{$type}}-{{$appareil}}" action="">
						  	<input name="antecedent" size="60"/>
								<button class="submit" type="button" onclick="$V(oFormAntFrmGrid.type, '{{$type}}'); $V(oFormAntFrmGrid.appareil, '{{$appareil}}'); $V(oFormAntFrmGrid.rques, this.form.antecedent.value); $V(this.form.antecedent, '');">Ajouter l'antécédent</button>
							</form>
					  </td>	
				  </tr>
	        <tr>
	        {{foreach from=$_aides item=curr_aide name=aides}}
	          {{assign var=i value=$smarty.foreach.aides.index}}
            {{assign var=text value=$curr_aide->text}}
            {{if isset($applied_antecedents.$type.$text|smarty:nodefaults)}}
              {{assign var=checked value=1}}
            {{else}}
              {{assign var=checked value=0}}
            {{/if}}
	          <td class="text" style="cursor: pointer; width: {{$width}}%; {{if $checked}}opacity: 0.3; cursor: default;{{/if}}" 
	              title="{{$curr_aide->text|smarty:nodefaults|JSAttribute}}" 
	              >
	            <label>
	            	<input type="checkbox" {{if $checked}}checked="checked" disabled="disabled"{{/if}} 
							         onclick="addAntecedent('{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$type}}', '{{$appareil}}', this)"/> 
								{{$curr_aide->name}}
							</label>
	          </td>
	          {{if ($i % $numCols) == ($numCols-1) && !$smarty.foreach.aides.last}}</tr><tr>{{/if}}
	        {{/foreach}}
	        </tr>
	        {{/foreach}}
	      </table>
			{{else}}
	      <div class="small-info" id="antecedents_{{$type}}" style="display: none;">
		      {{tr}}CAideSaisie.none{{/tr}}
		      pour le type
		      '{{tr}}CAntecedent.type.{{$type}}{{/tr}}'
				</div>
			{{/if}}
	    {{/foreach}}
    </td>
  </tr>
</table>