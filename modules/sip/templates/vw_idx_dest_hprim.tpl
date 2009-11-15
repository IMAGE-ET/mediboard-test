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
    <td class="halfPane" rowspan="3">
      {{if $dPconfig.sip.server}}
      <a class="button new" href="?m=sip&amp;tab=vw_idx_dest_hprim&amp;dest_hprim_id=0">
        Créer un nouveau destinataire HPRIM
      </a>
      {{/if}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="6">DESTINATAIRES HPRIM</th>
        </tr>
        <tr>
          <th>{{mb_title object=$dest_hprim field="destinataire"}}</th>
          <th>{{mb_title object=$dest_hprim field="type"}}</th>
          <th>{{mb_title object=$dest_hprim field="actif"}}</th>
          <th>{{mb_title object=$dest_hprim field="url"}}</th>
          <th>{{mb_title object=$dest_hprim field="username"}}</th>
          <th>{{mb_title object=$dest_hprim field="password"}}</th>
        </tr>
        {{foreach from=$listDestHprim item=_dest_hprim}}
        <tr {{if $_dest_hprim->_id == $dest_hprim->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m=sip&amp;tab=vw_idx_dest_hprim&amp;dest_hprim_id={{$_dest_hprim->_id}}" title="Modifier le destinataire HPRIM">
              {{mb_value object=$_dest_hprim field="destinataire"}}
            </a>
          </td>
          <td>{{mb_value object=$_dest_hprim field="type"}}</td>
          <td>{{mb_value object=$_dest_hprim field="actif"}}</td>
          <td class="text">{{mb_value object=$_dest_hprim field="url"}}</td>
          <td>{{mb_value object=$_dest_hprim field="username"}}</td>
          <td>{{if $_dest_hprim->password}}Oui{{else}}Non{{/if}}</td>
        </tr>
        {{/foreach}}
      </table>
      <div class="small-info">
        {{if !$dPconfig.sip.server}}
          Vous êtes en mode CIP vous pouvez ajouter qu'un destinataire H'XML (SIP).
        {{else}}
          Vous êtes en mode SIP vous pouvez ajouter plusieurs destinataires H'XML (CIP).
        {{/if}}
      </div>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editcip" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_cip_aed" />
      <input type="hidden" name="dest_hprim_id" value="{{$dest_hprim->_id}}" />
      <input type="hidden" name="del" value="0" />      
      
      <table class="form">
        <tr>
          {{if $dest_hprim->_id}}
          <th class="title modify" colspan="2">
            Modification du {{$dest_hprim->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">
            Création d'un destinataire HPRIM
          </th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="destinataire"}}</th>
          <td>{{mb_field object=$dest_hprim field="destinataire"}}</td>
        </tr>
        <tr>  
          <th>{{mb_label object=$dest_hprim field="type"}}</th>
          <td>
          	<input type="text" name="type" size="20" value="{{if $dPconfig.sip.server}}cip{{else}}sip{{/if}}" readonly="readonly" />
          </td>
        </tr>
        <tr>  
          <th>{{mb_label object=$dest_hprim field="url"}}</th>
          <td><input type="text" name="url" value="{{$dest_hprim->url}}" class="str notNull" value="%u%p" /></td>
        </tr>
        <tr>  
          <th>{{mb_label object=$dest_hprim field="username"}}</th>
          <td>{{mb_field object=$dest_hprim field="username"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="password"}}</th>
          <td>
            <input type="password" name="password" class="password {{if !$dest_hprim->_id}} notNull{{/if}}" value="" />
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$dest_hprim field="actif"}}</th>
          <td>{{mb_field object=$dest_hprim field="actif"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          	{{if $dest_hprim->_id}}
              <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
              <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$dest_hprim->_view|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
            {{else}}
              <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
      <div class="big-info">
        Les caractères suivants sont utilisés pour spécifier l'authentification dans l'url :
        <ul>
          <li>%u - Utilisateur service web </li>
          <li>%p - Mot de passe service web</li>
        </ul>
      </div>
    </td>
  </tr>
</table>