<script type="text/javascript" src="modules/dPpatients/javascript/autocomplete.js?build={{$mb_version_build}}"></script>

<script type="text/javascript">
function pageMain() {
  initInseeFields("group", "cp", "ville");
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;group_id=0" class="buttonnew">
        Créer un établissement
      </a>
      <table class="tbl">
        <tr>
          <th>liste des établissements</th>
          <th>Fonctions associées</th>
        </tr>
        {{foreach from=$listGroups item=curr_group}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$curr_group->group_id}}">
              {{$curr_group->text}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$curr_group->group_id}}">
              {{$curr_group->_ref_functions|@count}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <form name="group" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_groups_aed" />
	  <input type="hidden" name="group_id" value="{{$usergroup->group_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {{if $usergroup->group_id}}
            <a style="float:right;" href="javascript:view_log('CGroups',{{$usergroup->group_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Modification de l'établissement &lsquo;{{$usergroup->text}}&rsquo;
          {{else}}
            Création d'un établissement
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>
            <label for="text" title="intitulé de l'établissement, obligatoire.">Intitulé</label>
          </th>
          <td>
            <input type="text" title="{{$usergroup->_props.text}}" name="text" size="30" id="group_text" value="{{$usergroup->text}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="raison_sociale" title="Veuillez saisir la raison sociale de l'établissement">Raison Sociale</label>
          </th>
          <td>
            <input type="text" title="{{$usergroup->_props.raison_sociale}}" name="raison_sociale" size="30" value="{{$usergroup->raison_sociale}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="adresse" title="Veuillez saisir l'adresse l'établissement">Adresse</label>
          </th>
          <td>
            <textarea title="{{$usergroup->_props.adresse}}" name="adresse">{{$usergroup->adresse}}</textarea>
          </td>
        </tr>
        <tr>
          <th><label for="cp" title="Code postal">Code Postal</label></th>
          <td>
            <input size="31" maxlength="5" type="text" name="cp" value="{{$usergroup->cp}}" title="{{$usergroup->_props.cp}}" />
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th><label for="ville" title="Ville de l'établissement">Ville</label></th>
          <td>
            <input size="31" type="text" name="ville" value="{{$usergroup->ville}}" title="{{$usergroup->_props.ville}}" />
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th><label for="_tel1" title="Numéro de téléphone filaire">Téléphone</label></th>
          <td>
            <input type="text" name="_tel1" size="2" maxlength="2" value="{{$usergroup->_tel1}}" title="num|length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
            <input type="text" name="_tel2" size="2" maxlength="2" value="{{$usergroup->_tel2}}" title="num|length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
            <input type="text" name="_tel3" size="2" maxlength="2" value="{{$usergroup->_tel3}}" title="num|length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
            <input type="text" name="_tel4" size="2" maxlength="2" value="{{$usergroup->_tel4}}" title="num|length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
            <input type="text" name="_tel5" size="2" maxlength="2" value="{{$usergroup->_tel5}}" title="num|length|2" onkeyup="followUp(this, '_tel21', 2)" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="directeur" title="Veuillez saisir le nom du directeur de l'établissement">Nom du Directeur</label>
          </th>
          <td>
            <input type="text" title="{{$usergroup->_props.directeur}}" name="directeur" size="30" value="{{$usergroup->directeur}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="domiciliation">Domiciliation</label>
          </th>
          <td>
            <input type="text" title="{{$usergroup->_props.domiciliation}}" name="domiciliation" size="10" value="{{$usergroup->domiciliation}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="siret" title="Veuillez saisir le n° siret de l'établissement">N° SIRET</label>
          </th>
          <td>
            <input type="text" title="{{$usergroup->_props.siret}}" maxlength="14" name="siret" size="15" value="{{$usergroup->siret}}" />
          </td>
        </tr>
        
        
        
        
        
        
        <tr>
          <td class="button" colspan="2">
          {{if $usergroup->group_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'établissement',objName:'{{$usergroup->text|escape:javascript}}'})">
              Supprimer
            </button>
          {{else}}
            <button class="submit" type="submit" name="btnFuseAction">Créer</button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>