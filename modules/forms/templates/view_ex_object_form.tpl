{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<style type="text/css">
fieldset {
  margin: 0px;
}

div.ex-message-title {
  font-weight: bold;
  border-bottom: 1px solid #666;
  font-size: 1.2em;
  left: 0.5em; 
  right: 0.5em; 
	line-height: 1.4;
	padding-top: 3px;
  position: absolute;
}

span.ex-message-title-spacer {
  font-size: 1.6em;
}
</style>

{{if !@$readonly}}

<script type="text/javascript">
confirmSavePrint = function(form){
  form.callback.disabled = true;
	
  (FormObserver.changes == 0 || confirm("Pour imprimer le formulaire, il est nécessaire de l'enregistrer, souhaitez-vous continuer ?")) && 
           onSubmitFormAjax(form, {onComplete: function(){ 
             FormObserver.changes = 0;
             $("printIframe").src = "about:blank";
             $("printIframe").src = "?{{$smarty.server.QUERY_STRING|html_entity_decode}}&readonly=1&print=1&autoprint=1";
          } });
					
  form.callback.disabled = false;
	
  return false;
}

updateExObjectId = function(id, obj) {
  $V(getForm("editExObject").ex_object_id, id);
	
	if (!(obj._ui_messages[2] || obj._ui_messages[4] || obj._ui_messages[4])) {
	  window.close();
	}
}

Main.add(function(){
  ExObjectFormula.init({{$formula_token_values|@json}});
  ExObject.current = {object_guid: "{{$object_guid}}", event: "{{$event}}"};
});

if (window.opener && !window.opener.closed && window.opener !== window && window.opener.ExObject) {
  window.onunload = function(){
	  var element_id = "{{$_element_id}}";
		
		if (element_id.charAt(0) == "@") {
		  window.opener[element_id.substr(1)]();
		}
		else {
	    window.opener.ExObject.register.defer("{{$_element_id}}", {
	      ex_class_id: "{{$ex_class_id}}", 
	      object_guid: "{{$object_guid}}", 
	      event: "{{$event}}", 
	      _element_id: "{{$_element_id}}"
	    });
		}
  }
}
</script>

{{mb_form name="editExObject" m="system" dosql="do_ex_object_aed" method="post" className="watched"
          onsubmit="return onSubmitFormAjax(this)"}}
  {{mb_key object=$ex_object}}
  {{mb_field object=$ex_object field=_ex_class_id hidden=true}}
  {{mb_field object=$ex_object field=object_class hidden=true}}
  {{mb_field object=$ex_object field=object_id hidden=true}}
  
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="updateExObjectId" />
  
  {{if !$print}}
    <iframe id="printIframe" width="0" height="0" style="display: none;"></iframe>
    <button type="button" class="print" onclick="confirmSavePrint(this.form)" style="float: right;">
      {{tr}}Print{{/tr}}
    </button>
  {{/if}}
  
  <h2 style="font-weight: bold;">
    {{if $ex_object->_ref_reference_object_2 && $ex_object->_ref_reference_object_2->_id}}
      <span style="color: #006600;" 
           onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_reference_object_2->_guid}}');">
        {{$ex_object->_ref_reference_object_2}} 
      </span>
    {{else}}
      {{if in_array("IPatientRelated", class_implements($ex_object->object_class))}}
        {{assign var=_patient value=$ex_object->_ref_object->loadRelPatient()}}
        <span style="color: #006600;" 
             onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}');">
          {{$_patient}}
        </span>
      {{/if}}
    {{/if}}
    
    {{if $ex_object->_ref_reference_object_1 && $ex_object->_ref_reference_object_1->_id}}
      &ndash;
      <span onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_reference_object_1->_guid}}');">
        {{$ex_object->_ref_reference_object_1}}
      </span>
    {{/if}}
    
    <hr style="border-color: #333; margin: 4px 0;" />
    {{$ex_object->_ref_ex_class->name}} - {{$object}}
		
		{{if $parent_view}}
			<span style="float: right; color: #666;">
				Formulaire parent: {{$parent_view|smarty:nodefaults}}
			</span>
		{{/if}}
  </h2>
  
  <script type="text/javascript">
    Main.add(function(){
      Control.Tabs.create("ex_class-groups-tabs");
      document.title = "{{$ex_object->_ref_ex_class->name}} - {{$object}}".htmlDecode();
    });
  </script>
  
  <ul id="ex_class-groups-tabs" class="control_tabs">
  {{foreach from=$grid key=_group_id item=_grid}}
    {{if $groups.$_group_id->_ref_fields|@count}}
    <li>
      <a href="#tab-{{$groups.$_group_id->_guid}}">{{$groups.$_group_id}}</a>
    </li>
    {{/if}}
  {{/foreach}}
  </ul>
  <hr class="control_tabs" />
  
  <table class="main form">
    
    {{foreach from=$grid key=_group_id item=_grid}}
    {{if $groups.$_group_id->_ref_fields|@count}}
    <tbody id="tab-{{$groups.$_group_id->_guid}}" style="display: none;">
    {{foreach from=$_grid key=_y item=_line}}
    <tr>
      {{foreach from=$_line key=_x item=_group}}
        {{if $_group.object}}
          {{if $_group.object instanceof CExClassField}}
            {{assign var=_field value=$_group.object}}
            {{assign var=_field_name value=$_field->name}}
						
            {{if $_group.type == "label"}}
              {{if $_field->coord_field_x == $_field->coord_label_x+1}}
                <th style="font-weight: bold; vertical-align: middle;">
                  {{mb_label object=$ex_object field=$_field_name}}
                  {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
                </th>
              {{else}}
                <td style="font-weight: bold; text-align: left;">
                  {{mb_label object=$ex_object field=$_field_name}}
                  {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
                </td>
              {{/if}}
            {{elseif $_group.type == "field"}}
              <td>
                {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field}}
              </td>
            {{/if}}
          {{elseif $_group.object instanceof CExClassHostField}}
            {{assign var=_host_field value=$_group.object}} 
              {{if $_group.type == "label"}}
                <th style="font-weight: bold; text-align: left;">
                  {{mb_title object=$ex_object->_ref_object field=$_host_field->field}}
                </th>
              {{else}}
                <td>
                  {{mb_value object=$ex_object->_ref_object field=$_host_field->field}}
                </td>
              {{/if}}
          {{else}}
            {{assign var=_message value=$_group.object}} 
              {{if $_group.type == "message_title"}}
              
                {{if $_message->coord_text_x == $_message->coord_title_x+1}}
                  <th style="font-weight: bold; vertical-align: middle;">
                    {{$_message->title}}
                  </th>
                {{else}}
                  <td style="font-weight: bold; text-align: left;">
                    {{$_message->title}}
                  </td>
                {{/if}}
              {{else}}
                <td>
                  {{if $_message->type == "title"}}
                    <div class="ex-message-title">
                      {{$_message->text}}
                    </div>
                    <span class="ex-message-title-spacer">&nbsp;</span>
                  {{else}}
                    <div class="small-{{$_message->type}}">
                      {{mb_value object=$_message field=text}}
                    </div>
                  {{/if}}
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
          <th style="font-weight: bold; width: 50%; vertical-align: middle;" colspan="2">
            {{mb_label object=$ex_object field=$_field->name}}
					  {{mb_include module=forms template=inc_reported_value ex_object=$ex_object ex_field=$_field}}
          </th>
          <td colspan="2">
            {{mb_include module=forms template=inc_ex_object_field ex_object=$ex_object ex_field=$_field}}
          </td>
        </tr>
      {{/if}}
    {{/foreach}}
    
    </tbody>
    {{/if}}
    {{/foreach}}
    
    <tr>
      <td colspan="4" class="button">
        {{if $ex_object->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          
          {{if $forms_admin}}
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax: true, typeName:'', objName:'{{$ex_object->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
        {{else}}
          <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    
  </table>

{{/mb_form}}

{{else}}

{{* ----   READONLY   ---- *}}
  
<script type="text/javascript">
Main.add(function(){
  document.title = "{{$ex_object->_ref_ex_class->name}} - {{$object}}".htmlDecode();
	
  {{if $autoprint}}
	  if (document.execCommand) {
	    window.focus();
	    document.execCommand('print', false, null);
	  }
	  else {
	    window.print();
	  }
  {{/if}}    
});

function switchMode(){
  var only_filled = Url.parse().query.toQueryParams().only_filled;
  location.href = location.href.replace('only_filled='+only_filled, 'only_filled='+(only_filled == 1 ? 0 : 1));
}
</script>

{{if $print}}
  <div style="float: right;" class="not-printable">
	  <button class="change" onclick="switchMode()">
	  	Tous les champs
		</button>
		<button class="print" onclick="window.print()">{{tr}}Print{{/tr}}</button>
	</div>
{{/if}}

<table class="main {{if $print}} print {{else}} form {{/if}}">
	<thead>
	  <tr>
	    <td colspan="4">
	      <p style="font-weight: bold; font-size: 1.1em;">
	        {{if $ex_object->_ref_reference_object_2 && $ex_object->_ref_reference_object_2->_id}}
			      <span style="color: #006600;">
			        {{$ex_object->_ref_reference_object_2}} 
			      </span>
			    {{else}}
			      {{if in_array("IPatientRelated", class_implements($ex_object->object_class))}}
			        {{assign var=_patient value=$ex_object->_ref_object->loadRelPatient()}}
			        <span style="color: #006600;">
			          {{$_patient}}
			        </span>
			      {{/if}}
			    {{/if}}
			    
			    {{if $ex_object->_ref_reference_object_1 && $ex_object->_ref_reference_object_1->_id}}
			      &ndash;
			      <span>
			        {{$ex_object->_ref_reference_object_1}}
			      </span>
			    {{/if}}
			    
			    <br />
			    {{$ex_object->_ref_ex_class->name}} - {{$object}}
	      </p>
        <hr style="border-color: #333; margin: 4px 0;" />
	    </td>
	  </tr>
	</thead>
  
	{{if $only_filled}}
	
	  <tr>
	  	<td colspan="4">
	  		{{mb_include module=forms template=inc_vw_ex_object ex_object=$ex_object}}
	  	</td>
	  </tr>
		
	{{else}}
		
	  {{foreach from=$grid key=_group_id item=_grid}}
	  
	  {{if $groups.$_group_id->_ref_fields|@count}}
	  <tbody id="tab-{{$groups.$_group_id->_guid}}">
	    <tr>
	      <th class="title" colspan="4">{{$groups.$_group_id}}</th>
	    </tr>
	    
	  {{foreach from=$_grid key=_y item=_line}}
	  <tr>
	    {{foreach from=$_line key=_x item=_group}}
	      {{if $_group.object}}
	        {{if $_group.object instanceof CExClassField}}
	          {{assign var=_field value=$_group.object}}
	          {{if $_group.type == "label"}}
	            {{if $_field->coord_field_x == $_field->coord_label_x+1}}
	              <th style="font-weight: bold; vertical-align: middle; white-space: normal;">
	                {{mb_label object=$ex_object field=$_field->name}}
	              </th>
	            {{else}}
	              <td style="font-weight: bold; text-align: left;">
	                {{mb_label object=$ex_object field=$_field->name}}
	              </td>
	            {{/if}}
	          {{elseif $_group.type == "field"}}
	            <td>
	              {{mb_value object=$ex_object field=$_field->name}}
	            </td>
	          {{/if}}
	        {{elseif $_group.object instanceof CExClassHostField}}
	          {{assign var=_host_field value=$_group.object}} 
	            {{if $_group.type == "label"}}
	              <th style="font-weight: bold; text-align: left; white-space: normal;">
	                {{mb_title object=$ex_object->_ref_object field=$_host_field->field}}
	              </th>
	            {{else}}
	              <td>
	                {{mb_value object=$ex_object->_ref_object field=$_host_field->field}}
	              </td>
	            {{/if}}
	        {{else}}
	          {{assign var=_message value=$_group.object}} 
	            {{if $_group.type == "message_title"}}
	            
	              {{if $_message->coord_text_x == $_message->coord_title_x+1}}
	                <th style="font-weight: bold; vertical-align: middle; white-space: normal;">
	                  {{$_message->title}}
	                </th>
	              {{else}}
	                <td style="font-weight: bold; text-align: left;">
	                  {{$_message->title}}
	                </td>
	              {{/if}}
	            {{else}}
	              <td>
	                {{if $_message->type == "title"}}
	                  <div class="ex-message-title">
	                    {{$_message->text}}
	                  </div>
	                  <span class="ex-message-title-spacer">&nbsp;</span>
	                {{else}}
	                  <div class="small-{{$_message->type}}">
	                    {{mb_value object=$_message field=text}}
	                  </div>
	                {{/if}}
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
	        <th style="font-weight: bold; width: 50%; vertical-align: middle; white-space: normal;" colspan="2">
	          {{mb_label object=$ex_object field=$_field_name}}
	        </th>
	        <td colspan="2">
	          {{mb_value object=$ex_object field=$_field_name}}
	        </td>
	      </tr>
	    {{/if}}
	  {{/foreach}}
	  
	  </tbody>
	  {{/if}}
	  {{/foreach}}
	
	{{/if}}
	  
</table>
	

{{/if}}