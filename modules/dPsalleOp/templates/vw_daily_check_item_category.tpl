<script>
updateTarget = function(select) {
  var form = select.form;
  var parts = $V(select).split(/-/);
  $V(form.target_class, parts[0]);
  $V(form.target_id,    (parts[1] === "none" ? "" : parts[1]));
}

Main.add(Control.Tabs.create.curry("item_categories_tabs", true));
</script>

<table class="main layout">
  <tr>
    <td style="width: 50%;">
      <ul class="control_tabs" id="item_categories_tabs">
      {{foreach from=$item_categories_by_class key=_class item=_categories}}
        <li>
          <a href="#tab-{{$_class}}">
            {{tr}}CDailyCheckItemCategory.target_class.{{$_class}}{{/tr}}
          </a>
        </li>
      {{/foreach}}
      </ul>

      {{foreach from=$item_categories_by_class key=_class item=_categories_by_target}}
        <div id="tab-{{$_class}}" style="display: none;">
          <a class="button new" href="?m=salleOp&amp;a=vw_daily_check_item_category&amp;item_category_id=0&amp;target_class={{$_class}}&amp;dialog={{$dialog}}">
            {{tr}}CDailyCheckItemCategory-title-create{{/tr}}
          </a>

          <table class="main tbl">
            <tr>
              <th>{{mb_title class=CDailyCheckItemCategory field=title}}</th>
              <th>{{mb_title class=CDailyCheckItemCategory field=type}}</th>
              <th>{{mb_title class=CDailyCheckItemCategory field=desc}}</th>
            </tr>

            {{foreach from=$_categories_by_target key=_target item=_categories}}
              <tr>
                <th class="title" colspan="4">
                  {{if $_target == "all"}}
                    {{tr}}All{{/tr}}
                  {{else}}
                    <span data-object_guid="{{$_class}}-{{$_target}}">
                      {{$targets.$_class.$_target}}
                    </span>
                  {{/if}}
                </th>
              </tr>
              {{foreach from=$_categories item=_item}}
              <tr {{if $_item->_id == $item_category->_id}} class="selected" {{/if}}>
                <td>
                  <a href="?m=salleOp&amp;a=vw_daily_check_item_category&amp;item_category_id={{$_item->_id}}&amp;dialog={{$dialog}}">
                    {{mb_value object=$_item field=title}}
                  </a>
                </td>
                <td>{{mb_value object=$_item field=type}}</td>
                <td>{{mb_value object=$_item field=desc}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="4" class="empty">
                  {{tr}}CDailyCheckItemCategory.none{{/tr}}
                </td>
              </tr>
              {{/foreach}}
            {{/foreach}}
          </table>
        </div>
      {{/foreach}}
    </td>

    <td>
      <form name="edit-CDailyCheckItemCategory" action="?m={{$m}}&amp;a=vw_daily_check_item_category&amp;dialog={{$dialog}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_daily_check_item_category_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="daily_check_item_category_id" value="{{$item_category->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$item_category field="target_class" hidden=true}}
        {{mb_field object=$item_category field="target_id" hidden=true}}

        <table class="main form">
          {{mb_include module=system template=inc_form_table_header object=$item_category}}
          <tr>
            <th class="narrow">{{mb_label object=$item_category field="title"}}</th>
            <td>{{mb_field object=$item_category field="title"}}</td>
          </tr>
          <tr>
            <tr>
              <th>{{mb_label object=$item_category field="_target_guid"}}</th>
              <td>
                <select name="_target_guid" class="str notNull" onchange="updateTarget(this)">
                  <option value=""> &ndash; Salle </option>
                  {{foreach from=$targets key=_class item=_targets}}
                     <optgroup label="{{tr}}CDailyCheckItemCategory.target_class.{{$_class}}{{/tr}}">
                       {{foreach from=$_targets key=_id item=_target}}
                         <option value="{{$_target->_guid}}"
                                 {{if $_target->_id == $item_category->target_id && $_target->_class == $item_category->target_class}} selected {{/if}}>
                           {{if $_id == 0}}
                             {{tr}}CDailyCheckItemCategory.target_class.{{$_class}}{{/tr}} - {{tr}}All{{/tr}}
                           {{else}}
                             {{$_target}}
                           {{/if}}
                         </option>
                       {{/foreach}}
                     </optgroup>
                  {{/foreach}}
                </select>
              </td>
            </tr>
          </tr>
          <tr>
            <th>{{mb_label object=$item_category field="desc"}}</th>
            <td>{{mb_field object=$item_category field="desc"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>

              {{if $item_category->_id}}
                <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$item_category->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>