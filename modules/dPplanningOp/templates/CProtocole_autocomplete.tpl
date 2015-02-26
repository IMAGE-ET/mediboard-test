{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{$match->loadRefsFwd()}}

<script>
  {{if $match->for_sejour}}
    {{assign var=type value="sejour"}}
  {{else}}
    {{assign var=type value="interv"}}
  {{/if}}
  {{assign var="type_prot_chir" value="prot-"}}
  {{assign var="type_prot_anesth" value="prot-"}}
  {{if $match->protocole_prescription_anesth_class == "CPrescriptionProtocolePack"}}
    {{assign var="type_prot_anesth" value="pack-"}}
  {{/if}}
    {{if $match->protocole_prescription_chir_class == "CPrescriptionProtocolePack"}}
    {{assign var="type_prot_chir" value="pack-"}}
  {{/if}}

  aProtocoles['{{$type}}'][{{$match->protocole_id}}] = {
    protocole_id     : "{{$match->protocole_id}}",
    chir_id          : "{{$match->chir_id}}",
    chir_view        : "{{if $match->chir_id}}{{$match->_ref_chir->_view}}{{/if}}",
    codes_ccam       : "{{$match->codes_ccam}}",
    DP               : "{{$match->DP}}",
    libelle          : "{{$match->libelle|smarty:nodefaults|escape:"javascript"}}",
    libelle_sejour   : "{{$match->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
    _time_op         : "{{$match->_time_op}}",
    presence_preop   : "{{$match->presence_preop|truncate:5:''}}",
    presence_postop  : "{{$match->presence_postop|truncate:5:''}}",
    examen           : "{{$match->examen|smarty:nodefaults|escape:"javascript"}}",
    materiel         : "{{$match->materiel|smarty:nodefaults|escape:"javascript"}}",
    exam_per_op      : "{{$match->exam_per_op|smarty:nodefaults|escape:"javascript"}}",
    convalescence    : "{{$match->convalescence|smarty:nodefaults|escape:"javascript"}}",
    depassement      : "{{$match->depassement}}",
    forfait          : "{{$match->forfait}}",
    fournitures      : "{{$match->fournitures}}",
    type             : "{{$match->type}}",
    type_pec         : "{{$match->type_pec}}",
    facturable       : "{{$match->facturable}}",
    type_anesth      : "{{$match->type_anesth}}",
    duree_hospi      : {{$match->duree_hospi}},
    duree_heure_hospi : {{$match->duree_heure_hospi}},
    cote             : "{{$match->cote}}",
    exam_extempo     : "{{$match->exam_extempo}}",
    rques_sejour     : "{{$match->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
    rques_operation  : "{{$match->rques_operation|smarty:nodefaults|escape:"javascript"}}",
    protocole_prescription_anesth_id: "{{$type_prot_anesth}}{{$match->protocole_prescription_anesth_id}}",
    protocole_prescription_chir_id  : "{{$type_prot_chir}}{{$match->protocole_prescription_chir_id}}",
    service_id       : "{{$match->service_id}}",
    uf_hebergement_id: "{{$match->uf_hebergement_id}}",
    uf_medicale_id   : "{{$match->uf_medicale_id}}",
    uf_soins_id      : "{{$match->uf_soins_id}}",
    _types_ressources_ids : "{{$match->_types_ressources_ids}}"
  };
</script>

<span id="{{$match->protocole_id}}" class="view text" style="float: left;">
  <strong>
    {{if !$match->for_sejour}}
      {{if $match->libelle}}
        {{$match->libelle}}
      {{else}}
        Pas de libellé
      {{/if}}
    {{else}}
      {{if $match->libelle_sejour}}
        {{$match->libelle_sejour}}
      {{else}}
        Pas de libellé
      {{/if}}
    {{/if}}
  </strong>
</span>


<div style="color: #666; font-size: 0.8em; padding-left: 0.5em; clear: both;">
  {{if $match->duree_hospi}}
    {{$match->duree_hospi}} nuits en
  {{/if}}
  
  {{mb_value object=$match field=type}}
  {{if $match->chir_id}}
    - Dr {{$match->_ref_chir->_view}}
  {{elseif $match->function_id}}
    - {{$match->_ref_function->_view}}
  {{elseif $match->group_id}}
    - {{$match->_ref_group->_view}}
  {{/if}}
  <br />
  
  {{if $match->_ext_code_cim->code}}
    {{$match->_ext_code_cim->code}}
  {{/if}}
  
  {{foreach from=$match->_ext_codes_ccam item=_code}}
    {{$_code->code}}
    <br />
  {{/foreach}}
</div>