<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <a class="buttonnew" href="?m=sip&amp;tab=vw_idx_cip&amp;cip_id=0">
        Créer un nouveau client (CIP) pour le serveur (SIP)
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">CIP</th>
        </tr>
        <tr>
          <th>{{mb_title object=$cip field="client_id"}}</th>
          <th>{{mb_title object=$cip field="tag"}}</th>
          <th>{{mb_title object=$cip field="url"}}</th>
          <th>{{mb_title object=$cip field="login"}}</th>
          <th>{{mb_title object=$cip field="password"}}</th>
        </tr>
        {{foreach from=$listCIP item=curr_cip}}
        <tr {{if $curr_cip->_id == $cip->_id}}class="selected"{{/if}}>
          <td>
            <a href="?m=sip&amp;tab=vw_idx_cip&amp;cip_id={{$curr_cip->_id}}" title="Modifier le CIP">
              {{mb_value object=$curr_cip field="client_id"}}
            </a>
          </td>
          <td>{{mb_value object=$curr_cip field="tag"}}</td>
          <td class="text">{{mb_value object=$curr_cip field="url"}}</td>
          <td>{{mb_value object=$curr_cip field="login"}}</td>
          <td>{{if $curr_cip->password}}Oui{{else}}Non{{/if}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      <form name="editcip" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_cip_aed" />
      <input type="hidden" name="cip_id" value="{{$cip->_id}}" />
      <input type="hidden" name="del" value="0" />      
      
      <table class="form">
        <tr>
          {{if $cip->_id}}
          <th class="title modify" colspan="2">
            Modification du CIP {{$cip->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">
            Création d'un CIP
          </th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$cip field="client_id"}}</th>
          <td>{{mb_field object=$cip field="client_id"}}</td>
        </tr>
        <tr>  
          <th>{{mb_label object=$cip field="tag"}}</th>
          <td>{{mb_field object=$cip field="tag"}}</td>
        </tr>
        <tr>  
          <th>{{mb_label object=$cip field="url"}}</th>
          <td><input type="text" name="url" value="{{$cip->url}}" class="str notNull" value="%u%p" /></td>
        </tr>
        <tr>  
          <th>{{mb_label object=$cip field="login"}}</th>
          <td>{{mb_field object=$cip field="login"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$cip field="password"}}</th>
          <td>{{mb_field object=$cip field="password"}}</td>
         </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $cip->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le CIP',objName:'{{$cip->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
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