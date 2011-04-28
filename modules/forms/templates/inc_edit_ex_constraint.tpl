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
  $("exClassConstraintList").down("tr[data-constraint_id={{$ex_constraint->_id}}]").addUniqueClassName("selected");
	
  var form = getForm("editConstraint");
  toggleObjectSelector(form.elements.field, form.elements.field);
	
  var form = getForm("editConstraint");
  var url = new Url("forms", "ajax_autocomplete_hostfields");
  url.addParam("ex_class_id", "{{$ex_constraint->ex_class_id}}");
  url.autoComplete(form.elements._host_field_view, null, {
    minChars: 2,
    method: "get",
    //select: "view",
    dropdown: true,
    afterUpdateElement: function(field, selected){
      toggleObjectSelector(field, selected);
      $V(field.form.elements.field, selected.get("value"));
      $V(field.form.elements._host_field_view, selected.down(".view").getText().strip());
			
      /*if ($V(field.form.elements._host_field_view) == "") {
        $V(field.form.elements._host_field_view, selected.down('.view').innerHTML);
      }*/
    }
  });
});

toggleObjectSelector = function(input, selected) {
  var prop = selected.get("prop");
  var specType = prop.split(" ")[0];
	var dummy = DOM.div({className: prop});
  var spec = dummy.getProperties();
  
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
      $V(input.form._object_class, spec.class);
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
			  <input type="text" class="autocomplete" name="_host_field_view" value="{{$ex_constraint}}" size="60" />
        <input type="hidden" name="field" class="{{$ex_constraint->_props.field}}" tabIndex="1" value="{{$ex_constraint->field}}" />
      </td>
      
      {{* 
      <th>{{mb_label object=$ex_constraint field=_locale}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale tabIndex="4"}}</td>
      *}}
    </tr>
    <tr>
      <th>{{mb_label object=$ex_constraint field=operator}}</th>
      <td>{{mb_field object=$ex_constraint field=operator tabIndex="2"}}</td>
      
      {{* 
      <th>{{mb_label object=$ex_constraint field=_locale_court}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale_court tabIndex="5"}}</td>
      *}}
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
      
      {{* 
      <th>{{mb_label object=$ex_constraint field=_locale_desc}}</th>
      <td>{{mb_field object=$ex_constraint field=_locale_desc tabIndex="6"}}</td>
      *}}
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
