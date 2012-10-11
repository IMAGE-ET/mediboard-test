
<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("field_groups_layout");
  //ExClass.putCellSpans($$(".drop-grid")[0]);
});

toggleList = function(select) {
  $$(".hostfield-list").invoke("hide");
  $$(".hostfield-"+$V(select))[0].show();
}
</script>

<form name="form-layout-field" method="post" action="" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_field_aed" />
  <input type="hidden" name="ex_class_field_id" value="" />
  
  <input type="hidden" name="coord_label_x" class="coord" value="" />
  <input type="hidden" name="coord_label_y" class="coord" value="" />
  <input type="hidden" name="coord_field_x" class="coord" value="" />
  <input type="hidden" name="coord_field_y" class="coord" value="" />
</form>

<form name="form-layout-message" method="post" action="" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_message_aed" />
  <input type="hidden" name="ex_class_message_id" value="" />
  
  <input type="hidden" name="coord_title_x" class="coord" value="" />
  <input type="hidden" name="coord_title_y" class="coord" value="" />
  <input type="hidden" name="coord_text_x" class="coord" value="" />
  <input type="hidden" name="coord_text_y" class="coord" value="" />
</form>

<form name="form-layout-hostfield" method="post" action="" onsubmit="return false">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_ex_class_host_field_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="ex_class_host_field_id" value="" />
  <input type="hidden" name="ex_class_id" value="{{$ex_class->_id}}" />
  <input type="hidden" name="ex_group_id" value="" />
  <input type="hidden" name="host_class" value="" />
  <input type="hidden" name="field" value="" />
  <input type="hidden" name="callback" value="" />
  
  <input type="hidden" name="coord_label_x" class="coord" value="" />
  <input type="hidden" name="coord_label_y" class="coord" value="" />
  <input type="hidden" name="coord_value_x" class="coord" value="" />
  <input type="hidden" name="coord_value_y" class="coord" value="" />
</form>

<ul class="control_tabs" id="field_groups_layout" style="font-size: 0.9em;">
  {{foreach from=$ex_class->_ref_groups item=_group}}
    <li>
      <a href="#group-layout-{{$_group->_guid}}" style="padding: 2px 4px;">
        {{$_group->name}} <small>({{$_group->_ref_fields|@count}})</small>
      </a>
    </li>
  {{/foreach}}
  <li style="font-size: 1.2em; font-weight: bold;">
    <label title="Plutôt que glisser-déposer">
      <input type="checkbox" onclick="ExClass.setPickMode(this.checked)" checked="checked" />
      Disposer par clic
    </label>
  </li>
</ul>
<hr class="control_tabs" />

{{assign var=groups value=$ex_class->_ref_groups}}

<form name="form-grid-layout" method="post" onsubmit="return false" class="prepared pickmode">
  
{{foreach from=$grid key=_group_id item=_grid}}

<div id="group-layout-{{$groups.$_group_id->_guid}}" style="display: none;" class="group-layout">
  
