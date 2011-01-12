{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{main}}
  ExClass.id = "{{$ex_class->_id}}";
{{/main}}

<table class="main form">
  {{mb_include module=system template=inc_form_table_header object=$ex_class colspan="2"}}

  <tr>
    <td colspan="2">
      <form name="editExClass" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_ex_class_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="callback" value="ExClass.edit" />
        {{mb_key object=$ex_class}}
        
        {{mb_field object=$ex_class field=host_class hidden=true}}
        {{mb_field object=$ex_class field=event hidden=true}}
        
        <table class="main form">
          <tr>
            <th>{{mb_label object=$ex_class field=event}}</th>
            <td>
              {{if !$ex_class->_id}}
              <select name="_event" class="notNull" onchange="ExClass.setEvent(this)">
                <option value=""> &ndash; Choisir </option>
                {{foreach from=$classes item=_events key=_class}}
                  <optgroup label="{{tr}}{{$_class}}{{/tr}}">
                    {{foreach from=$_events item=_params key=_event_name}}
                      <option value="{{$_class}}.{{$_event_name}}" {{if $_class == $ex_class->host_class && $_event_name == $ex_class->event}}selected="selected"{{/if}}>
                        {{tr}}{{$_class}}{{/tr}} - {{$_event_name}}
                        {{if array_key_exists("multiple", $_params) && $_params.multiple}}
                          (multiple)
                        {{/if}}
                      </option>
                    {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
              {{else}}
                {{tr}}{{$ex_class->host_class}}{{/tr}} - {{$ex_class->event}}
              {{/if}}
            </td>
            
            <th>{{mb_label object=$ex_class field=name}}</th>
            <td>{{mb_field object=$ex_class field=name}}</td>
            
            <td>
              {{if $ex_class->_id}}
                <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{ajax:true,typeName:'la classe étendue ',objName:'{{$ex_class->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

{{if $ex_class->_id}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("ExClass-back", true);
});
</script>

<ul class="control_tabs" id="ExClass-back">
  <li><a href="#fields">{{tr}}CExClass-back-fields{{/tr}}</a></li>
  <li><a href="#constraints">{{tr}}CExClass-back-constraints{{/tr}}</a></li>
  <li><a href="#fields-layout">{{tr}}CExClassField-layout{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<table class="main layout" id="fields" style="display: none;">
  <tr>
    <td style="width: 20em; padding-right: 5px;">
      <button type="button" class="new" style="float: right;" onclick="ExField.create({{$ex_class->_id}})">
        {{tr}}CExClassField-title-create{{/tr}}
      </button>

      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClassField field=name}}</th>
          {{*<th>{{mb_title class=CExClassField field=prop}}</th>*}}
        </tr>
        {{foreach from=$ex_class->_ref_fields item=_field}}
          <tr>
            <td title="{{$_field->name}}">
              <a href="#1" onclick="ExField.edit({{$_field->_id}})"><strong>
                {{if $_field->_locale}}
                  {{$_field->_locale}}
                {{else}}
                  [{{$_field->name}}]
                {{/if}}
              </strong></a>
            </td>
            {{*<td>{{$_field->prop}}</td>*}}
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2">{{tr}}CExClassField.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="exFieldEditor">
      <!-- exFieldEditor -->
    </td>
  </tr>
</table>

