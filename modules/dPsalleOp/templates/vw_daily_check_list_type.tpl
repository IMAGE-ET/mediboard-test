{{mb_script module=salleOp script=check_list}}

<script>
Main.add(Control.Tabs.create.curry("list_type_tabs", true));
</script>

<table class="main layout">
  <tr>
    <td style="width: 50%;">
      <ul class="control_tabs" id="list_type_tabs">
        {{foreach from=$by_type key=_type item=_list_by_object}}
          <li>
            <a href="#tab-{{$_type}}">
              {{tr}}CDailyCheckListType.type.{{$_type}}{{/tr}}
            </a>
          </li>
        {{/foreach}}
      </ul>

      {{foreach from=$by_type key=_type item=_lists}}
        <div id="tab-{{$_type}}" style="display: none;">
          <a class="button new" href="?m=salleOp&amp;tab=vw_daily_check_list_type&amp;list_type_id=0&amp;type={{$_type}}&amp;dialog={{$dialog}}">
            {{tr}}CDailyCheckListType-title-create{{/tr}}
          </a>

          <table class="main tbl">
            <tr>
              <th>{{mb_title class=CDailyCheckListType field=title}}</th>
              <th>{{mb_title class=CDailyCheckListType field=description}}</th>
              <th>{{mb_title class=CDailyCheckListType field=_links}}</th>
              <th>{{tr}}CDailyCheckListType-back-daily_check_list_categories{{/tr}}</th>
            </tr>

            {{foreach from=$_lists item=_list}}
              <tr {{if $_list->_id == $list_type->_id}} class="selected" {{/if}}>
                <td>
                  <a href="?m=salleOp&amp;tab=vw_daily_check_list_type&amp;list_type_id={{$_list->_id}}&amp;dialog={{$dialog}}">
                    {{mb_value object=$_list field=title}}
                  </a>
                </td>
                <td class="compact">{{mb_value object=$_list field=description}}</td>
                <td class="compact">
                  {{foreach from=$_list->_ref_type_links item=_link}}
                    {{if $_link->object_id}}
                      {{$_link->_ref_object}}
                    {{else}}
                      <em> &ndash; {{tr}}All{{/tr}}</em>
                    {{/if}}
                    <br />
                  {{/foreach}}
                </td>
                <td>{{$_list->_count.daily_check_list_categories}}</td>
              </tr>
              {{foreachelse}}
              <tr>
                <td colspan="4" class="empty">
                  {{tr}}CDailyCheckListType.none{{/tr}}
                </td>
              </tr>
            {{/foreach}}
          </table>
        </div>
      {{/foreach}}
    </td>

    <td>
      {{mb_include module=salleOp template=inc_edit_check_list_type}}
    </td>
  </tr>
</table>