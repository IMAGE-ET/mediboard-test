<script type="text/javascript">
  function popupEditDailyCheckItemCategory() {
    var url = new Url('dPsalleOp', 'vw_daily_check_item_category');
    url.modal({width: 900, height: 600});
  }
  
  Main.add(function(){
    Control.Tabs.create("target_tabs", true);
  });
</script>

<table class="main">
  <tr>
    <td style="width: 30%;">
      <ul id="target_tabs" class="control_tabs">
        {{foreach from=$item_categories_by_class key=_class item=_categories}}
          <li>
            <a href="#tab-{{$_class}}">
              {{tr}}CDailyCheckItemCategory.target_class.{{$_class}}{{/tr}}
            </a>
          </li>
        {{/foreach}}
      </ul>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CDailyCheckItemType field=title}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=desc}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=attribute}}</th>
          <th>{{mb_title class=CDailyCheckItemType field=active}}</th>
        </tr>

        {{foreach from=$item_categories_by_class key=_class item=item_categories_by_target}}
          <tbody id="tab-{{$_class}}" style="display: none;">

          {{foreach from=$item_categories_by_target key=_target item=_categories}}
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

            {{foreach from=$_categories item=_cat}}
              {{if $_cat->_back.item_types|@count}}
                <tr>
                  <th colspan="4" class="category" style="text-align: left;">
                    <strong>{{$_cat->title}}</strong>
                    {{if $_cat->desc}}
                      &ndash; <small>{{$_cat->desc}}</small>
                    {{/if}}
                  </th>
                </tr>

                {{foreach from=$_cat->_back.item_types item=_item}}
                  <tr {{if $_item->_id == $item_type->_id}} class="selected" {{/if}}>
                    <td class="text" style="padding-left: 2em;">
                      <a href="?m={{$m}}&amp;tab=vw_daily_check_item_type&amp;item_type_id={{$_item->_id}}">
                        {{mb_value object=$_item field=title}}
                      </a>
                    </td>
                    <td class="compact">{{mb_value object=$_item field=desc}}</td>
                    <td class="text">{{mb_value object=$_item field=attribute}}</td>
                    <td>{{mb_value object=$_item field=active}}</td>
                  </tr>
                {{/foreach}}

              {{else}}
                <tr>
                  <td colspan="4" class="empty">{{tr}}CDailyCheckItemType.none{{/tr}}</td>
                </tr>
              {{/if}}
            {{/foreach}}
          {{foreachelse}}
            <tr>
              <td colspan="4" class="empty">{{tr}}CDailyCheckItemCategory.none{{/tr}}</td>
            </tr>
          {{/foreach}}
          </tbody>
        {{/foreach}}
      </table>
    </td>
    <td>
      <a class="button new" href="?m=salleOp&amp;tab=vw_daily_check_item_type&amp;item_type_id=0">
        {{tr}}CDailyCheckItemType-title-create{{/tr}}
      </a>
      <form name="edit-CDailyCheckItemType" action="?m=salleOp&amp;tab=vw_daily_check_item_type" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_daily_check_item_type_aed" />
        <input type="hidden" name="m" value="salleOp" />
        <input type="hidden" name="daily_check_item_type_id" value="{{$item_type->_id}}" />
        <input type="hidden" name="group_id" value="{{$g}}" />
        <input type="hidden" name="del" value="0" />
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
    </td>
  </tr>
</table>