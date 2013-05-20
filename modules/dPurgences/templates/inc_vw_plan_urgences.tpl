<div class="grille">
  <table class="main" id="table_grille">
    {{foreach from=$grilles.$name_grille item=ligne}}
    <tr>
      {{foreach from=$ligne item=_zone }}
        {{if $_zone!="0"}}
            <td data-lit-id="{{foreach from=$_zone->_ref_lits item=i name=foo}}{{if $smarty.foreach.foo.first}}{{$i->_id}} {{/if}}{{/foreach}}"
            data-nb-lits="{{$_zone->_ref_lits|@count}}"  rowspan="{{$_zone->_ref_emplacement->hauteur}}" colspan="{{$_zone->_ref_emplacement->largeur}}"  class="chambre"
            data-chambre-id="{{$_zone->chambre_id}}"  style="background-color:#{{$_zone->_ref_emplacement->color}};" >
            <small class="shadow" style="background-color:#{{$_zone->_ref_emplacement->color}};">{{$_zone}}</small>
            {{assign var=chambre   value=$_zone->chambre_id}}
            {{if isset($listSejours.$name_grille.$chambre|smarty:nodefaults)}}
              {{foreach from=$listSejours.$name_grille.$chambre item=_sejour }}
                {{assign var=patient   value=$_sejour->_ref_patient}}
                {{assign var=rpu   value=$_sejour->_ref_rpu}}
                <div class="patient draggable ccmu-{{if $rpu->ccmu}}{{$rpu->ccmu}}{{else}}0{{/if}}" data-rpu-guid="{{$rpu->_guid}}" data-patient-id="{{$rpu->_ref_sejour->patient_id}}">
                  <form name="{{$rpu->_guid}}" action="" method="post">
                    <input type="hidden" name="dosql" value="do_rpu_aed" />
                    <input type="hidden" name="del" value="0" />
                    <input type="hidden" name="m" value="dPurgences" />                          
                    {{mb_key object=$rpu}}
                    <input type="hidden" name="box_id"      value="{{$rpu->box_id}}"/>
                  </form>
                   <a href="?m=dPurgences&tab=vw_aed_rpu&rpu_id={{$rpu->_id}}">
                     <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');" >
                       {{$patient->nom}} {{$patient->prenom}}
                     </span>
                   </a>                 
                  {{mb_include template=inc_icone_attente rpu=$_sejour->_ref_rpu width=16}}
                 <div class="libelle compact" >
                    {{$rpu->motif|truncate:30|lower}}
                    <div>({{$patient->_age}}) Arrivée: {{mb_value object=$_sejour field=_entree date=$date}}</div>
                    <div>{{$rpu->diag_infirmier|spancate:60:"..."|lower}}</div>
                 </div>
                 
                  {{if $rpu->radio_debut}}
                    <img src="modules/soins/images/radio{{if !$rpu->radio_fin}}_grey{{/if}}.png"
                      {{if !$rpu->radio_fin}}
                        title="{{tr}}CRPU-radio_debut{{/tr}} à {{$rpu->radio_debut|date_format:$conf.time}}"
                      {{else}}
                        title="{{tr}}CRPU-radio_fin{{/tr}} à {{$rpu->radio_fin|date_format:$conf.time}}"
                      {{/if}}/>
                  {{/if}}
                 
                  {{if $rpu->bio_depart}}
                    <img src="images/icons/labo{{if !$rpu->bio_retour}}_grey{{/if}}.png"
                      {{if !$rpu->bio_retour}}
                        title="{{tr}}CRPU-bio_depart{{/tr}} à {{$rpu->bio_depart|date_format:$conf.time}}"
                      {{else}}
                        title="{{tr}}CRPU-bio_retour{{/tr}} à {{$rpu->bio_retour|date_format:$conf.time}}"
                      {{/if}}/>
                  {{/if}}
      
                  {{if $rpu->specia_att}}
                    <img src="modules/soins/images/stethoscope{{if !$rpu->specia_arr}}_grey{{/if}}.png"
                      {{if !$rpu->specia_arr}}
                        title="{{tr}}CRPU-specia_att{{/tr}} à {{$rpu->specia_att|date_format:$conf.time}}"
                      {{else}}
                        title="{{tr}}CRPU-specia_arr{{/tr}} à {{$rpu->specia_arr|date_format:$conf.time}}"
                      {{/if}}/>
                  {{/if}}
      
                  {{if $_sejour->_nb_files_docs > 0}}
                      <img src="images/icons/docitem.png"
                        title="{{$_sejour->_nb_files|default:0}} {{tr}}CMbObject-back-files{{/tr}} / {{$_sejour->_nb_docs|default:0}} {{tr}}CMbObject-back-documents{{/tr}}"/>
                  {{/if}}
                  
                  {{assign var=prescription value=$_sejour->_ref_prescription_sejour}}
                  {{if $prescription->_id}}
                      {{if $prescription->_count_recent_modif_presc}}
                        <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
                      {{else}}
                        <img src="images/icons/ampoule_grey.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
                      {{/if}}
                  {{/if}}
                  
                  {{if $_sejour->UHCD}}
                    <img src="images/icons/uhcd.png"/>
                  {{/if}}
                </div>
              {{/foreach}}
            {{/if}}
            </td>
      {{else}}
        <td></td>
      {{/if}}
    {{/foreach}}
    </tr>
    {{foreachelse}}
    <div class="small-warning">{{tr}}dPurgences-no-service_{{$name_grille}}s_id{{/tr}}</div>
    {{/foreach}}
  </table>
</div>