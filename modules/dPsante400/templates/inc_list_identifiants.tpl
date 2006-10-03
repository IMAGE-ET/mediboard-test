<table class="tbl">
  {{if $dialog}}
  <tr>
    <th colspan="4" class="title">
      {{if $list_idSante400|@count > 0}}
      Historique de {{$item}}
      {{else}}
      Pas d'historique
      {{/if}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    {{if !$dialog}}
    <th>classe</th>
    <th>ID Mediboard</th>
    <th>Objet</th>
    {{/if}}
    <th>Dernière mise à jour</th>
    <th>Etiquette</th>
    <th>ID Santé400</th>
  </tr>
  {{foreach from=$list_idSante400 item=curr_idSante400}}
  <tr>
    {{if !$dialog}}
    <td>{{$curr_idSante400->object_class}}</td>
    <td>{{$curr_idSante400->object_id}}</td>
    <td>
      {{assign var="object" value=$curr_idSante400->_ref_object}}
      {{if $object->_id}}
      {{$object->_view}}
      {{else}}
      <div class="warning">Objet supprimé</div>
      {{/if}}
    </td>
    {{/if}}
    <td>
      {{if !$dialog}}
      <a href="?m=dPsante400&amp;{{$actionType}}={{$action}}&amp;id_sante400_id={{$curr_idSante400->_id}}" >
        {{$curr_idSante400->last_update|date_format:"%d/%m/%Y à %Hh%M (%A)"}}
      </a>
      {{else}}
      {{$curr_idSante400->last_update|date_format:"%d/%m/%Y à %Hh%M (%A)"}}
      {{/if}}
    </td>
    <td>{{$curr_idSante400->id400}}</td>
    <td>{{$curr_idSante400->tag}}</td>
  </tr>
  {{/foreach}}
</table>
