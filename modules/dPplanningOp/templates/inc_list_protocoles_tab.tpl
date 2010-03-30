  <tbody id="{{$key_type}}" style="display: none;">
	<input id="type" type="hidden" name="type_protocole" value="{{$key_type}}">
   {{if $key_type=="interv"}}
   {{ assign var=nbprotocoles value=$nb.interv}}
   {{ assign var=listprotocoles value=$protocole_interv}}
   {{else}}
      {{ assign var=nbprotocoles value=$nb.sejour}}
      {{ assign var=listprotocoles value=$protocole_sejour}}
   {{/if}}
     
      {{mb_include module=system template=inc_pagination total=$nbprotocoles current=$page change_page='changePage'}}

    <tr>
      <th class="title">Liste des protocoles disponibles</th>
    </tr>
    
    {{foreach from=$listprotocoles item=_protocole}}
    <tr {{if $protSel->_id == $_protocole->_id && !$dialog}}class="selected"{{/if}}>    
      <td class="text">
        {{if $dialog}}
        <a href="#1" onclick="setClose('{{$key_type}}', {{$_protocole->_id}})">
        {{else}}
        <a href="?m={{$m}}&amp;tab=vw_protocoles&amp;protocole_id={{$_protocole->_id}}">
        {{/if}}
          <strong>
            {{$_protocole->_ref_chir->_view}}
            {{if $key_type == 'interv'}}
              {{if $_protocole->libelle}}
                - <em>[{{$_protocole->libelle}}]</em>
              {{/if}}
            {{else}}
              {{if $_protocole->libelle_sejour}}
                - <em>[{{$_protocole->libelle_sejour}}]</em>
              {{/if}}
            {{/if}}
          </strong>
        </a>
        {{if $_protocole->duree_hospi}}
        {{$_protocole->duree_hospi}} nuits en
        {{/if}}
        {{mb_value object=$_protocole field=type}}
        <br />
        {{if $_protocole->_ext_code_cim->code}}
          {{$_protocole->_ext_code_cim->code}}
          <em><strong>[{{$_protocole->_ext_code_cim->libelle|truncate:80}}]</strong></em>
          <br />
        {{/if}}
        {{foreach from=$_protocole->_ext_codes_ccam item=_code}}
          {{$_code->code}}
          <em><strong>[{{$_code->libelleLong|truncate:80}}]</strong></em>
          <br />
        {{/foreach}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="5">
        <div class="small-info">
        {{tr}}CProtocole.none{{/tr}} n'est disponible,
        veuillez commencer par créer un protocole
        afin de l'utiliser pour planifier un séjour
        </div>
      </td>
    </tr>
    {{/foreach}}
  </tbody>
