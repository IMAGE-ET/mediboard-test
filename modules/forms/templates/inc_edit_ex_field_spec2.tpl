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
  
  /*var translations = data["__enum[]"];
  if (translations) {
    translations.pop(); // pour supprimer l'element vide
    delete data["__enum[]"];
  }*/
  
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
  
  /*if (translations)
    $V(fieldForm._enum_translation, Object.toJSON(translations));*/
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
  
	<tr>
    <th class="title" colspan="2">Paramètres</th>
  </tr>
  
	{{assign var=advanced_controls_limit value=3}}
	
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
        {{* str *}}
        {{if $_type == "str"}}
          <input type="text" name="{{$_name}}" value="{{$spec->$_name}}" class="str nospace regex|^\s*[a-zA-Z0-9_]*\s*$|gi" />
          
        {{* num *}}
        {{elseif $_type == "num"}}
          <input type="text" name="{{$_name}}" value="{{$spec->$_name}}" class="str nospace regex|^\s*-?[\d\.]*\s*$" size="2" />
          
        {{* bool *}}
        {{elseif $_type == "bool"}}
          <label><input type="radio" name="{{$_name}}" value="" {{if $spec->$_name === null || $spec->$_name === ""}}checked="checked"{{/if}} /> {{tr}}Undefined{{/tr}}</label>
          <label><input type="radio" name="{{$_name}}" value="0" {{if $spec->$_name === 0 || $spec->$_name === "0"}}checked="checked"{{/if}} /> {{tr}}No{{/tr}}</label>
          <label><input type="radio" name="{{$_name}}" value="1" {{if $spec->$_name == 1}}checked="checked"{{/if}} /> {{tr}}Yes{{/tr}}</label>
          
        {{* enum *}}
        {{elseif is_array($_type)}}
          {{foreach from=$_type item=_type}}
          <label><input type="radio" name="{{$_name}}" value="{{$_type}}" {{if $spec->$_name === $_type}}checked="checked"{{/if}} /> {{tr}}CMbFieldSpec.{{$_name}}.{{$_type}}{{/tr}} </label>
          {{/foreach}}
          
        {{* field *}}
        {{elseif $_type == "field"}}
          {{if $other_fields|@count}}
            <select name="{{$_name}}">
              <option value=""> &mdash; </option>
              {{foreach from=$other_fields item=_other_field}}
                <option value="{{$_other_field}}" {{if $_other_field == $spec->$_name}}selected="selected"{{/if}}>{{$_other_field}}</option>
              {{/foreach}}
            </select>
          {{else}}
            <input type="hidden" name="{{$_name}}" value="" />
            <span style="color: #999">Aucun autre champ</span>
          {{/if}}
          
        {{* list *}}
        {{elseif $_type == "list"}}
				  
					{{if $ex_list->_id}}
					  <a class="button edit" href="?m=forms&amp;tab=view_ex_list&amp;object_guid={{$ex_list->_guid}}">{{tr}}CExList-title-modify{{/tr}}</a>
						<table class="tbl" style="width: 1%;">
						  <col class="narrow" />
							
	            <tr>
	              <th {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none;"{{/if}}>Valeur</th>
	              {{if $ex_list->coded}}
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
	                {{if $ex_list->coded}}
									  <td>
									  	{{$ex_list->_ref_items.$_value->code}}
										</td>
									{{/if}}
	                <td>{{$spec->_locales.$_value}}</td>
	              </tr>
	            {{foreachelse}}
	              <tr>
	                <td {{if $app->user_prefs.INFOSYSTEM == 0}}style="display: none;"{{/if}}></td>
	                <td colspan="{{$ex_list->coded|ternary:3:2}}" class="empty">Aucun élément</td>
	              </tr>
	            {{/foreach}}
		        </table>
					{{else}}
					  {{if $owner && $owner->_id}}
              {{foreach from=$spec->_list key=_key item=_value}}
                <input type="hidden" name="{{$_name}}[]" class="internal" value="{{$_value}}" />
              {{/foreach}}
					    <em>Voir "{{tr}}CExList-back-list_items{{/tr}}"</em>
						{{else}}
              <em>Enregistrez avant d'ajouter des élements</em>
						{{/if}}
          {{/if}}
					 
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
</form>

{{if $spec instanceof CEnumSpec && !$ex_list->_id && $owner && $owner->_id}}
  {{mb_include module=system template=inc_ex_list_item_edit object=$owner}}
{{/if}}