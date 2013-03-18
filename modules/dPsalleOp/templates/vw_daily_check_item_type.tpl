<form name="edit-CDailyCheckItemType" action="?m=salleOp&amp;tab=vw_daily_check_item_type" method="post" onsubmit="return onSubmitFormAjax(this, Control.Modal.close)">
  <input type="hidden" name="dosql" value="do_daily_check_item_type_aed" />
  <input type="hidden" name="m" value="salleOp" />
  <input type="hidden" name="daily_check_item_type_id" value="{{$item_type->_id}}" />
  <input type="hidden" name="group_id" value="{{$g}}" />
  <table class="main form">
    {{mb_include module=system template=inc_form_table_header object=$item_type}}
    <tr>
      <th class="narrow">{{mb_label object=$item_type field="title"}}</th>
      <td>{{mb_field object=$item_type field="title"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$item_type field="desc"}}</th>
      <td>{{mb_field object=$item_type field="desc"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$item_type field="category_id"}}</th>
      <td>
        <select name="category_id" class="ref notNull">
          <option value=""></option>
          {{foreach from=$item_categories_by_class key=_class item=item_categories_by_target}}
            <optgroup label="{{tr}}CDailyCheckItemCategory.target_class.{{$_class}}{{/tr}}">
              {{foreach from=$item_categories_by_target key=_target item=_categories}}
                <option disabled style="background: #ccc;">
                  {{if $_target == "all"}}
                    {{tr}}All{{/tr}}
                  {{else}}
                    {{$targets.$_class.$_target}}
                  {{/if}}
                </option>

                {{foreach from=$_categories item=_cat}}
                  <option value="{{$_cat->_id}}" {{if $_cat->_id == $item_type->category_id}} selected="selected" {{/if}}>
                    &nbsp; |- {{$_cat->title}}
                  </option>
                {{/foreach}}
              {{/foreach}}
            </optgroup>
          {{/foreach}}
        </select>

        <button type="button" class="new notext" onclick="popupEditDailyCheckItemCategory()">
          {{tr}}New{{/tr}}
        </button>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$item_type field="index"}}</th>
      <td>{{mb_field object=$item_type field="index" form="edit-CDailyCheckItemType" increment=true size=1}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$item_type field="attribute"}}</th>
      <td>{{mb_field object=$item_type field="attribute"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$item_type field="active"}}</th>
      <td>{{mb_field object=$item_type field="active"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>

        {{if $item_type->_id}}
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$item_type->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>