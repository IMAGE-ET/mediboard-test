<table class="form" id="services_secteur">
  <tr>
    <th class="title" colspan="3">
      {{tr}}CSecteur-back-services{{/tr}}
    </th>
  </tr>
  <tr>
    <td class="columns-2">
      <ul id="itemTags" class="tags" style="float: none">
        {{foreach from=$secteur->_ref_services item=_service}}
          <li class="tag">
            <button type="button" class="delete"  style="display: inline-block !important;"
                    onclick="removeService('{{$_service->_id}}')">
            </button>
            <span> {{mb_value object=$_service field=nom}}</span>
            <span class="compact">{{mb_value object=$_service field=description}}</span>
          </li>
          <br/>
          {{foreachelse}}
          <span class="empty">{{tr}}CService.none{{/tr}}</span>
        {{/foreach}}
      </ul>
    </td>
  </tr>
</table>


