{{mb_include module=system template=inc_pagination total=$total_disciplines current=$page change_page='CDiscipline.changePage'}}

<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="button narrow"></th>
          <th>{{mb_label object=$discipline field="text"}}</th>
          <th>{{mb_label object=$discipline field="categorie"}}</th>
          <th>{{tr}}CDiscipline-back-users{{/tr}}</th>
        </tr>

        {{foreach from=$disciplines item=_discipline}}
          <tr>
            <td>
              <button class="edit notext" onclick="CDiscipline.edit('{{$_discipline->_id}}', this)">{{tr}}Edit{{/tr}}</button>
            </td>
            <td class="text">
              {{$_discipline->_view}}
            </td>
            <td>
              {{$_discipline->categorie}}
            </td>
            <td style="text-align: center;">
              {{$_discipline->_ref_users|@count}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>