<!--  $Id$ -->

<script type="text/javascript">
Main.add(function () {
  if(oForm = document.addFrm)
    document.addFrm._new.focus();
});
</script>

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />
	<a href="?m={{$m}}&amp;tab={{$tab}}&amp;liste_id=0" class="button new"><strong>Créer une liste de choix</strong></a>        
    <table class="form">

      <tr>
        <th><label for="filter_user_id" title="Filtrer les listes pour cet utilisateur">Utilisateur</label></th>
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
      <th class="title" colspan="4">Listes de choix créées</th>
    </tr>
    
    <tr>
      <th>Utilisateur</th>
      <th>Nom</th>
      <th>Valeurs</th>
      <th>Compte-rendu associé</th>
    </tr>
    
    <tr>
      <th colspan="4"><strong>Modèles personnels</strong></th>
    </tr>

    {{foreach from=$listesPrat item=curr_liste}}
    <tr>
      {{assign var="liste_id" value=$curr_liste->liste_choix_id}}
      {{assign var="href" value="?m=$m&tab=$tab&liste_id=$liste_id"}}
      <td class="text"><a href="{{$href}}">{{$curr_liste->_ref_chir->_view}}</a></td>
      <td class="text"><a href="{{$href}}">{{$curr_liste->nom}}</a></td>
      <td><a href="{{$href}}">{{$curr_liste->_valeurs|@count}}</a></td>
      {{if $curr_liste->_ref_modele->compte_rendu_id}}
      <td class="text"><a href="{{$href}}">{{$curr_liste->_ref_modele->nom}} ({{tr}}{{$curr_liste->_ref_modele->object_class}}{{/tr}})</a></td>
      {{else}}
      <td><a href="{{$href}}">&mdash; Tous &mdash;</a></td>
      {{/if}}
    </tr>
    {{/foreach}}
    
    <tr>
      <th colspan="4"><strong>Modèles de cabinet</strong></th>
    </tr>

    {{foreach from=$listesFunc item=curr_liste}}
    <tr>
      {{assign var="liste_id" value=$curr_liste->liste_choix_id}}
      {{assign var="href" value="?m=$m&tab=$tab&liste_id=$liste_id"}}
      <td class="text"><a href="{{$href}}">{{$curr_liste->_ref_function->text}}</a></td>
      <td class="text"><a href="{{$href}}">{{$curr_liste->nom}}</a></td>
      <td><a href="{{$href}}">{{$curr_liste->_valeurs|@count}}</a></td>
      {{if $curr_liste->_ref_modele->compte_rendu_id}}
      <td class="text"><a href="{{$href}}">{{$curr_liste->_ref_modele->nom}} ({{tr}}{{$curr_liste->_ref_modele->object_class}}{{/tr}})</a></td>
      {{else}}
      <td><a href="{{$href}}">&mdash; Tous &mdash;</a></td>
      {{/if}}
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="pane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_liste_aed" />
    {{mb_field object=$liste field="liste_choix_id" hidden=1 prop=""}}
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $liste->_id}}
        {{tr}}CListeChoix-title-modify{{/tr}} '{{$liste->_view}}'
      {{else}}
        {{tr}}CListeChoix-title-create{{/tr}}
      {{/if}}
      </th>
    </tr>
  
    <tr>
      <th>{{mb_label object=$liste field="function_id"}}</th>
      <td>
        <select name="function_id" class="{{$liste->_props.function_id}}">
          <option value="">&mdash; Associer à une fonction &mdash;</option>
          {{foreach from=$listFunc item=curr_func}}
            <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $liste->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$liste field="chir_id"}}</th>
      <td>
        <select name="chir_id" class="{{$liste->_props.chir_id}}">
          <option value="">&mdash; Associer à un praticien &mdash;</option>
          {{foreach from=$listPrat item=curr_prat}}
            <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if ($liste->liste_choix_id && ($curr_prat->user_id == $liste->chir_id)) || (!$liste->liste_choix_id && ($curr_prat->user_id == $user_id))}}selected="selected"{{/if}}>
              {{$curr_prat->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$liste field="nom"}}</th>
      <td><input type="text" class="{{$liste->_props.nom}}" name="nom" value="{{$liste->nom}}" /></td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$liste field="compte_rendu_id"}}</th>
      <td>
        <select name="compte_rendu_id">
          <option value="">&mdash; Tous</option>
          <optgroup label="CR du praticien">
          {{foreach from=$listCrPrat item=curr_cr}}
          <option value="{{$curr_cr->_id}}" {{if $liste->compte_rendu_id == $curr_cr->_id}}selected="selected"{{/if}}>
            {{$curr_cr->nom}}
          </option>
          {{/foreach}}
          </optgroup>
          <optgroup label="CR du cabinet">
          {{foreach from=$listCrFunc item=curr_cr}}
          <option value="{{$curr_cr->_id}}" {{if $liste->compte_rendu_id == $curr_cr->_id}}selected="selected"{{/if}}>
            {{$curr_cr->nom}}
          </option>
          {{/foreach}}
          </optgroup>
        </select>

    <tr>
      <td class="button" colspan="2">
        {{if $liste->liste_choix_id}}
        <button class="modify" type="submit">
          {{tr}}Modify{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la liste',objName:'{{$liste->nom|smarty:nodefaults|JSAttribute}}'})">
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

  
  {{if $liste->_id}}
 
    {{if $liste->_valeurs|@count}}
    <table class="tbl">
      <tr><th class="category" colspan="2">Choix diponibles</th></tr>
      {{foreach from=$liste->_valeurs item=curr_valeur name=choix}}
      <tr>
        <td class="text">{{$curr_valeur|nl2br}}</td>
        <td style="width: 1%">
          <form name="DelChoix-{{$smarty.foreach.choix.iteration}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="dosql" value="do_liste_aed" />
          {{mb_field object=$liste field="liste_choix_id" hidden=1 prop=""}}
          <input type="hidden" name="del" value="0" />
          {{mb_field object=$liste field="valeurs" hidden=1 prop=""}}
          {{mb_field object=$liste field="chir_id" hidden=1 prop=""}}
          {{mb_field object=$liste field="function_id" hidden=1 prop=""}}
          <input type="hidden" name="_del" value="{{$curr_valeur}}" />
          <button class="remove notext" type="submit">{{tr}}Delete{{/tr}}</button>
          </form>
        </td>
      </tr>
      {{/foreach}}
    </table>
    {{/if}}
     
    <form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    
    <input type="hidden" name="dosql" value="do_liste_aed" />
    {{mb_field object=$liste field="liste_choix_id" hidden=1 prop=""}}
    <input type="hidden" name="del" value="0" />
    {{mb_field object=$liste field="valeurs" hidden=1 prop=""}}
    {{mb_field object=$liste field="chir_id" hidden=1 prop=""}}
    {{mb_field object=$liste field="function_id" hidden=1 prop=""}}

    <table class="form">
      <tr>
      	<th class="category" colspan="2">Ajouter un choix</th>
      </tr>
      <tr>
        <td>
	        <textarea name="_new"></textarea>
	      </td>
	    </tr>
	    <tr>
        <td class="button">
	        <button type="submit" class="add">{{tr}}Add{{/tr}}</button>
      	</td>
     	</tr>
    </table>

    </form>

  {{/if}}
  </td>  
</tr>
</table>