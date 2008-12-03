{{* $Id: $ *}}

  <table class="tbl">
    <tr>
      <th colspan="10">Liste des DMI</th>
    </tr>
    <tr>  
      <th>{{mb_title class=CDMI field=nom}}</th>
      <th>{{mb_title class=CDMI field=code}}</th>
    </tr>
    {{foreach from=$category->_ref_dmis item=_dmi}}
    <tr>
      <td>
        {{if $category->group_id == $g}}
          <a href="?m={{$m}}&amp;tab=vw_elements&amp;dmi_id={{$_dmi->_id}}">
            {{mb_value object=$_dmi field=nom}}
          </a>
        {{else}}
          {{mb_value object=$_dmi field=nom}}
        {{/if}}
      </td>
      <td>
        {{mb_value object=$_dmi field=code}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10"><em>{{tr}}CDMI.none{{/tr}}</em></td>
    </tr>
    {{/foreach}}
  </table>