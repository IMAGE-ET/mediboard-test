{{main}}Control.Tabs.create('tabs-owner', true);{{/main}}

<a onclick = "updateAddEditPack('');" class="button new">{{tr}}CPack-title-create{{/tr}}</a>
<table class="form">
  <tr>
     <th class="category" colspan="10">Filtrer les packs</th>
  </tr>
  
  <tr>
    <th><label for="filter_user_id">Utilisateur</label></th>
    <td>
      <select name="filter_user_id" onchange="reloadList('{{$pack_id}}', '{{$filter_class}}', this.value)">
        <option value="">&mdash; Choisir un utilisateur</option>
        {{foreach from=$listUser item=_user}}
          <option class="mediuser" style="border-color: #{{$_user->_ref_function->color}};" value="{{$_user->_id}}" {{if $_user->_id == $user_id}} selected="selected" {{/if}}>
            {{$_user->_view}}
          </option>
        {{/foreach}}
      </select>
    </td>
    <th><label for="filter_class">Type d'objet</label></th>
    <td>
      <select name="filter_class" onchange="reloadList('{{$pack_id}}', this.value, '{{$user_id}}')">
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
  <li>
    <a href="#owner-user" {{if !$packsUser|@count}}class="empty"{{/if}}>
      {{$userSel}} <small>({{$packsUser|@count}})</small>
    </a>
  </li>
  <li>
    <a href="#owner-func" {{if !$packsFunc|@count}}class="empty"{{/if}}>
      {{$userSel->_ref_function}} <small>({{$packsFunc|@count}})</small>
    </a>
  </li>
  <li>
    <a href="#owner-etab" {{if !$packsEtab|@count}}class="empty"{{/if}}>
      {{$userSel->_ref_function->_ref_group}} <small>({{$packsEtab|@count}})</small>
    </a>
  </li>
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
      <td class="text">
        {{if $_pack->fast_edit_pdf}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
        {{elseif $_pack->fast_edit}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
        {{/if}}
        {{if $_pack->fast_edit || $_pack->fast_edit_pdf}}
          <img style="float: right;" src="images/icons/lightning.png"/>
        {{/if}}
        <a href="#1" onclick="{{$onclick}}">{{$_pack->nom}}</a></td>
      <td class="text">{{$_pack->_back.modele_links|@count}}</td>
      <td class="text">{{tr}}{{$_pack->object_class}}{{/tr}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CPack.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
      
  <tbody id="owner-func" style="display: none">
    {{foreach from=$packsFunc item=_pack}}
    <tr {{if $_pack->_id == $pack_id}}class="selected"{{/if}} id="p{{$_pack->_id}}">
      {{assign var="_pack_id" value=$_pack->_id}}
      {{assign var="onclick" value="updateAddEditPack('$_pack_id');"}}
      <td>
        {{if $_pack->fast_edit_pdf}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
        {{elseif $_pack->fast_edit}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
        {{/if}}
        {{if $_pack->fast_edit || $_pack->fast_edit_pdf}}
          <img style="float: right;" src="images/icons/lightning.png"/>
        {{/if}}
        <a href="#1" onclick="{{$onclick}}">{{$_pack->nom}}</a></td>
      <td>{{$_pack->_back.modele_links|@count}}</td>
      <td>{{tr}}{{$_pack->object_class}}{{/tr}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10" class="empty">{{tr}}CPack.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
      
  <tbody id="owner-etab" style="display: none">
    {{foreach from=$packsEtab item=_pack}}
    <tr {{if $_pack->_id == $pack_id}}class="selected"{{/if}} id="p{{$_pack->_id}}">
      {{assign var="_pack_id" value=$_pack->_id}}
      {{assign var="onclick" value="updateAddEditPack('$_pack_id');"}}
      <td>
        {{if $_pack->fast_edit_pdf}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
        {{elseif $_pack->fast_edit}}
          <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
        {{/if}}
        {{if $_pack->fast_edit || $_pack->fast_edit_pdf}}
          <img style="float: right;" src="images/icons/lightning.png"/>
        {{/if}}
        <a href="#1" onclick="{{$onclick}}">{{$_pack->nom}}</a></td>
      <td>{{$_pack->_back.modele_links|@count}}</td>
      <td>{{tr}}{{$_pack->object_class}}{{/tr}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="10"  class="empty">{{tr}}CPack.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </tbody>
</table>