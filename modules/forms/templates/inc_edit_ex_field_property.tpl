
{{mb_script module="mediusers" script="color_selector" ajax=true}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("ex_field_property-form");
  typeSwitcher(form.elements.type);
  
  var url = new Url("forms", "ajax_autocomplete_ex_class_field_predicate");
  url.autoComplete(form.elements.predicate_id_autocomplete_view, null, {
    minChars: 2,
    method: "get",
    select: "view",
    dropdown: true,
    afterUpdateElement: function(field, selected){
      var id = selected.get("id");
      
      if (!id) {
        $V(field.form.predicate_id, "");
        $V(field.form.elements.predicate_id_autocomplete_view, "");
        return;
      }
      
      $V(field.form.predicate_id, id);
      
      if (id) {
        showField(id, selected.down('.name').getText());
      }
      
      if ($V(field.form.elements.predicate_id_autocomplete_view) == "") {
        $V(field.form.elements.predicate_id_autocomplete_view, selected.down('.view').getText());
      }
    },
    callback: function(input, queryString){
      return queryString + "&ex_class_id={{$ex_class->_id}}"; 
    }
  });

  form._value_size.addSpinner({step: 1, min: 6});
});

typeSwitcher = function(select){
  var form = select.form;
  var type = $V(select);
  var showColor = {{"CExClassFieldProperty::getColorStyles"|static_call:""|@json}}.indexOf(type) > -1;
  
  form._color_selector.setVisible(showColor);

  $$(".type-switch").each(function(e){
    var b = e.hasClassName(type);
    e.setVisible(b);

    if (b) {
      e.onchange();
    }
  });
};

propertyCallback = function(id, obj) {
  {{if $opener_field_value && $opener_field_view}}
    $V($("{{$opener_field_value}}"), id);
    $V($("{{$opener_field_view}}"), obj._view);
  {{else if $ex_field_property->object_id}}
    {{if $ex_field_property->object_class == "CExClassField"}}
      ExField.edit("{{$ex_field_property->object_id}}");
    {{elseif $ex_field_property->object_class == "CExClassMessage"}}
      ExMessage.edit("{{$ex_field_property->object_id}}");
    {{elseif $ex_field_property->object_class == "CExClassFieldSubgroup"}}
      ExSubgroup.edit("{{$ex_field_property->object_id}}");
    {{/if}}
  {{/if}}
  
  Control.Modal.close();
};

ColorSelector.init = function() {
  this.sForm  = "ex_field_property-form";
  this.sColor = "value";
  this.sColorView = "ex_field_property-form-color";
  this.bAddSharp = true;
  this.pop();
};
</script>

<form name="ex_field_property-form" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="callback" value="propertyCallback" />
  <input type="hidden" name="del" value="" />
  {{mb_class object=$ex_field_property}}
  {{mb_key object=$ex_field_property}}
  {{mb_field object=$ex_field_property field=object_id hidden=true}}
  {{mb_field object=$ex_field_property field=object_class hidden=true}}
  
  <table class="main form">
    <tr>
      {{assign var=object value=$ex_field_property}}
      {{if $object->_id}}
      <th class="title modify text" colspan="4">
        {{mb_include module=system template=inc_object_idsante400}}
        {{mb_include module=system template=inc_object_history}}
        {{tr}}{{$object->_class}}-title-modify{{/tr}} 
        '{{$object}}'
      </th>
      {{else}}
      <th class="title text" colspan="4">
        {{tr}}{{$object->_class}}-title-create{{/tr}} 
      </th>
      {{/if}}
    </tr>
    
    <tr>
      <th>{{mb_label object=$ex_field_property field=type}}</th>
      <td>
        {{mb_field object=$ex_field_property field=type onchange="typeSwitcher(this)"}}
        
        {{mb_field object=$ex_field_property field=value hidden=true}}
        
        <button type="button" class="search" onclick="ColorSelector.init()" name="_color_selector">
          {{tr}}Choose{{/tr}}
          <span id="ex_field_property-form-color" 
                style="display: inline-block; vertical-align: top; padding: 0; margin: 0; border: none; width: 16px; height: 16px; background-color: {{if $ex_field_property->value}}{{$ex_field_property->value}}{{else}}transparent{{/if}};">
          </span>
        </button>

        {{foreach from="CExClassFieldProperty"|static:"_style_values" item=_values key=_type}}
          <select class="type-switch {{$_type}}" style="display: none;" onchange="$V(this.form.elements.value, $V(this))">
            {{foreach from=$_values item=_value}}
              <option value="{{$_value}}" {{if $ex_field_property->value == $_value}}selected{{/if}}>
                {{tr}}CExClassFieldProperty.value.{{$_type}}.{{$_value}}{{/tr}}
              </option>
            {{/foreach}}
          </select>
        {{/foreach}}

        <span class="type-switch font-size">
          <input type="text" name="_value_size" onchange="$V(this.form.elements.value, $V(this)+'px')" value="{{if $ex_field_property->type == "font-size"}}{{$ex_field_property->value|floatval}}{{else}}11{{/if}}" size="2" /> pixels
        </span>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$ex_field_property field=predicate_id}}</th>
      <td colspan="3">
        <input type="text" name="predicate_id_autocomplete_view" size="70" value="{{$ex_field_property->_ref_predicate->_view}}" />
        {{mb_field object=$ex_field_property field=predicate_id hidden=true}}
        {{*<button class="new notext" onclick="ExFieldPredicate.create('{{$ex_field_property->ex_class_field_id}}', null, this.form)" type="button">
          {{tr}}New{{/tr}}
        </button>*}}
      </td>
    </tr>
    
    <tr>
      <td></td>
      <td>
        <button type="submit" class="submit singleclick">{{tr}}Save{{/tr}}</button>
        {{if $ex_field_property->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'le style ',objName:'{{$ex_field_property->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>