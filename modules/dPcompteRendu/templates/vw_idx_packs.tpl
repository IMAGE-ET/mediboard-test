<!--  $Id$ -->

<script type="text/javascript">

function reloadListModele(object_class){
  var url = new Url("dPcompteRendu","httpreq_vw_list_modeles");
  url.addParam("object_class", object_class);
  url.addParam("user_id", "{{$user_id}}");
  url.addParam("pack_id", "{{$pack->_id}}");
  url.requestUpdate("listModeles");
}

Main.add(function () {
  {{if $pack->_id}}
    reloadListModele("{{$pack->object_class}}");
  {{/if}}
  Control.Tabs.create('tabs-owner', true);
});
</script>

<table class="main">

<tr>
  <td class="greedyPane">
    
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;pack_id=0" class="button new">{{tr}}CPack-title-create{{/tr}}</a>

    <form name="filterFrm" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
        <tr>
          <th class="category" colspan="10">Filtrer les packs</th>
        </tr>
  
        <tr>
          <th><label for="filter_user_id">Utilisateur</label></th>
          <td>
            <select name="filter_user_id" onchange="this.form.submit()">
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
            <select name="filter_class" onchange="this.form.submit()">
              <option value="">&mdash; Tous les types d'objets</option>
              {{foreach from=$classes|smarty:nodefaults key=_class item=_class_tr}}
              <option value="{{$_class}}" {{if $_class == $class_name}} selected="selected" {{/if}}>
                {{tr}}{{$_class}}{{/tr}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
    </form>

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
        <tr {{if $_pack->_id == $pack->_id}}class="selected"{{/if}}>
          {{assign var="pack_id" value=$_pack->_id}}
          {{assign var="href" value="?m=$m&tab=$tab&pack_id=$pack_id"}}
          <td class="text"><a href="{{$href}}">{{$_pack->nom}}</a></td>
          <td><a href="{{$href}}">{{$_pack->_modeles|@count}}</a></td>
          <td class="text"><a href="{{$href}}">{{tr}}{{$_pack->object_class}}{{/tr}}</a></td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="10">{{tr}}CPack.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </tbody>
      
      <tbody id="owner-func" style="display: none">
        {{foreach from=$packsFunc item=_pack}}
        <tr {{if $_pack->_id == $pack->_id}}class="selected"{{/if}}>
          {{assign var="pack_id" value=$_pack->_id}}
          {{assign var="href" value="?m=$m&tab=$tab&pack_id=$pack_id"}}
          <td><a href="{{$href}}">{{$_pack->nom}}</a></td>
          <td><a href="{{$href}}">{{$_pack->_modeles|@count}}</a></td>
          <td><a href="{{$href}}">{{tr}}{{$_pack->object_class}}{{/tr}}</a></td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="10">{{tr}}CPack.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </tbody>
      
      <tbody id="owner-etab" style="display: none">
        {{foreach from=$packsEtab item=_pack}}
        <tr {{if $_pack->_id == $pack->_id}}class="selected"{{/if}}>
          {{assign var="pack_id" value=$_pack->_id}}
          {{assign var="href" value="?m=$m&tab=$tab&pack_id=$pack_id"}}
          <td><a href="{{$href}}">{{$_pack->nom}}</a></td>
          <td><a href="{{$href}}">{{$_pack->_modeles|@count}}</a></td>
          <td><a href="{{$href}}">{{tr}}{{$_pack->object_class}}{{/tr}}</a></td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="10">{{tr}}CPack.none{{/tr}}</td>
        </tr>
        {{/foreach}}
      </tbody>
    </table>

  </td>
  
  <td>

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$pack->_spec}}">

    <input type="hidden" name="dosql" value="do_pack_aed" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$pack}}

    <table class="form">

    <tr>
      <th class="title {{if $pack->_id}}modify{{/if}}" colspan="2">
      {{if $pack->_id}}
        {{mb_include module=system template=inc_object_history object=$pack}}
        Modification d'un pack
      {{else}}
        Création d'un pack
      {{/if}}
      </th>
    </tr>

    <tr>
      <th>{{mb_label object=$pack field="chir_id"}}</th>
      <td>
        <select name="chir_id" class="{{$pack->_props.chir_id}}" style="width: 12em;">
          <option value="">&mdash; Associer &mdash;</option>
          {{foreach from=$listUser item=_user}}
            <option class="mediuser" style="border-color: #{{$_user->_ref_function->color}};" value="{{$_user->_id}}" {{if $_user->_id == $pack->chir_id}}selected="selected"{{/if}}>
              {{$_user}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$pack field="function_id"}}</th>
      <td>
        <select name="function_id" class="{{$pack->_props.function_id}}" style="width: 12em;">
          <option value="">&mdash; Associer &mdash;</option>
          {{foreach from=$listFunc item=_func}}
            <option class="mediuser" style="border-color: #{{$_func->color}};" value="{{$_func->_id}}" {{if $_func->_id == $pack->function_id}}selected="selected"{{/if}}>
              {{$_func}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$pack field="group_id"}}</th>
      <td>
        <select name="group_id" class="{{$pack->_props.group_id}}" style="width: 12em;">
          <option value="">&mdash; Associer &mdash;</option>
          {{foreach from=$listEtab item=_etab}}
            <option value="{{$_etab->_id}}" {{if $_etab->_id == $pack->group_id}} selected="selected" {{/if}}>
              {{$_etab}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$pack field="nom"}}</th>
      <td>{{mb_field object=$pack field="nom"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$pack field="object_class"}}</th>
      <td>
        <select name="object_class" {{if $pack->_id}}onchange="reloadListModele(this.value);"{{/if}} style="width: 12em;">
          <option value="">&mdash; Choix d'une classe</option>
          {{foreach from=$pack->_specs.object_class->_list item=object_class}}
            <option value="{{$object_class}}" {{if $object_class == $pack->object_class}}selected = "selected"{{/if}}>
              {{tr}}{{$object_class}}{{/tr}}	
						</option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $pack->_id}}
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le pack',objName:'{{$pack->nom|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

    {{if $pack->_id}}
    <table class="form">
      {{if $pack->_modeles|@count}}
      <tr><th class="category" colspan="2">Modèles du pack</th></tr>
      {{foreach from=$pack->_modeles key=key_modele item=curr_modele}}
      <tr>
        <td style="width: 0.1%;">
          <form name="delFrm{{$pack->pack_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_pack_aed" />
            {{mb_key object=$pack}}
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_del" value="{{$key_modele}}" />
            <button class="trash notext" type="submit">{{tr}}Delete{{/tr}}</button>
          </form>
        </td>
        <td>{{$curr_modele->nom}}</td>
      </tr>
      {{/foreach}}
      {{/if}}
      <tr><th class="category" colspan="2">Ajouter un modèle</th></tr>
      <tr>
        <td colspan="2" id="listModeles"></td>
     </tr>
    </table>
    {{/if}}
  </td>
</tr>
</table>
