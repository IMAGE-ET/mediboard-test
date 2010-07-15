<!-- $Id$ -->

<script type="text/javascript">
{{if $dialog}}
  aProtocoles['{{$type}}'] = {};
  
  {{foreach from=$list_protocoles item=_protocole}}
  aProtocoles['{{$type}}'][{{$_protocole->protocole_id}}] = {
    protocole_id     : {{$_protocole->protocole_id}},
    chir_id          : {{$_protocole->chir_id}},
    codes_ccam       : "{{$_protocole->codes_ccam}}",
    DP               : "{{$_protocole->DP}}",
    libelle          : "{{$_protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
    libelle_sejour   : "{{$_protocole->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
    _hour_op         : "{{$_protocole->_hour_op}}",
    _min_op          : "{{$_protocole->_min_op}}",
    examen           : "{{$_protocole->examen|smarty:nodefaults|escape:"javascript"}}",
    materiel         : "{{$_protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
    convalescence    : "{{$_protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
    depassement      : "{{$_protocole->depassement}}",
    forfait          : "{{$_protocole->forfait}}",
    fournitures      : "{{$_protocole->fournitures}}",
    type             : "{{$_protocole->type}}",
    duree_hospi      : {{$_protocole->duree_hospi}},
    rques_sejour     : "{{$_protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
    rques_operation  : "{{$_protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
    protocole_prescription_anesth_id: "{{$_protocole->protocole_prescription_anesth_id}}",
    protocole_prescription_chir_id:   "{{$_protocole->protocole_prescription_chir_id}}",
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
  <tr {{if $protSel->_id == $_protocole->_id && !$dialog}}class="selected"{{/if}}>    
    <td class="text">
      {{if $dialog}}
        <a href="#1" onclick="setClose('{{$type}}', {{$_protocole->_id}})">
      {{else}}
        <a href="?m={{$m}}&amp;tab=vw_protocoles&amp;protocole_id={{$_protocole->_id}}&amp;page={{$page.$type}}">
      {{/if}}
        <strong>
          {{$_protocole->_ref_chir->_view}}
          
          {{if $type == 'interv'}}
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
      {{tr}}CProtocole.none{{/tr}} n'est disponible, veuillez commencer par 
      créer un protocole afin de l'utiliser pour planifier un séjour
      </div>
    </td>
  </tr>
  {{/foreach}}
</table>
