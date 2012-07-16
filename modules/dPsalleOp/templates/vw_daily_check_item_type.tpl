<script type="text/javascript">
  function popupEditDailyCheckItemCategory() {
    var url = new Url('dPsalleOp', 'vw_daily_check_item_category');
    url.popup(800, 500, '{{tr}}CDailyCheckItemCategory{{/tr}}');
  }
  
  Main.add(function(){
    Control.Tabs.create("target_tabs", true);
  });
</script>

{{assign var=targets value=$item_category->_specs.target_class}}

<table class="main">
  <tr>
    <td>
      <ul id="target_tabs" class="control_tabs">
        {{foreach from=$target_class_list item=_target}}
          <li><a href="#tab-{{$_target}}">{{tr}}CDailyCheckItemCategory.target_class.{{$_target}}{{/tr}}</a></li>
        {{/foreach}}
      </ul>
      <hr class="control_tabs" />
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CDailyCheckItemType field=title}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=desc}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=attribute}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=active}}</th>
        </tr>
        {{foreach from=$target_class_list item=_target}}
          <tbody id="tab-{{$_target}}" style="display: none;">
          {{foreach from=$item_categories_list.$_target item=_cat}}
            {{if $_cat->_back.item_types|@count}}
              <tr>
                <td colspan="10">
                  <strong>{{$_cat->title}}</strong>
                  {{if $_cat->desc}}
                    &ndash; <small>{{$_cat->desc}}</small>
                  {{/if}}
                </td>
              </tr>
              {{foreach from=$_cat->_back.item_types item=_item}}
              <tr>
                <td class="text" style="padding-left: 2em;">
                  <a href="?m={{$m}}&amp;tab=vw_daily_check_item_type&amp;item_type_id={{$_item->_id}}">
                    {{mb_value object=$_item field=title}}
                  </a>
                </td>
                <td class="compact">{{mb_value object=$_item field=desc}}</td>
                <td class="text">{{mb_value object=$_item field=attribute}}</td>
                <td>{{mb_value object=$_item field=active}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="10" class="empty">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
              </tr>
              {{/foreach}}
            {{/if}}
          {{foreachelse}}
            <tr>
              <td colspan="10" class="empty">{{tr}}CDailyCheckItemCategory.none{{/tr}}</td>
            </tr>
          {{/foreach}}
          </tbody>
        {{/foreach}}
      </table>
    </td>
    <td>
      <button type="button" class="new" onclick="location.href='?m={{$m}}&amp;tab=vw_daily_check_item_type&amp;item_type_id=0'">
        {{tr}}CDailyCheckItemType-title-create{{/tr}}
      </button>
      <form name="edit-CDailyCheckItemType" action="?m={{$m}}&amp;tab=vw_daily_check_item_type" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_daily_check_item_type_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="daily_check_item_type_id" value="{{$item_type->_id}}" />
        <input type="hidden" name="group_id" value="{{$g}}" />
        <input type="hidden" name="del" value="0" />
        <table class="main form">
          <tr>
            {{if $item_type->_id}}
            <th class="title modify" colspan="2">{{$item_type->title|truncate:30}}</th>
            {{else}}
            <th class="title" colspan="2">{{tr}}CDailyCheckItemType-title-create{{/tr}}</th>
            {{/if}}
          </tr>
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
              <select name="category_id">
                {{foreach from=$item_categories_list item=_cat_list key=_target}}
                  <optgroup label="{{tr}}CDailyCheckItemCategory.target_class.{{$_target}}{{/tr}}">
                    {{foreach from=$_cat_list item=_cat}}
                      <option value="{{$_cat->_id}}" {{if $_cat->_id == $item_type->category_id}} selected="selected" {{/if}}>{{$_cat->title}}</option>
                    {{/foreach}}
                  </optgroup>
                {{/foreach}}
              </select>
              <button type="button" class="new notext" onclick="popupEditDailyCheckItemCategory()">{{tr}}New{{/tr}}</button>
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
              {{if $item_type->_id}}
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$item_type->_view|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
              {{else}}
              <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr> 
        </table>
      </form>
    </td>
  </tr>
</table>