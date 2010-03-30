<!-- $Id$ -->

<script type="text/javascript">

{{if $dialog}}
  var aProtocoles = {
    sejour: {},
    interv: {}
  };
  {{foreach from=$protocoles key=key_type item=curr_type}}
    {{foreach from=$curr_type item=curr_protocole}}
    aProtocoles['{{$key_type}}'][{{$curr_protocole->protocole_id}}] = {
      protocole_id     : {{$curr_protocole->protocole_id}},
      chir_id          : {{$curr_protocole->chir_id}},
      codes_ccam       : "{{$curr_protocole->codes_ccam}}",
      DP               : "{{$curr_protocole->DP}}",
      libelle          : "{{$curr_protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
      libelle_sejour   : "{{$curr_protocole->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
      _hour_op         : "{{$curr_protocole->_hour_op}}",
      _min_op          : "{{$curr_protocole->_min_op}}",
      examen           : "{{$curr_protocole->examen|smarty:nodefaults|escape:"javascript"}}",
      materiel         : "{{$curr_protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
      convalescence    : "{{$curr_protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
      depassement      : "{{$curr_protocole->depassement}}",
      forfait          : "{{$curr_protocole->forfait}}",
      fournitures      : "{{$curr_protocole->fournitures}}",
      type             : "{{$curr_protocole->type}}",
      duree_hospi      : {{$curr_protocole->duree_hospi}},
      rques_sejour     : "{{$curr_protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
      rques_operation  : "{{$curr_protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
      protocole_prescription_anesth_id: "{{$curr_protocole->protocole_prescription_anesth_id}}",
      protocole_prescription_chir_id:   "{{$curr_protocole->protocole_prescription_chir_id}}",
      service_id_sejour : "{{$curr_protocole->service_id_sejour}}"
    };
    {{/foreach}}
  {{/foreach}}
  
  Main.add(function(){
    var urlComponents = Url.parse();
    $(urlComponents.fragment || 'interv').show();
  });
{{/if}}

setClose = function(type, protocole_id) {
  window.opener.ProtocoleSelector.set(aProtocoles[type][protocole_id]);
  window.close();
}

</script>

{{if !$dialog}}
<ul id="tabs-protocoles" class="control_tabs">
  <li>
  	<a href="#interv" {{if !count($protocoles.interv)}}class="empty"{{/if}}>
  		Chirurgicaux <small>({{$protocoles.interv|@count}})</small>
  	</a>
  </li>
  <li>
  	<a href="#sejour" {{if !count($protocoles.sejour)}}class="empty"{{/if}}>
  		Médicaux <small>({{$protocoles.sejour|@count}})</small>
  	</a>
  </li>
</ul>

<script type="text/javascript">
Main.add(function(){
  // Don't use .create() because the #fragment of the url 
  // is not taken into account, and this is important here
  new Control.Tabs('tabs-protocoles');
});
</script>

<hr class="control_tabs" />
{{/if}}

<table class="tbl">
{{foreach from=$protocoles key=key_type item=_type}}
    
  <tbody id="{{$key_type}}" style="display: none;">
	<input id="type" type="hidden" name="type_protocole" value="{{$key_type}}">
	 {{if $key_type=="interv"}}
	 {{ assign var=nbprotocoles value=$nb.interv}}
	 {{else}}
	    {{ assign var=nbprotocoles value=$nb.sejour}}
    {{/if}}
	    {{mb_include module=system template=inc_pagination total=$nbprotocoles current=$page change_page='changePage'}}
    <tr>
      <th class="title">Liste des protocoles disponibles</th>
    </tr>
    {{foreach from=$_type item=_protocole}}
    <tr {{if $protSel->_id == $_protocole->_id && !$dialog}}class="selected"{{/if}}>    
      <td class="text">
        {{if $dialog}}
        <a href="#1" onclick="setClose('{{$key_type}}', {{$_protocole->_id}})">
        {{else}}
        <a href="?m={{$m}}&amp;tab=vw_protocoles&amp;protocole_id={{$_protocole->_id}}&amp;page={{$page}}">
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
{{/foreach}}
</table>
