<table class="tbl">
  {{foreach from=$prestations item=_prestations key=object_class}}
    <tr>
        <th class="title" colspan="2">{{tr}}{{$object_class}}.all{{/tr}}</th>
    </tr>
    <tr>
      <th class="category">{{mb_label class=$object_class field=nom}}</th>
      <th class="category narrow">Nb. d'items</th>
    </tr>
    {{foreach from=$_prestations item=_prestation}}
      <tr id="prestation_{{$_prestation->_guid}}" class="prestation {{if $prestation_guid == $_prestation->_guid}}selected{{/if}}">
        <td>
          <a href="#1" onclick="updateSelected('{{$_prestation->_guid}}', 'prestation'); editPrestation('{{$_prestation->_id}}', '{{$_prestation->_class}}')">
            {{$_prestation->nom}}
          </a>
        </td>
        <td>{{$_prestation->_count_items}}</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="2" class="empty">{{tr}}{{$object_class}}.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>