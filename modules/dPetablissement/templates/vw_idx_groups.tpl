{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;group_id=0" class="button new">
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
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$curr_group->group_id}}">
              {{$curr_group->text}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;group_id={{$curr_group->group_id}}">
              {{$curr_group->_ref_functions|@count}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">

			{{mb_include_script module="dPpatients" script="autocomplete"}}
			<script type="text/javascript">
			Main.add(function () {
			  InseeFields.initCPVille("group", "cp", "ville", "tel");
			});
			</script>
    	
      <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_groups_aed" />
   	  <input type="hidden" name="group_id" value="{{$usergroup->group_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $usergroup->group_id}}
          <th class="category modify" colspan="2">
		      {{mb_include module=system template=inc_object_idsante400 object=$usergroup}}
		      {{mb_include module=system template=inc_object_history object=$usergroup}}
            Modification de l'établissement &lsquo;{{$usergroup->text}}&rsquo;
          {{else}}
          <th class="category" colspan="2">
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
          <td>{{mb_field object=$usergroup field="cp"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="ville"}}</th>
          <td>{{mb_field object=$usergroup field="ville"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="tel"}}</th>
		      <td>{{mb_field object=$usergroup field="tel"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="fax"}}</th>
		      <td>{{mb_field object=$usergroup field="fax"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="tel_anesth"}}</th>
		      <td>{{mb_field object=$usergroup field="tel_anesth"}}</td>
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
          <th>{{mb_label object=$usergroup field="finess"}}</th>
          <td>{{mb_field object=$usergroup field="finess"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$usergroup field="ape"}}</th>
          <td>{{mb_field object=$usergroup field="ape"}}</td>
     		</tr>
        

        {{if $usergroup->_id}}
        <tr>
          <th>{{mb_label object=$usergroup field="service_urgences_id"}}</th>
          <td>
            <select name="service_urgences_id">
              <option value="">&mdash Choisir le service d'urgences</option>
              {{foreach from=$usergroup->_ref_functions item="curr_fct"}}
              <option value="{{$curr_fct->_id}}" class="mediuser" style="border-color: #{{$curr_fct->color}}" {{if $curr_fct->_id == $usergroup->service_urgences_id}}selected="selected"{{/if}}>
                {{$curr_fct->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}

        <tr>
          <td class="button" colspan="2">
          {{if $usergroup->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Save{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'établissement',objName:'{{$usergroup->text|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{else}}
            <button class="new" type="submit" name="create">
              {{tr}}Create{{/tr}}
            </button>
          {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>