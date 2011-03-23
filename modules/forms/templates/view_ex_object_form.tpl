{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !@$readonly}}

<script type="text/javascript">
if (window.opener && window.opener !== window) {
  window.onunload = function(){
    window.opener.ExObject.register("{{$_element_id}}", {
      ex_class_id: "{{$ex_class_id}}", 
      object_guid: "{{$object_guid}}", 
      event: "{{$event}}", 
      _element_id: "{{$_element_id}}"
    });
  }
}

formulaTokenValues = {{$formula_token_values|@json}};
exGroups = {};

// get the input value : coded or non-coded
getInputValue = function(element){
  var value = $V(element);
  
  if (element instanceof NodeList || element instanceof HTMLCollection) {
    element = element[0];
  }
  
  var name = element.name;
  var result = formulaTokenValues[name];

  // non-coded
  if (result === true)
    return value;

  // coded
  return formulaTokenValues[name][value];
}

// computes the result of a form + exGroup(formula, resultField)
computeResult = function(form, exGroup){
  if (!exGroup.formula || !exGroup.resultField) return;

  var formula = exGroup.formula.replace(/[\[\]]/g, "");

  var parser = Parser.parse(formula);
  var vars = parser.variables();
  var values = {};
  
  vars.each(function(v){
    values[v] = getInputValue(form[v]);
  });

  $V(form[exGroup.resultField], parser.evaluate(values));
}

Main.add(function(){
  var c, inputs;
  
  {{foreach from=$grid key=_group_id item=_grid}}
    c = $("tab-{{$groups.$_group_id->_guid}}");
    inputs = c.select("input, textarea, select").filter(function(e){ return !e.readonly && !e.disabled });
    
    exGroups["{{$groups.$_group_id->_id}}"] = {
      formula: {{$groups.$_group_id->formula|@json}},
      resultField: {{$groups.$_group_id->_ref_formula_result_field->name|@json}}
    };

    function compute(event) {
      var element = Event.element(event);
      computeResult(element.form, exGroups["{{$groups.$_group_id->_id}}"]);
    }
    
    inputs.each(function(input){
      input.observe("change", compute);
      input.observe("ui:change", compute);
    });
  {{/foreach}}
});
</script>

{{mb_form name="editExObject" m="system" dosql="do_ex_object_aed" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: window.close})"}}
  {{mb_key object=$ex_object}}
  {{mb_field object=$ex_object field=_ex_class_id hidden=true}}
  {{mb_field object=$ex_object field=object_class hidden=true}}
  {{mb_field object=$ex_object field=object_id hidden=true}}
  
  <input type="hidden" name="del" value="0" />
	
	<h2>{{$ex_object->_ref_ex_class}} - {{$object}}</h2>
	
	{{main}}
	  Control.Tabs.create("ex_class-groups-tabs");
	{{/main}}
	
	<ul id="ex_class-groups-tabs" class="control_tabs">
	{{foreach from=$grid key=_group_id item=_grid}}
	  <li>
	  	<a href="#tab-{{$groups.$_group_id->_guid}}">{{$groups.$_group_id}}</a>
	  </li>
	{{/foreach}}
	</ul>
  <hr class="control_tabs" />
	
  <table class="main form">
  	
		{{foreach from=$grid key=_group_id item=_grid}}
		<tbody id="tab-{{$groups.$_group_id->_guid}}" style="display: none;">
    {{assign var=has_formula value=false}}
    {{if $groups.$_group_id->formula != null && $groups.$_group_id->formula_result_field_id}}
      {{assign var=has_formula value=true}}
    {{/if}}
		
    {{foreach from=$_grid key=_y item=_line}}
    <tr>
      {{foreach from=$_line key=_x item=_group}}
	      {{if $_group.object}}
	        {{if $_group.object instanceof CExClassField}}
					  {{if $_group.type == "label"}}
						  {{assign var=_field value=$_group.object}} 
		          <th style="font-weight: bold;">
		            {{mb_label object=$ex_object field=$_field->name}}
		          </th>
					  {{elseif $_group.type == "field"}}
		          <td>
                {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_group.object has_formula=$has_formula}}
		          </td>
						{{/if}}
					{{else}}
            {{assign var=_host_field value=$_group.object}} 
					  	{{if $_group.type == "label"}}
							  <th style="font-weight: bold;">
					    	  {{mb_label object=$ex_object->_ref_object field=$_host_field->field}}
								</th>
							{{else}}
                <td>
                  {{mb_value object=$ex_object->_ref_object field=$_host_field->field}}
                </td>
							{{/if}}
					{{/if}}
        {{else}}
          <td></td>
				{{/if}}
      {{/foreach}}
    </tr>
    {{/foreach}}
		
		{{* Out of grid *}}
    {{foreach from=$groups.$_group_id->_ref_fields item=_field}}
      {{assign var=_field_name value=$_field->name}}
			
		  {{if isset($out_of_grid.$_group_id.field.$_field_name|smarty:nodefaults)}}
		    <tr>
		      <th>
		        {{mb_label object=$ex_object field=$_field->name}}
		      </th>
		      <td colspan="3">
		        {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field}}
		      </td>
		    </tr>
		  {{/if}}
    {{/foreach}}
    
    </tbody>
    {{/foreach}}
		
    <tr>
      <td colspan="4" class="button">
        {{if $ex_object->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$ex_object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
		
  </table>

{{/mb_form}}

{{else}}

<table class="main form">
    <tr>
      <th class="title" colspan="4">
        {{$ex_object->_ref_ex_class}} - {{$object}}
      </th>
    </tr>
    
    {{foreach from=$grid key=_y item=_line}}
    <tr>
      {{foreach from=$_line key=_x item=_group}}
        {{if $_group.label}}
          {{assign var=_field value=$_group.label}} 
          <th style="font-weight: bold;">
            {{mb_label object=$ex_object field=$_field->name}}
          </th>
        {{elseif $_group.field}}
          {{assign var=_field value=$_group.field}} 
          <td>
            {{mb_value object=$ex_object field=$_field->name}}
          </td>
        {{else}}
          <td></td>
        {{/if}}
      {{/foreach}}
    </tr>
    {{/foreach}}
  </table>
{{/if}}