<script type="text/javascript">
enableFormElements = function(element) {
  var inputs = element.select("input,select,textarea,button:not(.keepEnable)");
  inputs.invoke("enable");
  return element.show();
}

disableFormElements = function(element) {
  var inputs = element.select("input,select,textarea,button:not(.keepEnable)");
  inputs.invoke("disable");
  return element.hide();
}

toggleCustomValue = function(button, b) {
  var customValue = button.up('div.custom-value');
  var inheritValue = button.up('td');

  if (b) {
    enableFormElements(customValue);
    inheritValue.down('input.inherit-value').disable();
    customValue.down("button.edit").hide();
    customValue.down("button.cancel").show();
  }
  else {
    disableFormElements(customValue).show();
    inheritValue.down('input.inherit-value').enable();
    customValue.down("button.edit").show();
    customValue.down("button.cancel").hide();
  }
}
</script>

<form name="edit-configuration" method="post" action="?" onsubmit="return onSubmitFormAjax(this, editObjectConfig.curry('{{$object_guid}}'))">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configuration_aed" />
  <input type="hidden" name="object_guid" value="{{$object_guid}}" />

  <table class="main tbl" style="height: calc(100% - 2.5em);">
    {{assign var=cols value=$ancestor_configs|@count}}

    {{foreach from=$ancestor_configs item=_ancestor}}
      <col style="width: {{math equation='100/x' x=$cols}}%" />
    {{/foreach}}

    {{assign var=level_0 value=null}}
    {{assign var=level_1 value=null}}
    {{assign var=level_2 value=null}}
    {{assign var=level_3 value=null}}

    {{foreach from=$configs key=_feature item=_prop}}
      {{assign var=space value=" "}}
      {{assign var=sections value=$space|explode:$_feature}}

      {{if $sections.0 != $level_0}}
        <tr>
          <th></th>
          {{foreach from=$ancestor_configs item=_ancestor name=ancestor}}
            {{if $_ancestor.object != "default"}}
              <th>
                {{if !$smarty.foreach.ancestor.last}} {{*  && $object_guid != $_ancestor.object->_guid *}}
                  {{if $_ancestor.object != "global" || $app->_ref_user->isAdmin()}}
                    <button type="button" class="edit notext" onclick="$V($('object_guid-selector'), '{{if $_ancestor.object instanceof CMbObject}}{{$_ancestor.object->_guid}}{{else}}{{$_ancestor.object}}{{/if}}')"></button>
                  {{/if}}
                {{/if}}
                {{if $_ancestor.object == "global"}}
                  {{tr}}config-inherit-{{$_ancestor.object}}{{/tr}}
                {{else}}
                  {{$_ancestor.object}}
                {{/if}}
              </th>
            {{/if}}
          {{/foreach}}
        </tr>
        <tr>
          <th class="title" colspan="{{$cols}}">{{tr}}module-{{$sections.0}}-court{{/tr}}</th>
        </tr>
        {{assign var=level_0 value=$sections.0}}
      {{/if}}

      {{if $sections.1 != $level_1}}
        <tr>
          <th class="category" colspan="{{$cols}}">{{tr}}{{$sections.1}}{{/tr}}</th>
        </tr>
        {{assign var=level_1 value=$sections.1}}
      {{/if}}

      {{if $sections.2 != $level_2 && $sections|@count > 3}}
        <tr>
          <th class="section" colspan="{{$cols}}">{{tr}}{{$sections.2}}{{/tr}}</th>
        </tr>
        {{assign var=level_2 value=$sections.2}}
      {{/if}}

      <tr>
        <td style="font-weight: bold; vertical-align: top;">
          <label title="{{tr}}config-{{$_feature|replace:' ':'-'}}-desc{{/tr}}">
            {{tr}}config-{{$_feature|replace:' ':'-'}}{{/tr}}
          </label>
        </td>

        {{assign var=prev_value value=null}}

        {{foreach from=$ancestor_configs item=_ancestor name=ancestor}}
          {{assign var=value value=$_ancestor.config_parent.$_feature}}
          {{assign var=is_inherited value=true}}

          {{if array_key_exists($_feature, $_ancestor.config)}}
            {{assign var=value value=$_ancestor.config.$_feature}}
            {{assign var=is_inherited value=false}}
          {{/if}}

          {{if $is_inherited}}
            {{assign var=value value=$prev_value}}
          {{/if}}

          {{if $_ancestor.object != "default"}}
            <td class="text" style="vertical-align: top;">
              {{if $smarty.foreach.ancestor.last}}
                <input type="hidden" name="c[{{$_feature}}]" value="{{'CConfiguration'|const:INHERIT}}" {{if !$is_inherited}} disabled {{/if}} class="inherit-value" />
              {{/if}}

              <div class="custom-value {{if !$smarty.foreach.ancestor.last && $is_inherited}}opacity-30{{/if}}">
                {{if $smarty.foreach.ancestor.last}}
                  <button type="button" class="edit notext compact keepEnable" onclick="toggleCustomValue(this, true)" {{if !$is_inherited}} style="display: none;" {{/if}}></button>
                  <button type="button" class="cancel notext compact keepEnable" onclick="toggleCustomValue(this, false)" {{if $is_inherited}} style="display: none;" {{/if}}></button>
                {{/if}}
                {{assign var=tpl value='config/inc_spec_'|cat:$_prop.type}}
                {{mb_include module=system template=$tpl is_last=$smarty.foreach.ancestor.last}}
              </div>
            </td>
          {{/if}}

          {{assign var=prev_value value=$value}}
        {{/foreach}}
      </tr>
    {{/foreach}}
      {{*<tr>
        <td></td>
        {{foreach from=$ancestor_configs item=_ancestor name=ancestor}}
          {{if $_ancestor.object != "default"}}
            <td>
              {{if $smarty.foreach.ancestor.last}}
                <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
              {{/if}}
            </td>
          {{/if}}
      {{/foreach}}
      </tr>*}}
  </table>
  <div style="position: fixed; bottom: 0px; width: 100%; height: 2.5em; background-color: #ffffff; text-align: center">
    <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
  </div>
</form>
