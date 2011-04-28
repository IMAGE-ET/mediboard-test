{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("editConstraint");
  toggleObjectSelector(form.elements.field);
});

toggleObjectSelector = function(select) {
  var selected = select.options[select.selectedIndex];
  var specType = selected.className.split(" ")[0];
  var spec = selected.getProperties();
  
  $$('.specfield').invoke("disableInputs");
  
  var specElements = $$('.spectype-'+specType);
  
  if (specElements.length == 0) {
    specElements = $$('.spectype-all');
  }
  
  specElements.invoke("enableInputs");
  
  switch (specType) {
    default: 
      break;
    
    case "ref":
      $V(select.form._object_class, spec.class);
      break;
      
    case "enum":
      break;
  }
}
</script>

<form name="editConstraint" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: ExClass.edit.curry({{$ex_constraint->ex_class_id}})})">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_constraint_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$ex_constraint}}
  {{mb_field object=$ex_constraint field=ex_class_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ex_constraint colspan="2"}}
    
    <tr>
      <th style="width: 12em;">
        {{mb_label object=$ex_constraint field=field}}
      </th>
      <td>
        <select name="field" class="{{$ex_constraint->_props.field}}"
                tabIndex="1" onchange="toggleObjectSelector(this)">
          <option value=""> &mdash; </option>
          {{foreach from=$ex_constraint->_ref_ex_class->_host_class_fields key=_field item=_spec name=_constraint}}
            <option class="{{$_spec}}" value="{{$_field}}" 
                    {{if $ex_constraint->field==$_field}} selected="selected" {{/if}}>
              {{tr}}{{$ex_constraint->_ref_ex_class->host_class}}-{{$_field}}{{/tr}} 
              
              [ 
                {{if $_spec instanceof CRefSpec && $_spec->class}}
                  {{if $_spec->meta}}
                    {{assign var=_meta value=$_spec->meta}}
                    {{assign var=_meta_spec value=$ex_constraint->_ref_ex_class->_host_class_fields.$_meta}}
                    {{" OU "|@implode:$_meta_spec->_locales}}
                  {{else}}
                    {{tr}}{{$_spec->class}}{{/tr}}
                  {{/if}}
                {{else}}
                  {{tr}}CMbFieldSpec.type.{{$_spec->getSpecType()}}{{/tr}}
                {{/if}}
              ]
            </option>
            
            {{if $_spec instanceof CRefSpec}}
              {{foreach from=$_spec->_subspecs key=_subfield item=_subspec}}
                <option class="{{$_subspec}}" value="{{$_field}}-{{$_subfield}}" 
                        {{if $ex_constraint->field == "$_field-$_subfield" }} selected="selected" {{/if}}>
                  &nbsp; |&ndash; {{tr}}{{$_subspec->className}}-{{$_subfield}}{{/tr}} 
                  
                  [ 
                    {{if $_subspec instanceof CRefSpec && $_subspec->class}}
                      {{if $_subspec->meta}}
                        {{assign var=_meta value=$_subspec->meta}}
                        {{assign var=_meta_spec value=$ex_constraint->_ref_ex_class->_host_class_fields.$_meta}}
                        {{" OU "|@implode:$_meta_spec->_locales}}
                      {{else}}
                        {{tr}}{{$_subspec->class}}{{/tr}}
                      {{/if}}
                    {{else}}
                      {{tr}}CMbFieldSpec.type.{{$_subspec->getSpecType()}}{{/tr}}
                    {{/if}}
                  ]
                </option>
              {{/foreach}}
            {{/if}}
          {{/foreach}}
        </select>
      </td>
      
      <!--
      <th>{{mb_label object=$ex_constraint field=_locale}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale tabIndex="4"}}</td>
      -->
    </tr>
    <tr>
      <th>{{mb_label object=$ex_constraint field=operator}}</th>
      <td>{{mb_field object=$ex_constraint field=operator tabIndex="2"}}</td>
      
      <!--
      <th>{{mb_label object=$ex_constraint field=_locale_court}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale_court tabIndex="5"}}</td>
      -->
    </tr>
    <tr>
      <th>{{mb_label object=$ex_constraint field=value}}</th>
      <td>
        <div class="specfield spectype-all">
          {{mb_field object=$ex_constraint field=value tabIndex="3"}}
        </div>
        
        <div class="specfield spectype-bool">
          {{mb_field object=$ex_constraint prop="bool" field=value tabIndex="3"}}
        </div>
        
        <div class="specfield spectype-ref">
          <input type="hidden" name="_object_class" value="{{$ex_constraint->_ref_target_object->_class_name}}" />
          <input type="text" name="_object_view" readonly="readonly" value="{{$ex_constraint->_ref_target_object}}" />
          <button type="button" class="search notext" onclick="ObjectSelector.init()">{{tr}}Search{{/tr}}</button>
          <script type="text/javascript">
            ObjectSelector.init = function(){  
              this.sForm     = "editConstraint";
              this.sId       = "value";
              this.sView     = "_object_view";
              this.sClass    = "_object_class";
              this.onlyclass = "true";
              this.pop();
            }
            
            ObjectSelector.set = function(oObject) {
              var oForm = getForm(this.sForm);
              
              if (oForm[this.sView]) {
                $V(oForm[this.sView], oObject.view);
              }
              
              $V(oForm[this.sClass], oObject.objClass);
              $V(oForm[this.sId], oObject.objClass+"-"+oObject.id);
            }
          </script>
        </div>
      </td>
      
      <!--
      <th>{{mb_label object=$ex_constraint field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale_desc tabIndex="6"}}</td>
      -->
    </tr>
      
    <tr>
      <th></th>
      <td colspan="1">
        {{if $ex_constraint->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'la contrainte ',objName:'{{$ex_constraint->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
