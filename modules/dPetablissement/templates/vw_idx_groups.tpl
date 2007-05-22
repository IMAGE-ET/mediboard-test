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
        Cr�er un �tablissement
      </a>
      <table class="tbl">
        <tr>
          <th>liste des �tablissements</th>
          <th>Fonctions associ�es</th>
        </tr>
        {{foreach from=$listGroups item=curr_group}}
        <tr {{if $curr_group->_id == $usergroup->_id}}class="selected"{{/if}}>
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
          
           <div class="idsante400" id="CGroups-{{$usergroup->group_id}}"></div>
           
            <a style="float:right;" href="#" onclick="view_log('CGroups',{{$usergroup->group_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification de l'�tablissement &lsquo;{{$usergroup->text}}&rsquo;
          {{else}}
            Cr�ation d'un �tablissement
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>
            <label for="text" title="intitul� de l'�tablissement, obligatoire.">Intitul�</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.text}}" name="text" size="30" id="group_text" value="{{$usergroup->text}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="raison_sociale" title="Veuillez saisir la raison sociale de l'�tablissement">Raison Sociale</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.raison_sociale}}" name="raison_sociale" size="30" value="{{$usergroup->raison_sociale}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="adresse" title="Veuillez saisir l'adresse l'�tablissement">Adresse</label>
          </th>
          <td>
            <textarea class="{{$usergroup->_props.adresse}}" name="adresse">{{$usergroup->adresse}}</textarea>
          </td>
        </tr>
        <tr>
          <th><label for="cp" title="Code postal">Code Postal</label></th>
          <td>
            <input size="31" maxlength="5" type="text" name="cp" value="{{$usergroup->cp}}" class="{{$usergroup->_props.cp}}" />
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th><label for="ville" title="Ville de l'�tablissement">Ville</label></th>
          <td>
            <input size="31" type="text" name="ville" value="{{$usergroup->ville}}" class="{{$usergroup->_props.ville}}" />
            <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th><label for="_tel1" title="Num�ro de t�l�phone filaire">T�l�phone</label></th>
          <td>
            <input type="text" name="_tel1" size="2" maxlength="2" value="{{$usergroup->_tel1}}" class="num length|2" onkeyup="followUp(this, '_tel2', 2)" /> - 
            <input type="text" name="_tel2" size="2" maxlength="2" value="{{$usergroup->_tel2}}" class="num length|2" onkeyup="followUp(this, '_tel3', 2)" /> -
            <input type="text" name="_tel3" size="2" maxlength="2" value="{{$usergroup->_tel3}}" class="num length|2" onkeyup="followUp(this, '_tel4', 2)" /> -
            <input type="text" name="_tel4" size="2" maxlength="2" value="{{$usergroup->_tel4}}" class="num length|2" onkeyup="followUp(this, '_tel5', 2)" /> -
            <input type="text" name="_tel5" size="2" maxlength="2" value="{{$usergroup->_tel5}}" class="num length|2" onkeyup="followUp(this, '_fax1', 2)" />
          </td>
        </tr>
        <tr>
          <th><label for="_fax1" title="Num�ro de fax">T�l�copie</label></th>
          <td>
            <input type="text" name="_fax1" size="2" maxlength="2" value="{{$usergroup->_fax1}}" class="num length|2" onkeyup="followUp(this, '_fax2', 2)" /> - 
            <input type="text" name="_fax2" size="2" maxlength="2" value="{{$usergroup->_fax2}}" class="num length|2" onkeyup="followUp(this, '_fax3', 2)" /> -
            <input type="text" name="_fax3" size="2" maxlength="2" value="{{$usergroup->_fax3}}" class="num length|2" onkeyup="followUp(this, '_fax4', 2)" /> -
            <input type="text" name="_fax4" size="2" maxlength="2" value="{{$usergroup->_fax4}}" class="num length|2" onkeyup="followUp(this, '_fax5', 2)" /> -
            <input type="text" name="_fax5" size="2" maxlength="2" value="{{$usergroup->_fax5}}" class="num length|2" onkeyup="followUp(this, 'mail', 2)" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="mail" title="Veuillez saisir une adresse e-mail">E-mail</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.mail}}" name="mail" value="{{$usergroup->mail}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="web" title="Veuillez saisir l'adresse d'un site internet">Site internet</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.web}}" name="web" value="{{$usergroup->web}}" />
          </td>
        </tr>
        
        <tr>
          <th>
            <label for="directeur" title="Veuillez saisir le nom du directeur de l'�tablissement">Nom du Directeur</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.directeur}}" name="directeur" size="30" value="{{$usergroup->directeur}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="domiciliation">Domiciliation</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.domiciliation}}" name="domiciliation" size="10" value="{{$usergroup->domiciliation}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="siret" title="Veuillez saisir le n� siret de l'�tablissement">N� SIRET</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.siret}}" maxlength="14" name="siret" size="15" value="{{$usergroup->siret}}" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="ape" title="Veuillez saisir le code APE de l'�tablissement">Code APE</label>
          </th>
          <td>
            <input type="text" class="{{$usergroup->_props.ape}}" maxlength="4" name="ape" size="5" value="{{$usergroup->ape}}" />
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $usergroup->group_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'�tablissement',objName:'{{$usergroup->text|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          {{else}}
            <button class="submit" type="submit" name="btnFuseAction">Cr�er</button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>