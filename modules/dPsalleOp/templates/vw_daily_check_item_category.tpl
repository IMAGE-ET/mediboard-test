<form name="edit-CDailyCheckItemCategory" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_daily_check_item_category_aed" />
  <input type="hidden" name="m" value="salleOp" />
  <input type="hidden" name="daily_check_item_category_id" value="{{$item_category->_id}}" />
  {{mb_field object=$item_category field="target_class" hidden=true}}
  {{mb_field object=$item_category field="target_id"    hidden=true}}
  {{mb_field object=$item_category field="list_type_id" hidden=true}}
  <input type="hidden" name="callback" value="CheckList.callbackItemCategory" />

  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$item_category}}
    <tr>
      <th>{{mb_label object=$item_category field="list_type_id"}}</th>
      <td>{{mb_value object=$item_category field="list_type_id" tooltip=true}}</td>
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$item_category field="title"}}</th>
      <td>{{mb_field object=$item_category field="title"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$item_category field="desc"}}</th>
      <td>{{mb_field object=$item_category field="desc"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>

        {{if $item_category->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(
            this.form,
            {ajax: true, typeName:'',objName:'{{$item_category->_view|smarty:nodefaults|JSAttribute}}'},
            {onComplete: Control.Modal.close}
            )">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $item_category->_id}}
  <table class="main tbl">
    <tr>
      <th colspan="5" class="title">Questions</th>
    </tr>

    <tr>
      <th>{{mb_title class=CDailyCheckItemType field=index}}</th>
      <th>{{mb_title class=CDailyCheckItemType field=title}}</th>
      <th>{{mb_title class=CDailyCheckItemType field=desc}}</th>
      <th>{{mb_title class=CDailyCheckItemType field=attribute}}</th>
      <th>{{mb_title class=CDailyCheckItemType field=active}}</th>
    </tr>

    {{foreach from=$item_category->_back.item_types item=_item}}
      <tr>
        <td class="narrow">{{mb_value object=$_item field=index}}</td>
        <td class="text">
          <a href="#1" onclick="CheckList.editItemType('{{$item_category->_id}}', '{{$_item->_id}}')">
            {{mb_value object=$_item field=title}}
          </a>
        </td>
        <td class="compact">{{mb_value object=$_item field=desc}}</td>
        <td class="text">{{mb_value object=$_item field=attribute}}</td>
        <td>{{mb_value object=$_item field=active}}</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="5" class="empty">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
      </tr>
    {{/foreach}}
    <tr>
      <td colspan="5">
        <button class="new compact" onclick="CheckList.editItemType('{{$item_category->_id}}', 0)" >
          {{tr}}CDailyCheckItemType-title-create{{/tr}}
        </button>
      </td>
    </tr>
  </table>
{{/if}}