<table class="main layout" id="constraints" style="display: none;">
  <tr>
    <td style="width: 20em; padding-right: 5px;">
      <button type="button" class="new" style="float: right;" onclick="ExConstraint.create({{$ex_class->_id}})">
        {{tr}}CExClassConstraint-title-create{{/tr}}
      </button>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClassConstraint field=field}}</th>
          <th>{{mb_title class=CExClassConstraint field=operator}}</th>
          <th>{{mb_title class=CExClassConstraint field=value}}</th>
        </tr>
        {{foreach from=$ex_class->_ref_constraints item=_constraint}}
          <tr>
            <td>
              <a href="#1" onclick="ExConstraint.edit({{$_constraint->_id}})">
                <strong>
                  {{tr}}{{$_constraint->_ref_ex_class->host_class}}-{{$_constraint->field}}{{/tr}}
                </strong>
              </a>
            </td>
            <td>{{mb_value object=$_constraint field=operator}}</td>
            <td>{{mb_value object=$_constraint field=value}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="3">{{tr}}CExClassConstraint.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="exConstraintEditor">
      <!-- exConstraintEditor -->
    </td>
  </tr>
</table>

<div id="fields-layout" style="display: none;">
  <script type="text/javascript">
  	Main.add(function(){
		  $$('.draggable').each(function(d){
			  new Draggable(d, {
		      //revert: true, 
		      scroll: window, 
		      ghosting: true
		    });
			});
			
			$$(".droppable").each(function(d){
		    Droppables.add(d, { 
		      hoverclass: 'dropover',
					onDrop: function(drag, drop) {
					  drag.style.position = null;
						
						// prevent multiple fields in the same cell
					  if (drop.hasClassName('grid') && drop.select('.draggable').length) return;
						
            // prevent fields in label-list and vice versa
            if (
						  drop.hasClassName('label-list') && drag.hasClassName('field') ||
						  drop.hasClassName('field-list') && drag.hasClassName('label')
						) return;
						
					  drop.insert(drag);
						submitLayout(drag);
					}
		    });
			});
		});
		
		function submitLayout(drag) {
		  var form = getForm("form-layout-field"),
			    drop = drag.up(".droppable"),
          coord_x = drop.getAttribute("data-x"),
          coord_y = drop.getAttribute("data-y"),
					type = drag.hasClassName("field") ? "field" : "label";
		  
			$(form).select('input.coord').each(function(coord){
			  $V(coord.disable(), '');
			});
			
			$V(form.ex_class_field_id, drag.getAttribute("data-field_id"));
      $V(form["coord_"+type+"_x"].enable(), coord_x);
      $V(form["coord_"+type+"_y"].enable(), coord_y);
			
			form.onsubmit();
		}
  </script>

	<div class="small-info">Glissez-déposez les champs et leur libellé dans la grille "Disposition"</div>
	
	<form name="form-layout-field" method="post" action="" onsubmit="return onSubmitFormAjax(this)">
		<input type="hidden" name="m" value="system" />
    <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
    <input type="hidden" name="ex_class_field_id" value="" />
		
    <input type="hidden" name="coord_label_x" class="coord" value="" />
    <input type="hidden" name="coord_label_y" class="coord" value="" />
    <input type="hidden" name="coord_field_x" class="coord" value="" />
    <input type="hidden" name="coord_field_y" class="coord" value="" />
	</form>
	
	<style type="text/css">
		.out-of-grid div {
		  /*display: inline-block;*/
		}
		
		.label-list .draggable,
		.field-list .draggable {
		  border: 1px dotted #aaa;
			margin: 2px;
			padding: 2px;
		}
		
		.droppable.grid .draggable.label {
		  text-align: right;
			font-weight: bold;
			white-space: nowrap;
		}
		
		.field:hover .field-info,
		.field-list .field .field-info {
		  display: block !important;
			position: absolute;
			top: -1.4em;
			color: #333;
			background: #ddd;
			padding: 0px 1em;
		  -moz-border-radius-topleft: 4px;
		  -moz-border-radius-topright: 4px;
		  -webkit-border-top-left-radius: 4px;
		  -webkit-border-top-right-radius: 4px;
		  border-radius-topleft: 4px;
		  border-radius-topright: 4px;
		}
    
    .field-list .field {
		  margin-top: 1.2em;
    }
	</style>
	
	<form name="form-grid-layout" method="post" onsubmit="return false">
		
	<div class="out-of-grid">
		<table class="main tbl" style="table-layout: fixed;">
	    <tr>
	      <th colspan="2" class="title">Eléments non placés</th>
	    </tr>
		  <tr>
			  <th>Libellés</th>
        <th>Champs</th>
			</tr>
			<tr>
				<td class="label-list droppable" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
					{{foreach from=$out_of_grid.label item=_field}}
					  {{mb_include module=system template=inc_ex_field_draggable _type="label"}}
					{{/foreach}}
				</td>
		
		    <td class="field-list droppable" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
		      {{foreach from=$out_of_grid.field item=_field}}
            {{mb_include module=system template=inc_ex_field_draggable _type="field"}}
		      {{/foreach}}
				</td>
      </tr>
		</table>
	</div>
	
	<br />
	
	<table class="main tbl" style="border-collapse: collapse;">
	  <tr>
	  	<th colspan="10" class="title">Disposition</th>
	  </tr>
	  <tr>
	  	<th></th>
	    {{foreach from=$grid|@reset key=_x item=_field}}
	      <th>{{$_x}}</th>
	    {{/foreach}}	
	  </tr>
		
		{{foreach from=$grid key=_y item=_line}}
	  <tr>
	  	<th style="padding: 4px; width: 2em; text-align: right;">{{$_y}}</th>
	  	{{foreach from=$_line key=_x item=_group}}
			  <td style="border: 1px dotted #ddd; min-width: 2em;" class="droppable grid" data-x="{{$_x}}" data-y="{{$_y}}">
				  {{foreach from=$_group item=_field key=_type}}
					  {{if $_field}}
						  {{mb_include module=system template=inc_ex_field_draggable}}
					  {{/if}}
					{{/foreach}}
			  </td>
			{{/foreach}}
	  </tr>
		{{/foreach}}
	</table>
	
	</form>

</div>
{{/if}}