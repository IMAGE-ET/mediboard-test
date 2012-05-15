<!-- $Id$ -->

<script type="text/javascript">
{{if $dialog}}
  if(!aProtocoles) {
    aProtocoles = {};
  }
  
  {{foreach from=$list_protocoles_total item=_protocole}}
    {{assign var="type_prot_chir" value="prot-"}}
    {{assign var="type_prot_anesth" value="prot-"}}
    {{if $_protocole->protocole_prescription_anesth_class == "CPrescriptionProtocolePack"}}
      {{assign var="type_prot_anesth" value="pack-"}}
    {{/if}}
      {{if $_protocole->protocole_prescription_chir_class == "CPrescriptionProtocolePack"}}
      {{assign var="type_prot_chir" value="pack-"}}
    {{/if}}
    aProtocoles[{{$_protocole->protocole_id}}] = {
      protocole_id     : {{$_protocole->protocole_id}},
      chir_id          : {{if $_protocole->chir_id}}"{{$_protocole->chir_id}}"{{else}}"{{$chir_id}}"{{/if}},
      codes_ccam       : "{{$_protocole->codes_ccam}}",
      cote             : "{{$_protocole->cote}}",
      DP               : "{{$_protocole->DP}}",
      libelle          : "{{$_protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
      libelle_sejour   : "{{$_protocole->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
      _hour_op         : "{{$_protocole->_hour_op}}",
      _min_op          : "{{$_protocole->_min_op}}",
      presence_preop   : "{{$_protocole->presence_preop}}",
      presence_postop  : "{{$_protocole->presence_postop}}",
      examen           : "{{$_protocole->examen|smarty:nodefaults|escape:"javascript"}}",
      materiel         : "{{$_protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
      convalescence    : "{{$_protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
      depassement      : "{{$_protocole->depassement}}",
      forfait          : "{{$_protocole->forfait}}",
      fournitures      : "{{$_protocole->fournitures}}",
      type             : "{{$_protocole->type}}",
      type_pec         : "{{$_protocole->type_pec}}",
      duree_uscpo      : "{{$_protocole->duree_uscpo}}",
      duree_preop      : "{{$_protocole->duree_preop}}",
      duree_hospi      : {{$_protocole->duree_hospi}},
      rques_sejour     : "{{$_protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
      rques_operation  : "{{$_protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
      protocole_prescription_anesth_id: "{{$type_prot_anesth}}{{$_protocole->protocole_prescription_anesth_id}}",
      libelle_protocole_prescription_chir: "{{$_protocole->_ref_protocole_prescription_chir->libelle|smarty:nodefaults}}",
      protocole_prescription_chir_id:   "{{$type_prot_chir}}{{$_protocole->protocole_prescription_chir_id}}",
      service_id       : "{{$_protocole->service_id}}"
    };
  {{/foreach}}
{{else}}
  Main.add(function(){
    $$("a[href=#{{$type}}] small")[0].update("({{$total_protocoles}})");
  });
{{/if}}

</script>

{{mb_include module=system template=inc_pagination total=$total_protocoles current=$page.$type change_page="changePage.$type" step=40}}

<table class="tbl">
  {{foreach from=$list_protocoles item=_protocole}}
  <tr>    
    <td class="text" onclick="chooseProtocole({{$_protocole->_id}}); return false;" style="cursor: pointer;">
      <strong>
        {{if $type == 'interv'}}
          {{if $_protocole->libelle}}
            {{$_protocole->libelle}}
          {{else}}
            Pas de libellé
          {{/if}}
        {{else}}
          {{if $_protocole->libelle_sejour}}
            {{$_protocole->libelle_sejour}}
          {{else}}
            [Pas de libellé]
          {{/if}}
        {{/if}}
      </strong>
      <br />
      {{if $_protocole->duree_hospi}}
        {{$_protocole->duree_hospi}} nuits en
      {{/if}}
      
      {{mb_value object=$_protocole field=type}}
      {{if $_protocole->chir_id}}
        - Dr {{$_protocole->_ref_chir->_view}}
      {{elseif $_protocole->function_id}}
        - {{$_protocole->_ref_function->_view}}
      {{elseif $_protocole->group_id}}
        - {{$_protocole->_ref_group->_view}}
      {{/if}}
      <br />
      
      {{if $_protocole->_ext_code_cim->code}}
        {{$_protocole->_ext_code_cim->code}}
        : [{{$_protocole->_ext_code_cim->libelle|truncate:80}}]
        <br />
      {{/if}}
      
      {{foreach from=$_protocole->_ext_codes_ccam item=_code}}
        {{$_code->code}}
        : [{{$_code->libelleLong|truncate:80}}]
        <br />
      {{/foreach}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="5">
      <div class="small-info">
      {{tr}}CProtocole.none{{/tr}} n'est disponible, veuillez commencer par 
      créer un protocole afin de l'utiliser pour planifier un séjour
      </div>
    </td>
  </tr>
  {{/foreach}}
</table>
