{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

ExConceptSpec.options = {{$spec->getOptions()|@json}};

updateFieldSpec = function(){
  var form = getForm("editFieldSpec");
  if (!checkForm(form)) return;
  
  var data = form.serialize(true);
  var fields = {};
  var str = "{{$spec->getSpecType()}}";
  
  Object.keys(data).each(function(k){
    var d = data[k];
    
    if (d !== "") {
      if (Object.isArray(d)) {
        d = d.filter(function(e){return e !== ""});
        if (d.length == 0) return;
      }
      
      str += " "+(k.split("[")[0]);
      if (Object.isArray(d))
        str += "|"+d.invoke("replace", /\s|\|/g, "").join("|");
      else {
        var v = d.strip();
        if (ExFieldSpec.options[k] != "bool" || v != "1") {
          str += "|"+v;
        }
      }
      
      fields[k] = d;
    }
  });
  
  str = str.strip();
  ExConceptSpec.prop = str;
  
  var fieldForm = getForm("{{$form_name}}");
  $V(fieldForm.prop, str);
}

avoidSpaces = function(event) {
  var key = Event.key(event);
  // space or pipe
  if (key == 32 || key == 124) Event.stop(event);
}

cloneTemplate = function(input) {
  var template = $(input).up('table').down('.template');
  var clone = template.clone(true).observe("change", updateFieldSpec);
  template.insert({before: clone.show().removeClassName('template')});
  clone.down('input[type=text]').tryFocus();
}

confirmDelEnum = function(button) {
  if (!confirm("Voulez-vous vraiment supprimer cette valeur ? Elles seront supprimées de la base.")) return false;
  $(button).up("tr").remove(); 
	updateFieldSpec();
	return true;
}

updateInternalNameEnum = function(e) {
  var target = $(e).up('tr').down('input.internal');
  if (target.readOnly) return;
  
  $V(target, ExField.slug($V(e)));
}

updateTriggerData = function(select) {
  var fieldForm = getForm("{{$form_name}}");
  $V(fieldForm._triggered_data, $V(select));
  $$("select.triggered-data-select").without(select).each(function(s){
    s.selectedIndex = 0;
  });
}

Main.add(function(){
  var form = getForm("editFieldSpec");
	
  form.select("input.nospace").invoke("observe", "keypress", avoidSpaces);
  form.select("input, select").invoke("observe", "change", updateFieldSpec);
	
  updateFieldSpec();
});
</script>

<form name="editFieldSpec" action="?" method="get" onsubmit="return false">
  
<table class="main form">
	<col class="narrow" />
	
	{{assign var=advanced_controls_limit value=4}}
	
  {{foreach from=$options item=_type key=_name name=specs}}
	  {{if $smarty.foreach.specs.index == $advanced_controls_limit}}
		  <tr>
		  	<th></th>
		  	<td>
		  		<button class="down" type="button" onclick="$(this).up('table').select('tr.advanced').invoke('toggle')">Plus d'options</button>
        </td>
			</tr>
		{{/if}}
		
    <tr {{if $smarty.foreach.specs.index >= $advanced_controls_limit}}class="advanced" style="display: none;"{{/if}}>
      <th><label for="{{$_name}}" title="{{$_name}}">{{tr}}CMbFieldSpec.{{$_name}}{{/tr}}</label></th>
      <td>
        {{assign var=spec_value value=$spec->$_name}}
				
        {{* str *}}
        {{if $_type == "str"}}
          <input type="text" name="{{$_name}}" value="{{$spec_value}}" class="str nospace regex|^\s*[a-zA-Z0-9_]*\s*$|gi" />
          
        {{* num *}}
        {{elseif $_type == "num"}}
          <input type="text" name="{{$_name}}" value="{{$spec_value}}" class="str nospace regex|^\s*-?[\d\.]*\s*$" size="2" />
          
        {{* bool *}}
        {{elseif $_type == "bool"}}
          <label><input type="radio" name="{{$_name}}" value=""  {{if $spec_value === null || $spec_value === ""}}checked="checked"{{/if}} /> {{tr}}Undefined{{/tr}}</label>
          <label><input type="radio" name="{{$_name}}" value="0" {{if $spec_value === 0 || $spec_value === "0"}}checked="checked"{{/if}} /> {{tr}}No{{/tr}}</label>
          <label><input type="radio" name="{{$_name}}" value="1" {{if $spec_value == 1}}checked="checked"{{/if}} /> {{tr}}Yes{{/tr}}</label>
          
        {{* enum *}}
        {{elseif is_array($_type)}}
          {{foreach from=$_type item=_type}}
          <label><input type="radio" name="{{$_name}}" value="{{$_type}}" {{if $spec_value === $_type}}checked="checked"{{/if}} /> {{tr}}CMbFieldSpec.{{$_name}}.{{$_type}}{{/tr}} </label>
          {{/foreach}}
          
        {{* field *}}
        {{elseif $_type == "field"}}
          {{if $other_fields|@count}}
            <select name="{{$_name}}">
              <option value=""> &mdash; </option>
              {{foreach from=$other_fields item=_other_field}}
                <option value="{{$_other_field}}" {{if $_other_field == $spec_value}}selected="selected"{{/if}}>{{$_other_field}}</option>
              {{/foreach}}
            </select>
          {{else}}
            <input type="hidden" name="{{$_name}}" value="" />
            <span style="color: #999">Aucun autre champ</span>
          {{/if}}
          
        {{* list *}}
        {{elseif $_type == "list"}}
				  
					{{* {{if $context instanceof CExConcept && $list_owner instanceof CExList}}
            
						<table class="tbl" style="width: 1%;">
						  <col class="narrow" />
							
	            <tr>
	              <th {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none;"{{/if}}>Valeur</th>
	              {{if $list_owner->coded}}
								  <th>Code</th>
								{{/if}}
	              <th>Nom</th>
	            </tr>
	            
	            {{foreach from=$spec->_list key=_key item=_value}}
	              <tr>
	                <td style="text-align: right; {{if $app->user_prefs.INFOSYSTEM == 0}}display: none;{{/if}}">
	                  {{$_value}}
	                  <input type="hidden" name="{{$_name}}[]" class="internal" value="{{$_value}}" />
	                </td>
	                {{if $list_owner->coded}}
									  <td>
									  	{{$ex_list->_ref_items.$_value->code}}
										</td>
									{{/if}}
	                <td>{{$spec->_locales.$_value}}</td>
	              </tr>
	            {{foreachelse}}
	              <tr>
	                <td {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none;"{{/if}}></td>
	                <td colspan="{{$list_owner->coded|ternary:3:2}}" class="empty">Aucun élément</td>
	              </tr>
	            {{/foreach}}
		        </table>
            
					{{else}} *}}
          
					  {{if $context && $context->_id}}
						  {{if $context == $list_owner}}
	              {{foreach from=$spec->_list key=_key item=_value}}
	                <input type="hidden" name="{{$_name}}[]" class="internal" value="{{$_value}}" />
	              {{/foreach}}
							{{/if}}
					    <em>Voir "{{tr}}CExList-back-list_items{{/tr}}"</em>
						{{else}}
              <em>Enregistrez avant d'ajouter des élements</em>
						{{/if}}
            
          {{* {{/if}} *}}
					 
        {{* class *}}
        {{elseif $_type == "class"}}
          <select name="{{$_name}}">
            {{foreach from=$classes item=_value}}
              <option value="{{$_value}}" {{if $_value == $spec->class}}selected="selected"{{/if}}>{{$_value}}</option>
            {{/foreach}}
          </select>
          
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>

{{if $spec instanceof CEnumSpec && $context && $context->_id}}
  {{if $context == $list_owner}}
	  </form>
    {{mb_include module=system template=inc_ex_list_item_edit}}
  {{else}}
    {{mb_include module=system template=inc_ex_list_item_subset}}
		</form>
  {{/if}}
{{else}}
  </form>
{{/if}}


