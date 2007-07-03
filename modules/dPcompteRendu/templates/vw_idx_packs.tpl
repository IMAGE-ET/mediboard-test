<!--  $Id$ -->

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />

	<a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;pack_id=0" class="buttonnew"><strong>Créer un pack</strong></a>
        
    <table class="form">

      <tr>
        <th><label for="filter_user_id" title="Filtrer les packs pour cet utilisateur">Utilisateur</label></th>
        <td>
          <select name="filter_user_id" onchange="this.form.submit()">
            <option value="0">&mdash; Choisir un utilisateur</option>
            {{foreach from=$users item=curr_user}}
            <option class="mediuser" style="border-color: #{{$curr_user->_ref_function->color}};" value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $user_id}} selected="selected" {{/if}}>
              {{$curr_user->_view}}
            </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="4"><strong>Packs créées</strong></th>
    </tr>
    
    <tr>
      <th>Utilisateur</th>
      <th>Nom</th>
      <th>modeles</th>
    </tr>

    {{foreach from=$packs item=curr_pack}}
    <tr>
      {{assign var="pack_id" value=$curr_pack->pack_id}}
      {{assign var="href" value="?m=$m&tab=$tab&pack_id=$pack_id"}}
      <td><a href="{{$href}}">{{$curr_pack->_ref_chir->_view}}</a></td>
      <td><a href="{{$href}}">{{$curr_pack->nom}}</a></td>
      <td><a href="{{$href}}">{{$curr_pack->_modeles|@count}}</a></td>
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="pane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_pack_aed" />
    {{mb_field object=$pack field="pack_id" hidden=1 prop=""}}
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $pack->pack_id}}
        Modification d'un pack
      {{else}}
        Création d'un pack
      {{/if}}
      </th>
    </tr>

    <tr>
      <th>
        {{mb_label object=$pack field="chir_id"}}
      </th>
      <td>
        <select name="chir_id" class="{{$pack->_props.chir_id}}">
          <option value="">&mdash; Choisir un utilisateur</option>
          {{foreach from=$users item=curr_user}}
          <option class="mediuser" style="border-color: #{{$curr_user->_ref_function->color}};" value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $pack->chir_id}} selected="selected" {{/if}}>
            {{$curr_user->_view}}
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
      <td class="button" colspan="2">
        {{if $pack->pack_id}}
        <button class="modify" type="submit">
          {{tr}}Modify{{/tr}}
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

    {{if $pack->pack_id}}
    <table class="form">
      {{if $pack->_modeles|@count}}
      <tr><th class="category" colspan="2">Modèles du pack</th></tr>
      {{foreach from=$pack->_modeles key=key_modele item=curr_modele}}
      <tr><td>{{$curr_modele->nom}}</td>
        <td>
          <form name="delFrm{{$pack->pack_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_pack_aed" />
          {{mb_field object=$pack field="pack_id" hidden=1 prop=""}}
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="modeles" value="{{$pack->modeles|smarty:nodefaults|JSAttribute}}" />
          <input type="hidden" name="_del" value="{{$key_modele}}" />
          <button class="trash notext" type="submit">{{tr}}Delete{{/tr}}</button>
          </form>
        </td>
      </tr>
      {{/foreach}}
      {{/if}}
      <tr><th class="category" colspan="2">Ajouter un modèle</th></tr>
      <tr><td colspan="2">
        <form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_pack_aed" />
        {{mb_field object=$pack field="pack_id" hidden=1 prop=""}}
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="modeles" value="{{$pack->modeles|smarty:nodefaults|JSAttribute}}" />
        <label for="_new" title="Veuillez choisir un modèle" />
        <select name="_new" class="notNull ref">
          <option value="">&mdash; Choisir un modèle</option>
          <optgroup label="Modèles du praticien">
            {{foreach from=$listModelePrat item=curr_modele}}
            <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
            {{/foreach}}
          </optgroup>
          <optgroup label="Modèles du cabinet">
            {{foreach from=$listModeleFunc item=curr_modele}}
            <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
            {{/foreach}}
          </optgroup>
        </select>
        <button type="submit" class="tick notext">{{tr}}Select{{/tr}}</button>
        </form>
      </td></tr>
    </table>
    {{/if}}

  </td>
  
</tr>

</table>
