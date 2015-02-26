<!-- $Id$ -->

<script type="text/javascript">
{{if $dialog}}
  if(!aProtocoles) {
    aProtocoles = {};
  }
  
  {{foreach from=$list_protocoles item=_protocole}}
    {{assign var="type_prot_chir" value="prot-"}}
    {{assign var="type_prot_anesth" value="prot-"}}
    {{assign var=libelle value=""}}
    {{if $_protocole->protocole_prescription_anesth_class == "CPrescriptionProtocolePack"}}
      {{assign var="type_prot_anesth" value="pack-"}}
    {{/if}}
    {{if $_protocole->protocole_prescription_chir_class == "CPrescriptionProtocolePack"}}
      {{assign var="type_prot_chir" value="pack-"}}
    {{/if}}
    {{if $_protocole->_ref_protocole_prescription_chir}}
      {{assign var=libelle value=$_protocole->_ref_protocole_prescription_chir->libelle}}
    {{/if}}

{{*
    {{mb_ternary test=$_protocole->_ref_protocole_prescription_chir var=libelle value=$_protocole->_ref_protocole_prescription_chir->libelle other=""}}
*}}
    aProtocoles[{{$_protocole->_id}}] = {
      protocole_id     : {{$_protocole->_id}},
      chir_id          : {{if $_protocole->chir_id}}"{{$_protocole->chir_id}}"{{else}}"{{$chir->_id}}"{{/if}},
      chir_view        : "{{if $_protocole->chir_id}}{{$_protocole->_ref_chir->_view}}{{else}}{{$chir->_view}}{{/if}}",
      codes_ccam       : "{{$_protocole->codes_ccam}}",
      cote             : "{{$_protocole->cote}}",
      DP               : "{{$_protocole->DP}}",
      libelle          : "{{$_protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
      libelle_sejour   : "{{$_protocole->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
      _time_op         : "{{$_protocole->_time_op}}",
      presence_preop   : "{{$_protocole->presence_preop}}",
      presence_postop  : "{{$_protocole->presence_postop}}",
      examen           : "{{$_protocole->examen|smarty:nodefaults|escape:"javascript"}}",
      materiel         : "{{$_protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
      exam_per_op      : "{{$_protocole->exam_per_op|smarty:nodefaults|escape:"javascript"}}",
      convalescence    : "{{$_protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
      depassement      : "{{$_protocole->depassement}}",
      forfait          : "{{$_protocole->forfait}}",
      fournitures      : "{{$_protocole->fournitures}}",
      type             : "{{$_protocole->type}}",
      charge_id        : "{{$_protocole->charge_id}}",
      type_pec         : "{{$_protocole->type_pec}}",
      facturable       : "{{$_protocole->facturable}}",
      type_anesth      : "{{$_protocole->type_anesth}}",
      duree_uscpo      : "{{$_protocole->duree_uscpo}}",
      duree_preop      : "{{$_protocole->duree_preop}}",
      duree_hospi      : {{$_protocole->duree_hospi}},
      duree_heure_hospi : {{$_protocole->duree_heure_hospi}},
      rques_sejour     : "{{$_protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
      rques_operation  : "{{$_protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
      protocole_prescription_anesth_id: "{{$type_prot_anesth}}{{$_protocole->protocole_prescription_anesth_id}}",
      libelle_protocole_prescription_chir: "{{$libelle}}",
      protocole_prescription_chir_id:   "{{$type_prot_chir}}{{$_protocole->protocole_prescription_chir_id}}",
      service_id       : "{{$_protocole->service_id}}",
      uf_hebergement_id: "{{$_protocole->uf_hebergement_id}}",
      uf_medicale_id   : "{{$_protocole->uf_medicale_id}}",
      uf_soins_id      : "{{$_protocole->uf_soins_id}}",
      _types_ressources_ids : "{{$_protocole->_types_ressources_ids}}",
      exam_extempo     : "{{$_protocole->exam_extempo}}"
    };
  {{/foreach}}
{{else}}
  Main.add(function() {
    Control.Tabs.setTabCount('{{$type}}', '{{$total_protocoles}}');
  });
{{/if}} 

