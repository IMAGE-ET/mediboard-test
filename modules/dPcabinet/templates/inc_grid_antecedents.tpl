<script type="text/javascript">
function addAntecedent(rques, type, appareil, element) {
  if (window.opener) {
    var oForm = window.opener.document.forms['editAntFrm'];
    if (oForm) {
      oForm.rques.value = rques;
      oForm.type.value = type;
      oForm.appareil.value = appareil;
      window.opener.onSubmitAnt(oForm);
      
      var input = element.select('input').first();
      input.checked = true;
      input.onclick = function(){return false};
      
      $(element).setStyle({cursor: 'default', opacity: 0.3}).onclick = null;
    }
  }
  return false;
}

Main.add(function () {
  Control.Tabs.create('tab-antecedents', false);
});

</script>

<!-- Antécédents -->
{{assign var=numCols value=4}}
{{math equation="100/$numCols" assign=width format="%.1f"}}
<table id="antecedents" class="main" style="display: none;">
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
	          <th colspan="1000">{{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}}</th>
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
	              onclick="return addAntecedent('{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$type}}', '{{$appareil}}', this)">
	            <input type="checkbox" {{if $checked}}checked="checked"{{/if}} /> {{$curr_aide->name}}
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