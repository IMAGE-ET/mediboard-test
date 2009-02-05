{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="prescription_editor"}}

<table class="main">
  <tr>
    <td colspan="2">
      <form name="FilterFrm" action="?" method="get" onsubmit="return checkForm(this);">     
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <table class="form">
        <tr>
          <td  class="readonly">
            {{mb_label object=$filter field=object_class}}
            {{mb_field object=$filter field=object_class}}
          </td>
          <td class="readonly">
            {{mb_label object=$filter field=object_id}}
            {{mb_field object=$filter field=object_id hidden="1" onchange="this.form.submit()"}}
            {{mb_include_script module=system script=object_selector}}
            <input type="text" size="60" name="_view" readonly="readonly" value="{{if $object->_id}}{{$object->_view}}{{/if}}" />
            <button type="button" onclick="ObjectSelector.init()" class="search">Rechercher</button>
            <script type="text/javascript">
              ObjectSelector.init = function() {
                this.sForm     = "FilterFrm";
                this.sView     = "_view";
                this.sId       = "object_id";
                this.sClass    = "object_class";
                this.onlyclass = "true"; 
                this.pop();
              }
            </script>
          </td>
        </tr>
       </table>
      </form>
    </td>
  </tr>
  {{if $object->_id}}
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="title">
            {{$patient->_view}} -
            {{mb_value object=$patient field=naissance}}
            ({{$patient->_age}} ans)
          </th>
        </tr>
        <!-- Affichage du dossier medical du patient -->
        <tr>
          <td class="text">
            <strong>Antécédents</strong>
            <ul>
            {{if $dossier_medical->_ref_antecedents}}
              {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
              {{if $list_antecedent|@count}}
              <li>
                {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
                {{foreach from=$list_antecedent item=curr_antecedent}}
                <ul>
                  <li>
                    {{if $curr_antecedent->date}}
                      {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
                    {{/if}}
                    <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_antecedent->_guid}}', 'objectViewHistory')">
                      {{$curr_antecedent->rques}}
                    </span>
                  </li>
                </ul>
                {{/foreach}}
              </li>
              {{/if}}
              {{/foreach}}
            {{else}}
              <li><em>Pas d'antécédents</em></li>
            {{/if}}
            </ul>
            <strong>Traitements</strong>
            <ul>
              {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
              <li>
                {{if $curr_trmt->fin}}
                  Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
                {{elseif $curr_trmt->debut}}
                  Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
                {{/if}}
                <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_trmt->_guid}}', 'objectViewHistory')">
                  {{$curr_trmt->traitement}}
                </span>
              </li>
              {{foreachelse}}
              <li><em>Pas de traitements</em></li>
              {{/foreach}}
            </ul>
            <strong>Diagnostics</strong>
            <ul>
              {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
              <li>
                {{$curr_code->code}}: {{$curr_code->libelle}}
              </li>
              {{foreachelse}}
              <li><em>Pas de diagnostic</em></li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
      </table>
      <!-- Affichage des autres prescriptions -->
      <table class="tbl">
        <tr>
          <th>
          {{if $object->_class_name == "CSejour"}}
            Sejour    
          {{else}}
            Consultation
          {{/if}}
          </th>
        </tr>
        {{if is_array($object->_ref_prescriptions) && $object->_class_name == "CConsultation"}}
          {{assign var=prescription_externe value=$object->_ref_prescriptions.externe}}
          <tr class="selected">
            <td class="text">
              <a href="#" onclick="Prescription.reloadPrescSejour('{{$prescription_externe->_id}}')" >
                {{$prescription_externe->_view}} {{tr}}CPrescription.type.{{$prescription_externe->type}}{{/tr}}
              </a>
            </td>
          </tr> 
        {{/if}}
      </table>
      {{if $object->_class_name == "CSejour"}}
      <table class="tbl">
        <tr>
          <th colspan="2">Liste des prescriptions</th>
        </tr>  
        <!-- Affichage des prescription du sejour -->
        {{foreach from=$object->_ref_prescriptions item=_prescription key=type}}
          <!-- Ne pas afficher les prescriptions de traitements -->
          {{if $type != "traitement"}}
            {{if $_prescription->_id}}
            <tr {{if $_prescription->_id == $prescription->_id}}class="selected"{{/if}}>
                <td class="text">
                  <a href="#" onclick="Prescription.reloadPrescSejour('{{$_prescription->_id}}')" >
                    {{$_prescription->_view}} {{tr}}CPrescription.type.{{$_prescription->type}}{{/tr}}
                  </a>
                </td>
              </tr>
              {{/if}}
           {{/if}}
         {{/foreach}}
      </table>
      {{/if}}
    </td>
    <!-- Affichage de la prescription selectionnée -->
    <td class="greedyPane">
      <div id="prescription_sejour">
        {{assign var=prescriptions value=$object->_ref_prescriptions}}
        
        {{if !$object->_count_prescriptions}}
        <form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
            <input type="hidden" name="m" value="dPprescription" />
            <input type="hidden" name="dosql" value="do_prescription_aed" />
            <input type="hidden" name="prescription_id" value="" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$object->_id}}"/>
            <input type="hidden" name="object_class" value="{{$object->_class_name}}" />
            <input type="hidden" name="callback" value="Prescription.reloadPrescSejour" />
            {{if $object->_class_name == "CConsultation"}}
              <input type="hidden" name="type" value="externe" />
            {{else}}
              <input type="hidden" name="type" value="pre_admission" />
            {{/if}}  
            <button type="button" class="new" onclick="submitFormAjax(this.form, 'systemMsg');">Créer une prescription</button>
          </form>
         {{else}}
           <div class="big-info">
             Veuillez sélectionner une prescription sur la gauche pour la visualiser.
           </div>
         {{/if}}
      </div>
    </td>
  </tr>
  {{/if}}
</table>