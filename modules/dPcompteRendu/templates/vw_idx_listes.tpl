<!--  $Id$ -->

<script type="text/javascript">

function pageMain() {
  if(oForm = document.addFrm)
    document.addFrm._new.focus();
}
</script>

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />
	<a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;liste_id=0" class="buttonnew"><strong>Créer une liste de choix</strong></a>        
    <table class="form">

      <tr>
        <th><label for="filter_user_id" title="Filtrer les listes pour cet utilisateur">Utilisateur</label></th>
        <td>
          <select name="filter_user_id" onchange="this.form.submit()">
            <option value="0">&mdash; Tous les utilisateurs</option>
            {{foreach from=$users item=curr_user}}
            <option value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $user_id}} selected="selected" {{/if}}>
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
      <td class="text"><a href="{{$href}}">{{$curr_liste->_ref_modele->nom}} ({{tr}}CCompteRendu.type.{{$curr_liste->_ref_modele->type}}{{/tr}})</a></td>
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
      <td class="text"><a href="{{$href}}">{{$curr_liste->_ref_modele->nom}} ({{tr}}CCompteRendu.type.{{$curr_liste->_ref_modele->type}}{{/tr}})</a></td>
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
    <input type="hidden" name="liste_choix_id" value="{{$liste->liste_choix_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $liste->liste_choix_id}}
        Modification d'une liste
      {{else}}
        Création d'une liste
      {{/if}}
      </th>
    </tr>
  
    <tr>
      <th><label for="function_id" title="Fonction à laquelle le modèle est associé">Fonction</label></th>
      <td>
        <select name="function_id" title="{{$liste->_props.function_id}}" onchange="this.form.chir_id.value = ''">
          <option value="">&mdash; Associer à une fonction &mdash;</option>
          {{foreach from=$listFunc item=curr_func}}
            <option value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $liste->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  
    <tr>
      <th><label for="chir_id" title="Praticien auquel le modèle est associé">Praticien</label></th>
      <td>
        <select name="chir_id" title="{{$liste->_props.chir_id}}" onchange="this.form.function_id.value = ''">
          <option value="">&mdash; Associer à un praticien &mdash;</option>
          {{foreach from=$listPrat item=curr_prat}}
            <option value="{{$curr_prat->user_id}}" {{if ($liste->liste_choix_id && ($curr_prat->user_id == $liste->chir_id)) || (!$liste->liste_choix_id && ($curr_prat->user_id == $user_id))}}selected="selected"{{/if}}>
              {{$curr_prat->_view}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="nom" title="intitulé de la liste, obligatoire.">Intitulé</label></th>
      <td><input type="text" title="{{$liste->_props.nom}}" name="nom" value="{{$liste->nom}}" /></td>
    </tr>
    
    <tr>
      <th><label for="compte_rendu_id" title="Compte-rendu associé.">Compte-rendu</label></th>
      <td>
        <select name="compte_rendu_id">
          <option value="0">&mdash; Tous</option>
          <optgroup label="CR du praticien">
          {{foreach from=$listCrPrat item=curr_cr}}
          <option value="{{$curr_cr->compte_rendu_id}}" {{if $liste->compte_rendu_id == $curr_cr->compte_rendu_id}}selected="selected"{{/if}}>
            {{$curr_cr->nom}}
          </option>
          {{/foreach}}
          </optgroup>
          <optgroup label="CR du cabinet">
          {{foreach from=$listCrFunc item=curr_cr}}
          <option value="{{$curr_cr->compte_rendu_id}}" {{if $liste->compte_rendu_id == $curr_cr->compte_rendu_id}}selected="selected"{{/if}}>
            {{$curr_cr->nom}}
          </option>
          {{/foreach}}
          </optgroup>
        </select>

    <tr>
      <td class="button" colspan="2">
        {{if $liste->liste_choix_id}}
        <button class="submit" type="submit">
          Valider
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la liste',objName:'{{$liste->nom|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" type="submit">
          Créer
        </button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
  
  {{if $liste->liste_choix_id}}
  <td class="pane">
  
    <table class="form">
      {{if $liste->_valeurs|@count}}
      <tr><th class="category" colspan="2">Choix diponibles</th></tr>
      {{foreach from=$liste->_valeurs|smarty:nodefaults item=curr_valeur}}
      <tr><td>{{$curr_valeur}}</td>
        <td>
          <form name="delFrm{{$liste->liste_choix_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm()">
          <input type="hidden" name="dosql" value="do_liste_aed" />
          <input type="hidden" name="liste_choix_id" value="{{$liste->liste_choix_id}}" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="valeurs" value="{{$liste->valeurs}}" />
          <input type="hidden" name="chir_id" value="{{$liste->chir_id}}" />
          <input type="hidden" name="function_id" value="{{$liste->function_id}}" />
          <input type="hidden" name="_del" value="{{$curr_valeur}}" />
          <button class="trash notext" type="submit"></button>
          </form>
        </td>
      </tr>
      {{/foreach}}
      {{/if}}
      <tr><th class="category" colspan="2">Ajouter un choix</th></tr>
      <tr><td colspan="2">
        <form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm()">
        <input type="hidden" name="dosql" value="do_liste_aed" />
        <input type="hidden" name="liste_choix_id" value="{{$liste->liste_choix_id}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="valeurs" value="{{$liste->valeurs}}" />
        <input type="hidden" name="chir_id" value="{{$liste->chir_id}}" />
        <input type="hidden" name="function_id" value="{{$liste->function_id}}" />
        <input type="text" name="_new" value="" />
        <button type="submit" class="tick notext"></button>
        </form>
      </td></tr>
    </table>
  
  </td>
  {{/if}}
  
</tr>

</table>
