<script type="text/javascript">
reloadListModele = function(filter_class){
	  var url = new Url("dPcompteRendu","httpreq_vw_list_modeles");
	  url.addParam("filter_class", filter_class);
	  url.addParam("user_id", "{{$user_id}}");
	  url.addParam("pack_id", "{{$pack->_id}}");
	  url.requestUpdate("listModeles");
	}

addEditPackCallback = function(id) {
	updateAddEditPack(id, '{{$filter_class}}');
	reloadList(id, '{{$user_id}}');
}

Main.add(function() {
	{{if $pack->_id}}
	  reloadListModele('{{$filter_class}}');
	{{/if}}
});

</script>
<form name="editFrm" action="?" method="post" onsubmit="return onSubmitFormAjax(this)" class="{{$pack->_spec}}">
    <input type="hidden" name="m" value="dPcompteRendu" />
    <input type="hidden" name="dosql" value="do_pack_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="callback" value="addEditPackCallback" />
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
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le pack',ajax: true, objName:'{{$pack->nom|smarty:nodefaults|JSAttribute}}'})">
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
      {{if $pack->_back.modele_links|@count}}
      <tr><th class="category" colspan="2">Modèles du pack</th></tr>
      {{foreach from=$pack->_back.modele_links item=modele_link}}
      <tr>
        <td style="width: 0.1%;">
          <form name="delFrm{{$modele_link->_id}}" action="?" method="post"
                onsubmit="return onSubmitFormAjax(this, 
                                {onComplete: function() {
                                   updateAddEditPack({{$pack->_id}});
                                   reloadList({{$pack->_id}}, '{{$user_id}}');
                                }});">
            <input type="hidden" name="m" value="dPcompteRendu" />
            <input type="hidden" name="dosql" value="do_modele_to_pack_aed" />
            {{mb_key object=$pack}}
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="modele_to_pack_id" value="{{$modele_link->_id}}" />
            <button class="trash notext" type="button" onclick="this.form.onsubmit()">{{tr}}Delete{{/tr}}</button>
          </form>
        </td>
        <td>{{$modele_link}}</td>
      </tr>
      {{/foreach}}
      {{/if}}
      <tr><th class="category" colspan="2">Ajouter un modèle</th></tr>
      <tr>
        <td colspan="2" id="listModeles"></td>
     </tr>
    </table>
    {{/if}}