</script>

{{if !count($list_protocoles)}}
  <div class="small-info">
  {{tr}}CProtocole.none{{/tr}} n'est disponible, veuillez commencer par 
  créer un protocole afin de l'utiliser pour planifier un séjour
  </div>
  {{mb_return}}
{{/if}}

{{mb_include module=system template=inc_pagination total=$total_protocoles current=$page.$type change_page="changePage$type" step=$step}}

<table class="tbl">
  {{if $conf.dPplanningOp.CProtocole.nicer}}
  <tr>
    <th colspan="2">
      {{if $type == "interv"}}
        {{mb_title class=CProtocole field=libelle}}
      {{else}}
        {{mb_title class=CProtocole field=libelle_sejour}}
      {{/if}}
    </th>
    <th>
      {{if $type == "interv"}}
        {{mb_title class=CProtocole field=codes_ccam}}
      {{else}}
        {{mb_title class=CProtocole field=DP}}
      {{/if}}
    </th>
    <th>
      {{mb_title class=CProtocole field=type}}
    </th>
    <th>
      {{mb_title class=CProtocole field=duree_hospi}}
    </th>

    {{if $type == "interv"}}
    <th>
      {{mb_title class=CProtocole field=cote}}
    </th>
    <th>
      {{mb_title class=CProtocole field=temp_operation}}
    </th>
    {{else}}
    <th>
      {{mb_title class=CProtocole field=convalescence}} /
      {{mb_title class=CProtocole field=rques_sejour}}
    </th>
    {{/if}}
  </tr>
  {{/if}}

  {{foreach from=$list_protocoles item=_protocole}}

  {{if $conf.dPplanningOp.CProtocole.nicer}}
  <tr onclick="chooseProtocole({{$_protocole->_id}}); return false;" style="cursor: pointer;">
    <td class="narrow {{$_protocole->_owner}}">
    </td>
    <td class="text">
      {{if $type == "interv"}}
        <strong>{{mb_value object=$_protocole field=libelle}}</strong>
      {{else}}
        <strong>{{mb_value object=$_protocole field=libelle_sejour}}</strong>
      {{/if}}
    </td>
    
    <td class="text">
      {{if $type == "interv"}}
        {{foreach from=$_protocole->_ext_codes_ccam item=_code}}
        <div class="compact">
          <strong>{{$_code->code}}</strong>
          {{$_code->libelleLong|spancate}}
        </div>  
        {{/foreach}}
      {{else}}
        {{assign var=code value=$_protocole->_ext_code_cim}}
        {{if $code->code}}
          <strong>{{$code->code}}</strong>
          {{$code->libelle|spancate}}
        {{/if}}
      {{/if}}
    </td>
        
    <td>
      {{mb_value object=$_protocole field=type}}
    </td>

    <td {{if !$_protocole->duree_hospi}} class="empty" {{/if}} >
      {{mb_value object=$_protocole field=duree_hospi}} nuit(s)
    </td>

    {{if $type == "interv"}}
    <td {{if !$_protocole->cote}} class="empty" {{/if}} >
      {{mb_value object=$_protocole field=cote}}
    </td>

    <td>
      {{mb_value object=$_protocole field=temp_operation}}
    </td>

    {{else}}
    <td class="text">
      {{if $_protocole->convalescence}}
      <div class="compact">
        <strong>C</strong>: {{$_protocole->convalescence|spancate}}
      </div>
      {{/if}}

      {{if $_protocole->rques_sejour}}
      <div class="compact">
        <strong>R</strong>: {{$_protocole->rques_sejour|spancate}}
      </div>
      {{/if}}
    </td>

    {{/if}}
  </tr>  

  {{else}}
  <tr>    
    <td colspan="7" class="text" onclick="chooseProtocole({{$_protocole->_id}}); return false;" style="cursor: pointer;">
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
  {{/if}}
  {{/foreach}}
</table>
