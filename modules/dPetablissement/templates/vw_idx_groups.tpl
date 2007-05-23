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
            Modification de l'établissement &lsquo;{{$usergroup->text}}&rsquo;
          {{else}}
            Création d'un établissement
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="text"}}</th>
          <td>{{mb_field object=$usergroup field="text"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="raison_sociale"}}</th>
          <td>{{mb_field object=$usergroup field="raison_sociale"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="adresse"}}</th>
          <td>{{mb_field object=$usergroup field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="cp"}}</th>
          <td>{{mb_field object=$usergroup field="cp"}}
            <div style="display:none;" class="autocomplete" id="cp_auto_complete"></div>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$usergroup field="ville"}}</th>
          <td>{{mb_field object=$usergroup field="ville"}}
        	 <div style="display:none;" class="autocomplete" id="ville_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="_tel1" defaultFor="_tel1"}}</th>
		    <td>
		      {{mb_field object=$usergroup field="_tel1" tabindex="155" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel2', 2)"}} -
		      {{mb_field object=$usergroup field="_tel2" tabindex="156" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel3', 2)"}} -
		      {{mb_field object=$usergroup field="_tel3" tabindex="157" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel4', 2)"}} -
		      {{mb_field object=$usergroup field="_tel4" tabindex="158" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_tel5', 2)"}} -
		      {{mb_field object=$usergroup field="_tel5" tabindex="159" size="2" maxlength="2" prop="num length|2"}}
		    </td>
        </tr>
        <tr>
           <th>{{mb_label object=$usergroup field="_fax1" defaultFor="_fax1"}}</th>
		   <td>
		      {{mb_field object=$usergroup field="_fax1" tabindex="155" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_fax2', 2)"}} -
		      {{mb_field object=$usergroup field="_fax2" tabindex="156" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_fax3', 2)"}} -
		      {{mb_field object=$usergroup field="_fax3" tabindex="157" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_fax4', 2)"}} -
		      {{mb_field object=$usergroup field="_fax4" tabindex="158" size="2" maxlength="2" prop="num length|2" onkeyup="followUp(this, '_fax5', 2)"}} -
		      {{mb_field object=$usergroup field="_fax5" tabindex="159" size="2" maxlength="2" prop="num length|2"}}
		  </td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="mail"}}</th>
          <td>{{mb_field object=$usergroup field="mail"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="web"}}</th>
          <td>{{mb_field object=$usergroup field="web"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="directeur"}}</th>
          <td>{{mb_field object=$usergroup field="directeur"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="domiciliation"}}</th>
          <td>{{mb_field object=$usergroup field="domiciliation"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="siret"}}</th>
          <td>{{mb_field object=$usergroup field="siret"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="ape"}}</th>
          <td>{{mb_field object=$usergroup field="ape"}}</td>
 		</tr>
        <tr>
          <td class="button" colspan="2">
          {{if $usergroup->group_id}}
            <button class="modify" type="submit">Valider</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'établissement',objName:'{{$usergroup->text|smarty:nodefaults|JSAttribute}}'})">
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