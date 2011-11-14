<div class="draggable {{$_type}}" data-type="{{$_type}}" data-field_id="{{$_field->_id}}" ondblclick="ExField.edit({{$_field->_id}})">
  <div style="position: relative;">
    {{if $_type == "field"}}
      <div class="field-info" style="display: none;">{{if $_field->_locale}}{{tr}}{{$_field->_locale}}{{/tr}}{{else}}{{$_field->name}}{{/if}}</div>
			<div class="field-content">
				{{mb_include module=forms template=inc_ex_object_field ex_field=$_field mode=layout form="form-grid-layout"}}
				
				{{* 
			  {{mb_field object=$ex_object field=$_field->name register=true increment=true form="form-grid-layout" rows=1}}
				 *}}
			</div>
    {{else}}
      {{mb_label object=$ex_object field=$_field->name}}
    {{/if}}
    <div class="overlay"></div>
    <div class="size" ondblclick="Event.stop(event)">
			<div class="row arrows">
				<input type="hidden" class="rowspan" value="1" />
	      <button class="up notext"   value="-1" onclick="ExClass.changeSpan(this)">Une ligne en moins</button>
	      <button class="down notext" value="1"  onclick="ExClass.changeSpan(this)">Une ligne en plus</button>
			</div>
			
      <div class="col arrows">
        <input type="hidden" class="colspan" value="1" />
        <button class="left notext"  value="-1" onclick="ExClass.changeSpan(this)">Une colonne en moins</button>
        <button class="right notext" value="1"  onclick="ExClass.changeSpan(this)">Une colonne en plus</button>
      </div>
    </div>
  </div>
</div>