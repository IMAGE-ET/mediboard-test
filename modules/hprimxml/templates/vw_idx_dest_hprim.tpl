{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane" rowspan="5">
      <a class="button new" href="?m=hprimxml&amp;tab=vw_idx_dest_hprim&amp;dest_hprim_id=0">
      	{{tr}}CDestinataireHprim-title-create{{/tr}}
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">{{tr}}CDestinataireHprim{{/tr}}</th>
        </tr>
        <tr>
          <th>{{mb_title object=$dest_hprim field="nom"}}</th>
          <th>{{mb_title object=$dest_hprim field="group_id"}}</th>
          <th>{{mb_title object=$dest_hprim field="type"}}</th>
					<th>{{mb_title object=$dest_hprim field="evenement"}}</th>
          <th>{{mb_title object=$dest_hprim field="actif"}}</th>
        </tr>
        {{foreach from=$listDestHprim item=_dest_hprim}}
        <tr {{if $_dest_hprim->_id == $dest_hprim->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m=hprimxml&amp;tab=vw_idx_dest_hprim&amp;dest_hprim_id={{$_dest_hprim->_id}}" title="Modifier le destinataire HPRIM">
              {{mb_value object=$_dest_hprim field="nom"}}
            </a>
          </td>
          <td>{{$_dest_hprim->_ref_group->_view}}</td>
          <td>{{mb_value object=$_dest_hprim field="type"}}</td>
					<td>{{mb_value object=$_dest_hprim field="evenement"}}</td>
          <td>{{mb_value object=$_dest_hprim field="actif"}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editcip" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_destinataire_aed" />
      <input type="hidden" name="dest_hprim_id" value="{{$dest_hprim->_id}}" />
      <input type="hidden" name="del" value="0" />      
      
      <table class="form">
        <tr>
          {{if $dest_hprim->_id}}
          <th class="title modify" colspan="2">
            {{tr}}CDestinataireHprim-title-modify{{/tr}} '{{$dest_hprim->_view}}'
          </th>
          {{else}}
          <th class="title" colspan="2">
           {{tr}}CDestinataireHprim-title-create{{/tr}}
          </th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="nom"}}</th>
          <td>{{mb_field object=$dest_hprim field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="group_id"}}</th>
          <td>
            <select name="group_id" class="{{$dest_hprim->_props.group_id}}" style="width: 17em;">
              <option value="">&mdash; Associer à un établissement</option>
              {{foreach from=$listEtab item=_etab}}
                <option value="{{$_etab->_id}}" {{if $_etab->_id == $dest_hprim->group_id}} selected="selected" {{/if}}>
                  {{$_etab->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>  
          <th>{{mb_label object=$dest_hprim field="type"}}</th>
          <td>
          	<input type="text" name="type" size="20" value="{{if $dPconfig.sip.server}}cip{{else}}sip{{/if}}" readonly="readonly" />
          </td>
        </tr>
				<tr>
          <th>{{mb_label object=$dest_hprim field="evenement"}}</th>
          <td>{{mb_field object=$dest_hprim field="evenement"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="actif"}}</th>
          <td>{{mb_field object=$dest_hprim field="actif"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          	{{if $dest_hprim->_id}}
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$dest_hprim->_view|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
            {{/if}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
          </td>
        </tr>     
      </table>
      </form>
    </td>
  </tr>
  {{if $dest_hprim->_ref_exchange_source}}
  <tr>
    <td>
      <table class="form">  
        <tr>
          <th class="category" colspan="2">
            {{tr}}config-exchange-source{{/tr}}
          </th>
        </tr>
        
        <tr>
          <td colspan="2"> {{mb_include module=system template=inc_config_exchange_source source=$dest_hprim->_ref_exchange_source}} </td>
        </tr>
      </table>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>