{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !@$readonly}}

{{unique_id var=ex_form_hash}}
{{assign var=ex_form_hash value="ex_$ex_form_hash"}}

<script type="text/javascript">
ExObjectForms = window.ExObjectForms || {};

ExObjectForms.{{$ex_form_hash}} = {
  confirmSavePrint: function(form){
    var oldCallback = $V(form.callback);
    $V(form.callback, 'ExObjectForms.{{$ex_form_hash}}.printForm');

    (FormObserver.changes == 0 || confirm("Pour imprimer le formulaire, il est nécessaire de l'enregistrer, souhaitez-vous continuer ?")) &&
             form.onsubmit();

    $V(form.callback, oldCallback);

    return false;
  },

  closeOnSuccess: function(id, obj) {
    this.updateId(id, obj);

    if (!(obj._ui_messages[3] || obj._ui_messages[4])) { // warning ou error
      var element_id = "{{$_element_id}}";

      if (element_id && window.opener && !window.opener.closed && window.opener !== window && window.opener.ExObject) {
        if (element_id.charAt(0) == "@") {
          eval("window.opener."+element_id.substr(1)+"()"); // ARG
        }
        else {
          var target = window.opener.$(element_id);

          if (target.get("ex_class_id")) {
            window.opener.ExObject.loadExObjects.defer(
              target.get("reference_class"),
              target.get("reference_id"),
              element_id,
              target.get("detail"),
              target.get("ex_class_id")
            );
          }
          else {
            window.opener.ExObject.register.defer(element_id, {
              ex_class_id: "{{$ex_class_id}}",
              object_guid: "{{$object_guid}}",
              event_name: "{{$event_name}}",
              _element_id: element_id
            });
          }
        }
      }

      window.close();
    }
  },

  printForm: function(id, obj) {
    this.updateId(id, obj);

    FormObserver.changes = 0;
    var iFrame = $("printIframe");
    iFrame.src = "about:blank";
    iFrame.src = "?{{$smarty.server.QUERY_STRING|html_entity_decode}}&readonly=1&print=1&autoprint=1&ex_object_id="+id;
  },

  updateId: function(id, obj) {
    $V(getForm("editExObject_{{$ex_form_hash}}").ex_object_id, id);
    //(window.callback_{{$ex_class_id}} || window.launcher && window.launcher.callback_{{$ex_class_id}})();
  }
};

Main.add(function(){
  var form = getForm("editExObject_{{$ex_form_hash}}");

  ExObject.current = {object_guid: "{{$object_guid}}", event_name: "{{$event_name}}"};
  ExObject.pixelPositionning = {{$ex_object->_ref_ex_class->pixel_positionning}} == 1;
  new ExObjectFormula({{$formula_token_values|@json}}, form);
  ExObject.initPredicates({{$ex_object->_fields_default_properties|@json}}, {{$ex_object->_fields_display_struct|@json}}, form);
});
</script>