<div class="out-of-grid droppable">
  <script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create("class-message-layout-tabs-{{$_group_id}}");
  });
  </script>
  
  <ul class="control_tabs" id="class-message-layout-tabs-{{$_group_id}}">
    <li>
      <a href="#outofgrid-class-fields-{{$_group_id}}">Champs</a>
    </li>
    <li>
      <a href="#outofgrid-messages-{{$_group_id}}">Textes / Messages</a>
    </li>
    <li>
      <a href="#outofgrid-hostfields-{{$_group_id}}">Champs de Mediboard</a>
    </li>
  </ul>
  <hr class="control_tabs" />
  
  <!-- Fields -->
  <div id="outofgrid-class-fields-{{$_group_id}}" style="display: none;">
    <table class="main tbl" style="table-layout: fixed;">
      <tr>
        <th>Libellés</th>
        <th>Valeurs</th>
      </tr>
    </table>
    
    <table class="main layout" style="table-layout: fixed;">
      <tr>
        <td class="label-list" data-x="" data-y="" style="padding: 4px; height: 2em; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.label item=_field}}
            {{mb_include module=forms template=inc_ex_field_draggable _type="label" }}
          {{/foreach}}
        </td>
    
        <td class="field-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.field item=_field}}
            {{mb_include module=forms template=inc_ex_field_draggable _type="field"}}
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
  
  <!-- Messages -->
  <div id="outofgrid-messages-{{$_group_id}}" style="display: none;">
    <table class="main tbl" style="table-layout: fixed;">
      <tr>
        <th>Titres des messages (pas nécessaire de les placer)</th>
        <th>Messages</th>
      </tr>
    </table>
    
    <table class="main layout" style="table-layout: fixed;">
      <tr>
        <td class="message_title-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.message_title item=_field}}
            {{mb_include module=forms template=inc_ex_message_draggable _type="message_title"}}
          {{/foreach}}
        </td>
        <td class="message_text-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.message_text item=_field}}
            {{mb_include module=forms template=inc_ex_message_draggable _type="message_text"}}
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
  
  <!-- Host fields -->
  <div id="outofgrid-hostfields-{{$_group_id}}" style="display: none;">
    <table class="main layout">
      <tr>
        <td class="narrow">
          <select onchange="toggleList(this)" class="dont-lock">
            {{foreach from=$ex_class->_host_objects item=_object key=_class}}
              <option value="{{$_class}}">{{tr}}{{$_class}}{{/tr}}</option>
            {{/foreach}}
          </select>
        </td>
        <td>
          {{foreach from=$ex_class->_host_objects item=_object key=_class name=_host_objects}}
            <div style="overflow-y: scroll; min-height: 140px; max-height: 140px; {{if !$smarty.foreach._host_objects.first}} display: none; {{/if}}" 
                 class="hostfield-{{$_class}} hostfield-list" data-x="" data-y="">
              <ul>
              {{foreach from=$_object->_specs item=_spec key=_field}}
                {{if $_spec->show == 1 || $_field == "_view" || ($_spec->show == "" && $_field.0 !== "_")}}
                  <li>
                    {{mb_include module=forms template=inc_ex_host_field_draggable ex_group_id=$_group_id host_object=$_object}}
                  </li>
                {{/if}}
              {{/foreach}}
              </ul>
            </div>
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
</div>

<table class="main drop-grid" style="border-collapse: collapse;">
  <tr>
    <th colspan="5" class="title">Disposition</th>
  </tr>
  <tr>
    <th style="background: #ddd;"></th>
    {{foreach from=$_grid|@reset key=_x item=_field}}
      <th style="background: #ddd;">{{$_x}}</th>
    {{/foreach}}  
  </tr>
  
  {{foreach from=$_grid key=_y item=_line}}
  <tr>
    <th style="padding: 4px; width: 2em; text-align: right; background: #ddd;">{{$_y}}</th>
    {{foreach from=$_line key=_x item=_group}}
      <td style="border: 1px dotted #aaa; min-width: 2em; padding: 0; vertical-align: middle;" class="cell">
      
        {{*
        <div style="position: relative;" class="cell-layout-wrapper">
          <table class="layout cell-layout">
            <tr>
              <td></td>
              <td><a href="#">&#x25B2;</a><br /><a href="#">&#x25BC;</a></td>
              <td></td>
            </tr>
            <tr class="middle">
              <td><a href="#">&#x25C4;</a>&#x2005;<a href="#">&#x25BA;</a></td>
              <td></td>
              <td><a href="#">&#x25C4;</a>&#x2005;<a href="#">&#x25BA;</a></td>
            </tr>
            <tr>
              <td></td>
              <td><a href="#">&#x25B2;<br /><a href="#">&#x25BC;</a></td>
              <td></td>
            </tr>
          </table>
        </div>
        *}}
      
        <div class="droppable grid" data-x="{{$_x}}" data-y="{{$_y}}">
          {{if $_group.object}}
            {{if $_group.object instanceof CExClassField}}
              {{mb_include module=forms template=inc_ex_field_draggable 
                           _field=$_group.object 
                           _type=$_group.type}}
            {{elseif $_group.object instanceof CExClassHostField}}
              {{assign var=_host_field value=$_group.object}}
              {{assign var=_host_class value=$_host_field->host_class}}
              {{assign var=_host_object value=$ex_class->_host_objects.$_host_class}}
            
              {{mb_include module=forms template=inc_ex_host_field_draggable 
                           _host_field=$_group.object 
                           ex_group_id=$_group_id 
                           _field=$_group.object->field 
                           _type=$_group.type
                           host_object=$_host_object}}
            {{else}}
              {{mb_include module=forms template=inc_ex_message_draggable 
                           _field=$_group.object 
                           ex_group_id=$_group_id 
                           _type=$_group.type}}
            {{/if}}
          {{else}}
            &nbsp;
          {{/if}}
        </div>
      </td>
    {{/foreach}}
  </tr>
  {{/foreach}}
</table>

</div>

{{/foreach}}

</form>
