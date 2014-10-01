{{* Tooltip des antécédents du patient *}}
<table class="main tabview">
  <tr>
    <th class="title" colspan="2">
      {{tr}}CAntecedent.more{{/tr}}
    </th>
  </tr>
  <tr>
    {{foreach from=$tab_atc key=nom item=antecedents}}
      <td style="width: 50%;" class="cell-layout">
        <table class="tbl main tabview">
          <tr>
            <th>{{tr}}{{$nom}}{{/tr}}</th>
          </tr>
          {{foreach from=$antecedents key=name item=cat}}
            {{if $name != "alle" && $cat|@count}}
              <tr>
                <th class="section">
                  {{tr}}CAntecedent.type.{{$name}}{{/tr}}
                </th>
              </tr>
              {{foreach from=$cat item=ant}}
                <tr>
                  <td>
                    {{if $ant->date}}
                      {{mb_value object=$ant field=date}}:
                    {{/if}}
                    {{$ant->rques}}
                  </td>
                </tr>
              {{/foreach}}
            {{/if}}
          {{foreachelse}}
            {{if !$ant_communs|@count}}
              <tr>
                <td class="empty">{{tr}}CAntecedent.none{{/tr}}</td>
              </tr>
            {{/if}}
          {{/foreach}}
        </table>
      </td>
    {{/foreach}}
  </tr>
  <table class="tbl main tabview">
    {{foreach from=$ant_communs key=name item=cat}}
      <tr>
        <th class="section" colspan="2">
          {{tr}}CAntecedent.type.{{$name}}{{/tr}}
        </th>
      </tr>
      {{foreach from=$cat item=ant}}
        <tr>
          <td colspan="2">
            {{if $ant->date}}
              {{mb_value object=$ant field=date}}:
            {{/if}}
            {{$ant->rques}}
          </td>
        </tr>
      {{/foreach}}
    {{/foreach}}
  </table>
</table>