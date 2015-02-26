{{*
 * $Id:$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 *}}

{{assign var="type_prot_chir" value="prot-"}}
{{assign var="type_prot_anesth" value="prot-"}}
{{assign var=libelle value=""}}
{{if $protocole->protocole_prescription_anesth_class == "CPrescriptionProtocolePack"}}
  {{assign var="type_prot_anesth" value="pack-"}}
{{/if}}
  {{if $protocole->protocole_prescription_chir_class == "CPrescriptionProtocolePack"}}
  {{assign var="type_prot_chir" value="pack-"}}
{{/if}}
{{if $protocole->_ref_protocole_prescription_chir}}
  {{assign var=libelle value=$protocole->_ref_protocole_prescription_chir->libelle}}
{{/if}}

<script type="text/javascript">
  aProtocoles[{{$protocole->_id}}] = {
    protocole_id     : {{$protocole->_id}},
    chir_id          : {{if $protocole->chir_id}}"{{$protocole->chir_id}}"{{else}}"{{$chir_id}}"{{/if}},
    chir_view        : "{{if $protocole->chir_id}}{{$protocole->_ref_chir->_view}}{{/if}}",
    codes_ccam       : "{{$protocole->codes_ccam}}",
    cote             : "{{$protocole->cote}}",
    DP               : "{{$protocole->DP}}",
    libelle          : "{{$protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
    libelle_sejour   : "{{$protocole->libelle_sejour|smarty:nodefaults|escape:"javascript"}}",
    _time_op         : "{{$protocole->_time_op}}",
    presence_preop   : "{{$protocole->presence_preop|truncate:5:''}}",
    presence_postop  : "{{$protocole->presence_postop|truncate:5:''}}",
    examen           : "{{$protocole->examen|smarty:nodefaults|escape:"javascript"}}",
    materiel         : "{{$protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
    exam_per_op      : "{{$protocole->exam_per_op|smarty:nodefaults|escape:"javascript"}}",
    convalescence    : "{{$protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
    depassement      : "{{$protocole->depassement}}",
    forfait          : "{{$protocole->forfait}}",
    fournitures      : "{{$protocole->fournitures}}",
    type             : "{{$protocole->type}}",
    charge_id        : "{{$protocole->charge_id}}",
    type_pec         : "{{$protocole->type_pec}}",
    facturable       : "{{$protocole->facturable}}",
    type_anesth      : "{{$protocole->type_anesth}}",
    duree_uscpo      : "{{$protocole->duree_uscpo}}",
    duree_preop      : "{{$protocole->duree_preop}}",
    duree_hospi      : {{$protocole->duree_hospi}},
    duree_heure_hospi : {{$protocole->duree_heure_hospi}},
    rques_sejour     : "{{$protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
    rques_operation  : "{{$protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
    protocole_prescription_anesth_id: "{{$type_prot_anesth}}{{$protocole->protocole_prescription_anesth_id}}",
    libelle_protocole_prescription_chir: "{{$libelle}}",
    protocole_prescription_chir_id:   "{{$type_prot_chir}}{{$protocole->protocole_prescription_chir_id}}",
    service_id       : "{{$protocole->service_id}}",
    uf_hebergement_id: "{{$protocole->uf_hebergement_id}}",
    uf_medicale_id   : "{{$protocole->uf_medicale_id}}",
    uf_soins_id      : "{{$protocole->uf_soins_id}}",
    _types_ressources_ids : "{{$protocole->_types_ressources_ids}}",
    exam_extempo     : "{{$protocole->exam_extempo}}"
  };
  
  ProtocoleSelector.set(aProtocoles[{{$protocole->_id}}]);
  Control.Modal.close();
</script>
