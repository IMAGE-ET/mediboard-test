<tr>
  <th class="narrow">{{tr}}Choose{{/tr}}</th>
  <td colspan="2">
    {{assign var=field value=uf_`$context`_id}}
    {{assign var=value value=$affectation->$field}}

    <input type="hidden" name="{{$field}}" value="{{$value}}"/>
    {{assign var=found_checked value=0}}
    {{foreach from=$ufs item=_uf}}
    <input type="radio" name="{{$field}}_radio_view" value="{{$_uf->_id}}"
      {{if $value == $_uf->_id}}
        checked="checked"
        {{assign var=found_checked value=1}}
      {{/if}}
      onclick="$V(this.form.{{$field}}, this.value); $V(this.form.{{$field}}_view, '')"> 
    {{$_uf}}
  {{/foreach}}
  
  <div>
    Autre :
    {{assign var=ref_uf     value=_ref_uf_`$context`}}
    {{assign var=unfound_uf value=$affectation->$ref_uf}}
    <input type="text" class="autocomplete" name="{{$field}}_view"
      {{if !$found_checked}}value="{{$unfound_uf->_view}}{{/if}}"/>
  </div>
  
  
  <script type="text/javascript">
    Main.add(function() {
      var form = getForm("affect_uf");
      var url = new Url("system", "httpreq_field_autocomplete");
      url.addParam("class", "CUniteFonctionnelle");
      url.addParam("field", "libelle");
      url.addParam("limit", 30);
      url.addParam("view_field", "code");
      url.addParam("show_view", true);
      url.addParam("input_field", "{{$field}}_view");
      url.addParam("wholeString", false);
      url.autoComplete(form.{{$field}}_view, null, {
        minChars: 1,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field, selected) {
          var form = field.form;
          $V(form.{{$field}}, selected.getAttribute("id").split("-")[2]);
          if (form.{{$field}}_radio_view.length) {
            $A(form.{{$field}}_radio_view).each(function(elt) {
              elt.checked = "";
            });
          }
          else {
            form.{{$field}}_radio_view.checked = "";
          }
        } });
      } );
  </script>
</td>
</tr>
