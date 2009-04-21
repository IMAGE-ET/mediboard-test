{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

			{{if $can->edit}}
      <form name="editEtablissement" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_SpEtablissement_aed" />
      <input type="hidden" name="sp_etab_id" value="{{$etablissement->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $etablissement->_id}}
          <th class="title modify" colspan="2">
     	 				Modification de l'établissement 
          </th>
          {{else}}
          <th class="title" colspan="2">
      			Création d'un établissement
          </th>
          {{/if}}
        </tr>
        <tr>	
          	<th>{{mb_label object=$etablissement field="group_id"}}</th>
            <td>
            	<select name="group_id">
              	<option value="">&mdash; Choisir un établissement &mdash;</option>
              		{{foreach from=$listGroups item=curr_groupe}}
                		<option value="{{$curr_groupe->_id}}" {{if $curr_groupe->_id == $etablissement->_id}} selected="selected" {{/if}}  >
                  		{{$curr_groupe->_view}}
                		</option>
              		{{/foreach}}
            	</select>
          	</td>
        </tr>
        <tr>
      		<th>{{mb_label object=$etablissement field="increment_year"}}</th>
      		<td>{{mb_field object=$etablissement field="increment_year"}}</td>
        </tr>
        <tr>
      		<th>{{mb_label object=$etablissement field="increment_patient"}}</th>
      		<td>{{mb_field object=$etablissement field="increment_patient"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $etablissement->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'etablissement',objName:'{{$etablissement->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
