{{main}}Control.Tabs.create('tabs-owner', true);{{/main}}

<a onclick = "updateAddEditPack('');" class="button new">{{tr}}CPack-title-create{{/tr}}</a>
<table class="form">
  <tr>
     <th class="category" colspan="10">Filtrer les packs</th>
  </tr>
  
  <tr>
    <th><label for="filter_user_id">Utilisateur</label></th>
    <td>
      <select name="filter_user_id" onchange="reloadList('{{$pack_id}}', this.value, '{{$filter_class}}')">
        <option value="">&mdash; Choisir un utilisateur</option>
        {{foreach from=$listUser item=_user}}
          <option class="mediuser" style="border-color: #{{$_user->_ref_function->color}};" value="{{$_user->user_id}}" {{if $_user->user_id == $user_id}} selected="selected" {{/if}}>
            {{$_user->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
    <th><label for="filter_class">Type d'objet</label></th>
    <td>
      <select name="filter_class" onchange="reloadList('{{$pack_id}}', '{{$user_id}}', this.value)">
        <option value="">&mdash; Tous les types d'objets</option>
        {{foreach from=$classes|smarty:nodefaults key=_class item=_class_tr}}
          <option value="{{$_class}}" {{if $_class == $filter_class}} selected="selected" {{/if}}>
            {{tr}}{{$_class}}{{/tr}}
          </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

<ul id="tabs-owner" class="control_tabs">
  <li><a href="#owner-user">{{$userSel}} <small>({{$packsUser|@count}})</small></a></li>
  <li><a href="#owner-func">{{$userSel->_ref_function}} <small>({{$packsFunc|@count}})</small></a></li>
  <li><a href="#owner-etab">{{$userSel->_ref_function->_ref_group}} <small>({{$packsEtab|@count}})</small></a></li>
</ul>
    <hr class="control_tabs" />
<table class="tbl">
  <tr>
    <th>Nom</th>
      <th>Modèles</th>
      <th>Type</th>
  </tr>
  
  <tbody id="owner-user" style="display: none">
    {{foreach from=$packsUser item=_pack}}
    <tr {{if $_pack->_id == $pack_id}}class="selected"{{/if}} id="p{{$_pack->_id}}">
      {{assign var="_pack_id" value=$_pack->_id}}
      {{assign var="onclick" value="updateAddEditPack('$_pack_id');"}}
      <td class="text"><a href="#1" onclick="{{$onclick}}">{{$_pack->nom}} </a></td>
      <td class="text">{{$_pack->_back.modele_links|@count}}</td>
      <td class="text">{{tr}}{{$_pack->object_class}}{{/tr}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CPack.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
      
  <tbody id="owner-func" style="display: none">
    {{foreach from=$packsFunc item=_pack}}
    <tr {{if $_pack->_id == $pack_id}}class="selected"{{/if}} id="p{{$_pack->_id}}">
      {{assign var="_pack_id" value=$_pack->_id}}
      {{assign var="onclick" value="updateAddEditPack('$_pack_id');"}}
      <td><a href="#1" onclick="{{$onclick}}">{{$_pack->nom}}</a></td>
      <td>{{$_pack->_back.modele_links|@count}}</td>
      <td>{{tr}}{{$_pack->object_class}}{{/tr}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CPack.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
      
  <tbody id="owner-etab" style="display: none">
    {{foreach from=$packsEtab item=_pack}}
    <tr {{if $_pack->_id == $pack_id}}class="selected"{{/if}} id="p{{$_pack->_id}}">
      {{assign var="_pack_id" value=$_pack->_id}}
      {{assign var="onclick" value="updateAddEditPack('$_pack_id');"}}
      <td><a href="#1" onclick="{{$onclick}}">{{$_pack->nom}}</a></td>
      <td>{{$_pack->_back.modele_links|@count}}</td>
      <td>{{tr}}{{$_pack->object_class}}{{/tr}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CPack.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
</table>