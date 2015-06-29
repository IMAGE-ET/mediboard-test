
{{if $count_links && $show_only && $hypertext_links|@count}}
  <span onmouseover="ObjectTooltip.createDOM(this, 'view_hypertext-link')";>{{$hypertext_links|@count}} {{tr}}CMbObject-back-hypertext_links{{/tr}}</span>
{{/if}}

{{if !$show_only}}
  <table class="form">
    {{foreach from=$hypertext_links item=_hypertext_link}}
      <tr>
        <td class="narrow"><a href="#" onclick="HyperTextLink.edit('{{$object_id}}', '{{$object_class}}', '{{$_hypertext_link->_id}}');">{{$_hypertext_link->name}}</a></td>
        <td class="narrow"><button type="button" class="glob notext" title="{{tr}}Access{{/tr}}" onclick="HyperTextLink.accessLink('{{$_hypertext_link->name}}', '{{$_hypertext_link->link}}')"/></td>
      </tr>
      {{foreachelse}}
      <tr>
        <td colspan="2">{{tr}}CHyperTextLink.none{{/tr}}</td>
      </tr>
    {{/foreach}}
    <tr>
      <td colspan="2"><button type="button" class="new" onclick="HyperTextLink.edit('{{$object_id}}', '{{$object_class}}')">{{tr}}New{{/tr}}</button></td>
    </tr>
  </table>
{{else}}
  {{if $count_links}}
    <div style="display: none;" id="view_hypertext-link">
      <table class="tbl">
        <tr>
          <th>{{tr}}CHyperTextLink{{/tr}}</th>
        </tr>
        {{foreach from=$hypertext_links item=_hypertext_link name="loop_hyperlink"}}
          <tr>
            <td>
              <a href="#" onclick="HyperTextLink.accessLink('{{$_hypertext_link->name}}', '{{$_hypertext_link->link}}')"">{{$_hypertext_link->name}} <i class="fa fa-external-link"></i></a>
            </td>
          </tr>
          {{foreachelse}}
          <tr>
            <td>
            {{tr}}CHyperTextLink.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </div>
  {{else}}
    {{foreach from=$hypertext_links item=_hypertext_link name="loop_hyperlink"}}
      <a href="#" onclick="HyperTextLink.accessLink('{{$_hypertext_link->name}}', '{{$_hypertext_link->link}}')"">{{$_hypertext_link->name}} <i class="fa fa-external-link"></i></a>
    {{foreachelse}}
      {{tr}}CHyperTextLink.none{{/tr}}
    {{/foreach}}
  {{/if}}
{{/if}}