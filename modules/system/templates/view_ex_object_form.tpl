{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
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
</script>

{{mb_form name="editExObject" m="system" dosql="do_ex_object_aed" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: window.close})"}}
  {{mb_key object=$ex_object}}
  {{mb_field object=$ex_object field=_ex_class_id hidden=true}}
  
  {{mb_field object=$ex_object field=object_class hidden=true}}
  {{mb_field object=$ex_object field=object_id hidden=true}}
  
  <input type="hidden" name="del" value="0" />
		
  <table class="main form">
    <tr>
      <th class="title" colspan="4">
        {{$ex_object->_ref_ex_class}} - {{$object}}
      </th>
    </tr>
		
    {{foreach from=$grid key=_y item=_line}}
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
		          {{assign var=_field value=$_group.object}} 
		          <td>
		          	{{assign var=_field_name value=$_field->name}}
				        {{assign var=_spec value=$ex_object->_specs.$_field_name}}
				        
				        {{if $_spec instanceof CRefSpec}}
				          <script type="text/javascript">
				          Main.add(function(){
				            var form = getForm("editExObject");
				            var url = new Url("system", "ajax_seek_autocomplete");
				            
				            url.addParam("object_class", "{{$_spec->class}}");
				            url.addParam("field", "{{$_field_name}}");
				            url.addParam("input_field", "_{{$_field_name}}_view");
				            url.autoComplete(form.elements["_{{$_field_name}}_view"], null, {
				              minChars: 3,
				              method: "get",
				              select: "view",
				              dropdown: true,
				              afterUpdateElement: function(field,selected){
				                $V(field.form["{{$_field_name}}"], selected.getAttribute("id").split("-")[2]);
				                if ($V(field.form.elements["_{{$_field_name}}_view"]) == "") {
				                  $V(field.form.elements["_{{$_field_name}}_view"], selected.down('.view').innerHTML);
				                }
				              }
				            });
				          });
				          </script>
				          <input type="text" class="autocomplete" name="_{{$_field_name}}_view" value="{{$ex_object->_fwd.$_field_name}}" size="30" />
				          {{mb_field object=$ex_object field=$_field->name form=editExObject hidden=true}}
				        {{else}}
				          {{mb_field object=$ex_object field=$_field->name register=true increment=true form=editExObject}}
				        {{/if}}
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
    {{foreach from=$ex_object->_ref_ex_class->_ref_fields item=_field}}
      {{assign var=_field_name value=$_field->name}}
			
		  {{if isset($out_of_grid.field.$_field_name|smarty:nodefaults)}}
		    <tr>
		      <th>
		        {{mb_label object=$ex_object field=$_field->name}}
		      </th>
		      <td colspan="3">
		        {{assign var=_spec value=$ex_object->_specs.$_field_name}}
		        
		        {{if $_spec instanceof CRefSpec}}
		          <script type="text/javascript">
		          Main.add(function(){
		            var form = getForm("editExObject");
		            var url = new Url("system", "ajax_seek_autocomplete");
		            
		            url.addParam("object_class", "{{$_spec->class}}");
		            url.addParam("field", "{{$_field_name}}");
		            url.addParam("input_field", "_{{$_field_name}}_view");
		            url.autoComplete(form.elements["_{{$_field_name}}_view"], null, {
		              minChars: 3,
		              method: "get",
		              select: "view",
		              dropdown: true,
		              afterUpdateElement: function(field,selected){
		                $V(field.form["{{$_field_name}}"], selected.getAttribute("id").split("-")[2]);
		                if ($V(field.form.elements["_{{$_field_name}}_view"]) == "") {
		                  $V(field.form.elements["_{{$_field_name}}_view"], selected.down('.view').innerHTML);
		                }
		              }
		            });
		          });
		          </script>
		          <input type="text" class="autocomplete" name="_{{$_field_name}}_view" value="{{$ex_object->_fwd.$_field_name}}" size="30" />
		          {{mb_field object=$ex_object field=$_field->name form=editExObject hidden=true}}
		        {{else}}
		          {{mb_field object=$ex_object field=$_field->name register=true increment=true form=editExObject}}
		        {{/if}}
		      </td>
		    </tr>
		  {{/if}}
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