{{mb_form name="editExObject_$ex_form_hash" m="system" dosql="do_ex_object_aed" method="post" className="watched"
          onsubmit="return onSubmitFormAjax(this)"}}
  {{mb_key object=$ex_object}}
  {{mb_field object=$ex_object field=_ex_class_id hidden=true}}
  {{mb_field object=$ex_object field=group_id hidden=true}}

  {{mb_field object=$ex_object field=object_class hidden=true}}
  {{mb_field object=$ex_object field=object_id hidden=true}}

  {{mb_field object=$ex_object field=reference_class hidden=true}}
  {{mb_field object=$ex_object field=reference_id hidden=true}}

  {{mb_field object=$ex_object field=reference2_class hidden=true}}
  {{mb_field object=$ex_object field=reference2_id hidden=true}}

  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="ExObjectForms.{{$ex_form_hash}}.closeOnSuccess" />

  {{if !$print && !$preview_mode}}
    <iframe id="printIframe" width="0" height="0" style="display: none;"></iframe>
    <button type="button" class="print singleclick" onclick="ExObjectForms.{{$ex_form_hash}}.confirmSavePrint(this.form)" style="float: right;">
      {{tr}}Print{{/tr}}
    </button>
  {{/if}}

  {{if !$noheader}}
  <h2 style="font-weight: bold;">
    {{if $ex_object->_ref_reference_object_2 && $ex_object->_ref_reference_object_2->_id}}
      <span style="color: #006600;"
           onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_reference_object_2->_guid}}');">
        {{$ex_object->_ref_reference_object_2}}

        {{if $ex_object->_ref_reference_object_2 instanceof CPatient}}
          {{mb_include module=patients template=inc_vw_ipp ipp=$ex_object->_ref_reference_object_2->_IPP}}
        {{/if}}
      </span>
    {{else}}
      {{if $ex_object->_rel_patient}}
        {{assign var=_patient value=$ex_object->_rel_patient}}
        <span style="color: #006600;"
             onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}');">
          {{$_patient}}
          {{mb_include module=patients template=inc_vw_ipp ipp=$_patient->_IPP}}
        </span>
      {{/if}}
    {{/if}}

    {{if $ex_object->_ref_reference_object_1 && $ex_object->_ref_reference_object_1->_id}}
      &ndash;
      <span onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_reference_object_1->_guid}}');">
        {{$ex_object->_ref_reference_object_1}}
      </span>
    {{/if}}

    &ndash;
    <span style="color: #0000AA;" {{if $ex_object->_id}} onmouseover="ObjectTooltip.createEx(this, 'CExObject_{{$ex_object->_ex_class_id}}-{{$ex_object->_id}}', 'objectViewHistory')" {{/if}}>
      {{if $ex_object->_id}}
        <img src="images/icons/history.gif" width="16" height="16"/>
      {{else}}
        <img src="images/icons/new.png" width="16" height="16"/>
      {{/if}}
      {{$ex_object->_ref_last_log->_ref_user}}
    </span>

    <hr style="border-color: #333; margin: 4px 0;" />
    {{*<span style="float: right;">{{$ex_object->_ref_group}}</span>*}}

    {{$ex_object->_ref_ex_class->name}} - {{$object}}

    {{if $parent_view}}
      <span style="float: right; color: #666;">
        Formulaire parent: {{$parent_view|smarty:nodefaults}}
      </span>
    {{/if}}
  </h2>
  {{/if}}

  <script type="text/javascript">
    Main.add(function(){
      Control.Tabs.create("ex_class-groups-tabs-{{$ex_form_hash}}", false, {
        afterChange: function(container){
          if (Object.isFunction(ExObject.groupTabsCallback[container.id])) {
            ExObject.groupTabsCallback[container.id]();
          }
        }
      });

      if (window.parent != window || window.opener != window) {
        document.title = "{{$ex_object->_ref_ex_class->name}} - {{$object}}".htmlDecode();
      }
    });
  </script>

  {{$ui_msg|smarty:nodefaults}}

  <ul id="ex_class-groups-tabs-{{$ex_form_hash}}" class="control_tabs" style="clear: left;">
    {{foreach from=$groups item=_group}}
      {{if $_group->_ref_fields|@count}}
      <li>
        <a href="#tab-{{$_group->_guid}}">{{$_group}}</a>
      </li>
      {{/if}}
    {{/foreach}}

    {{foreach from=$ex_object->_native_views item=_object key=_name}}
      {{if $_object && $_object->_id || $preview_mode}}
        <li><a href="#tab-native_views-{{$_name}}" class="special">{{tr}}CExClass.native_views.{{$_name}}{{/tr}}</a></li>
      {{/if}}
    {{/foreach}}

    {{if $ex_object->_ref_ex_class->pixel_positionning}}
      <li style="padding-left: 3em;">
        <button class="modify singleclick" type="submit" {{if !$object->_id}}disabled{{/if}}>{{tr}}Save{{/tr}}</button>

        {{if $can_delete && $ex_object->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{callback: (function(){ FormObserver.changes = 0; onSubmitFormAjax(this.form); }).bind(this), typeName:'', objName:'{{$ex_object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </li>
    {{/if}}
  </ul>
  <hr class="control_tabs" />

  {{if $ex_object->_ref_ex_class->pixel_positionning}}
    {{mb_include module=forms template=inc_form_pixel_grid}}
  {{else}}
    {{mb_include module=forms template=inc_form_grid}}
  {{/if}}

{{/mb_form}}

{{foreach from=$ex_object->_native_views item=_object key=_name}}
  <div id="tab-native_views-{{$_name}}" style="display: none;">
    {{if $preview_mode}}
      <div class="small-info">
        Ici apparaitra la vue <strong>{{tr}}CExClass.native_views.{{$_name}}{{/tr}}</strong>.
      </div>
    {{else}}
      {{mb_include module=forms template="inc_native_view_$_name" object=$_object}}
    {{/if}}
  </div>
{{/foreach}}

{{else}}

{{* ----   READONLY   ---- *}}

<script type="text/javascript">
Main.add(function(){
  document.title = "{{$ex_object->_ref_ex_class->name}} - {{$object}}".htmlDecode();

  {{if $autoprint}}
    if (document.execCommand) {
      window.focus();
      document.execCommand('print', false, null);
    }
    else {
      window.print();
    }
  {{/if}}
});

function switchMode(){
  var only_filled = Url.parse().query.toQueryParams().only_filled;
  location.href = location.href.replace('only_filled='+only_filled, 'only_filled='+(only_filled == 1 ? 0 : 1));
}
</script>

{{if $print}}
  <div style="float: right;" class="not-printable">
    <button class="change" onclick="switchMode()">
      Tous les champs
    </button>
    <button class="print singleclick" onclick="window.print()">{{tr}}Print{{/tr}}</button>
  </div>
{{/if}}

<table class="main {{if $print}} print {{else}} form {{/if}}">
  {{if !$noheader}}
  <thead>
    <tr>
      <td colspan="4">
        <p style="font-weight: bold; font-size: 1.1em;">
          {{*<span style="float: right;">{{$ex_object->_ref_group}}</span>*}}

          {{if $ex_object->_ref_reference_object_2 && $ex_object->_ref_reference_object_2->_id}}
            <span style="color: #006600;">
              {{$ex_object->_ref_reference_object_2}}
              {{if $ex_object->_ref_reference_object_2 instanceof CPatient}}
                {{mb_include module=patients template=inc_vw_ipp ipp=$ex_object->_ref_reference_object_2->_IPP}}
              {{/if}}
            </span>
          {{else}}
            {{if $ex_object->_rel_patient}}
              {{assign var=_patient value=$ex_object->_rel_patient}}
              <span style="color: #006600;">
                {{$_patient}}
                {{mb_include module=patients template=inc_vw_ipp ipp=$_patient->_IPP}}
              </span>
            {{/if}}
          {{/if}}

          {{if $ex_object->_ref_reference_object_1 && $ex_object->_ref_reference_object_1->_id}}
            &ndash;
            <span>
              {{$ex_object->_ref_reference_object_1}}
            </span>
          {{/if}}

          &ndash;
          <span style="color: #0000AA;" {{if $ex_object->_id}} onmouseover="ObjectTooltip.createEx(this, 'CExObject_{{$ex_object->_ex_class_id}}-{{$ex_object->_id}}', 'objectViewHistory')" {{/if}}>
            {{if $ex_object->_id}}
              <img src="images/icons/history.gif" width="16" height="16"/>
            {{else}}
              <img src="images/icons/new.png" width="16" height="16"/>
            {{/if}}
            {{$ex_object->_ref_last_log->_ref_user}}
          </span>

          <br />
          {{$ex_object->_ref_ex_class->name}} - {{$object}}
        </p>
        <hr style="border-color: #333; margin: 4px 0;" />
      </td>
    </tr>
  </thead>
  {{/if}}

  {{if $only_filled}}

    <tr>
      <td colspan="4">
        {{mb_include module=forms template=inc_vw_ex_object ex_object=$ex_object}}

        {{foreach from=$ex_object->_native_views item=_object key=_name}}
          {{if $_object && $_object->_id}}
            <h4 style="margin: 0.5em; border-bottom: 1px solid #666;">{{tr}}CExClass.native_views.{{$_name}}{{/tr}}</h4>
            {{mb_include module=forms template="inc_native_view_`$_name`_print" object=$_object}}
          {{/if}}
        {{/foreach}}
      </td>
    </tr>

  {{else}}

    {{foreach from=$grid key=_group_id item=_grid}}

    {{if $groups.$_group_id->_ref_fields|@count}}
    <tbody id="tab-{{$groups.$_group_id->_guid}}">
      <tr>
        <th class="title" colspan="4">{{$groups.$_group_id}}</th>
      </tr>

    {{foreach from=$_grid key=_y item=_line}}
    <tr>
      {{foreach from=$_line key=_x item=_group name=_x}}
        {{if $_group.object}}
          {{if $_group.object instanceof CExClassField}}
            {{assign var=_field value=$_group.object}}
            {{assign var=_field_name value=$_field->name}}

            {{if $_group.type == "label"}}
              {{if $_field->coord_field_x == $_field->coord_label_x+1}}
                <th style="font-weight: bold; vertical-align: middle; white-space: normal;">
                  {{mb_label object=$ex_object field=$_field_name}}
                </th>
              {{else}}
                <td style="font-weight: bold; text-align: left;">
                  {{mb_label object=$ex_object field=$_field_name}}
                </td>
              {{/if}}
            {{elseif $_group.type == "field"}}
              <td>
                <div {{if $ex_object->_specs.$_field_name instanceof CTextSpec}} style="text-block" {{/if}}>
                  {{$_field->prefix}}
                  {{mb_value object=$ex_object field=$_field_name}}
                  {{$_field->suffix}}
                </div>
              </td>
            {{/if}}
          {{elseif $_group.object instanceof CExClassHostField}}
            {{assign var=_host_field value=$_group.object}}
            {{if $_group.type == "label"}}
              {{assign var=_next_col value=$smarty.foreach._x.iteration}}
              {{assign var=_next value=null}}

              {{if array_key_exists($_next_col,$_line)}}
                {{assign var=_tmp_next value=$_line.$_next_col}}

                {{if $_tmp_next.object instanceof CExClassHostField}}
                  {{assign var=_next value=$_line.$_next_col.object}}
                {{/if}}
              {{/if}}

              {{if $_next && $_next->host_class == $_host_field->host_class && $_next->field == $_host_field->field}}
                <th style="font-weight: bold; vertical-align: top; white-space: normal;">
                  {{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}
                </th>
              {{else}}
                <td style="font-weight: bold; text-align: left; white-space: normal;">
                  {{mb_label object=$_host_field->_ref_host_object field=$_host_field->field}}
                </td>
              {{/if}}
            {{else}}
              <td>
                {{mb_value object=$_host_field->_ref_host_object field=$_host_field->field}}
              </td>
            {{/if}}
          {{else}}
            {{assign var=_message value=$_group.object}}
              {{if $_group.type == "message_title"}}

                {{if $_message->coord_text_x == $_message->coord_title_x+1}}
                  <th style="font-weight: bold; vertical-align: middle; white-space: normal;">
                    {{$_message->title}}
                  </th>
                {{else}}
                  <td style="font-weight: bold; text-align: left;">
                    {{$_message->title}}
                  </td>
                {{/if}}
              {{else}}
                <td>
                  {{mb_include module=forms template=inc_ex_message}}
                </td>
              {{/if}}
          {{/if}}
        {{else}}
          <td></td>
        {{/if}}
      {{/foreach}}
    </tr>
    {{/foreach}}

    {{* Out of grid *}}
    {{foreach from=$groups.$_group_id->_ref_fields item=_field}}
      {{assign var=_field_name value=$_field->name}}

      {{if isset($out_of_grid.$_group_id.field.$_field_name|smarty:nodefaults)}}
        <tr>
          <th style="font-weight: bold; width: 50%; vertical-align: middle; white-space: normal;" colspan="2">
            {{mb_label object=$ex_object field=$_field_name}}
          </th>
          <td colspan="2">
            <div {{if $ex_object->_specs.$_field_name instanceof CTextSpec}} class="text-block" {{/if}}>
              {{$_field->prefix}}
              {{mb_value object=$ex_object field=$_field_name}}
              {{$_field->suffix}}
            </div>
          </td>
        </tr>
      {{/if}}
    {{/foreach}}

    </tbody>
    {{/if}}
    {{/foreach}}

    {{foreach from=$ex_object->_native_views item=_object key=_name}}
      {{if $_object && $_object->_id}}
      <tbody>
        <tr>
          <th class="title" colspan="4">
            {{tr}}CExClass.native_views.{{$_name}}{{/tr}}
          </th>
        </tr>
        <tr>
          <td colspan="4">{{mb_include module=forms template="inc_native_view_`$_name`_print" object=$_object}}</td>
        </tr>
      </tbody>
      {{/if}}
    {{/foreach}}
  {{/if}}

</table>


{{/if}}