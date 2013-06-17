<div class="out-of-grid droppable">
  <script type="text/javascript">
  Main.add(function(){
    Control.Tabs.create("class-message-layout-tabs-{{$_group_id}}");
  });
  </script>
  
  <ul class="control_tabs" id="class-message-layout-tabs-{{$_group_id}}">
    {{if !$ex_class->pixel_positionning}}
    <li>
      <a href="#outofgrid-class-fields-{{$_group_id}}">Champs</a>
    </li>
    {{/if}}
    <li>
      <a href="#outofgrid-messages-{{$_group_id}}">Textes / Messages</a>
    </li>
    <li>
      <a href="#outofgrid-hostfields-{{$_group_id}}">Champs de Mediboard</a>
    </li>
  </ul>
  <hr class="control_tabs" />
  
  <!-- Fields -->
  {{if !$ex_class->pixel_positionning}}
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
            {{if !$_field->disabled}}
              {{mb_include module=forms template=inc_ex_field_draggable _type="label"}}
            {{/if}}
          {{/foreach}}
        </td>
    
        <td class="field-list" data-x="" data-y="" style="padding: 4px; vertical-align: top;">
          {{foreach from=$out_of_grid.$_group_id.field item=_field}}
            {{if !$_field->disabled}}
              {{mb_include module=forms template=inc_ex_field_draggable _type="field"}}
            {{/if}}
          {{/foreach}}
        </td>
      </tr>
    </table>
  </div>
  {{/if}}
  
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
        <td style="width: 40%;">
          <select onchange="toggleList(this)" class="dont-lock">
            {{foreach from=$ex_class->_host_objects item=_object key=_class}}
              <option value="{{$_class}}">{{tr}}{{$_class}}{{/tr}}</option>
            {{/foreach}}
          </select>
        </td>
        <td>
          {{foreach from=$ex_class->_host_objects item=_object key=_class name=_host_objects}}
            <div style="overflow-y: scroll; min-height: 140px; max-height: 140px; {{if $smarty.foreach._host_objects.first}} display: inline-block; {{else}} display: none; {{/if}}"
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