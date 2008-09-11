{{* $id: $ *}}

<h1>Liste des indentifiants synchronisés</h1>

<table class="tbl">
{{foreach from=$idGroups item=idGroup}}
  <tr>
    <th class="title">
      {{tr}}CGroups{{/tr}} : {{$idGroup->_ref_object->_view}}</h2>
    </th>
    <th>{{$idGroup->id400}}</th>
  </tr>
{{/foreach}}
</table>