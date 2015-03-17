{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{assign var=consultation value=$object}}
{{assign var=patient value=$object->_ref_patient}}

<table class="tbl">
  <tr>
    <th colspan="2">
      {{mb_include module=system template=inc_object_notes     }}
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history   }}
      {{$object}}
    </th>
  </tr>
  <tr>
    <td rowspan="3" style="width: 1px;">
      {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$patient size=50}}
    </td>
    <td>
      <strong>{{mb_value object=$patient}}</strong>
    </td>
  </tr>
  <tr>
    <td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_chir}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_value object=$object field=_datetime}}
    </td>
  </tr>
  {{if $object->_ref_categorie->_id}}
    <tr>
      <td colspan="2">
        Catégorie : 
        {{mb_include module=cabinet template=inc_icone_categorie_consult categorie=$object->_ref_categorie display_name=true}}
      </td>
    </tr>
  {{/if}}
  <tr>
    <td colspan="2" class="text">
      {{mb_value object=$object field=motif}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      {{mb_value object=$object field=rques}}
    </td>
  </tr>
  
  {{if $consultation->annule == 1}}
    <tr>
      <th class="category cancelled" colspan="2">
      {{tr}}CConsultation-annule{{/tr}}
      </th>
    </tr>
  {{/if}}


  {{if $object->_can->edit}}
  <tr>
    <td class="button" colspan="2">
      {{mb_script module="dPcabinet" script="edit_consultation" ajax="true"}}
      <button type="button" class="change" onclick="Consultation.editRDVModal('{{$consultation->_id}}')">
        {{tr}}Rendez-vous{{/tr}}
      </button>
        <button type="button" class="edit" onclick="Consultation.editModal('{{$consultation->_id}}')">
          {{tr}}CConsultation{{/tr}}
        </button>
        <button type="button" class="edit" onclick="Consultation.editModal('{{$consultation->_id}}',  'reglement')">
          {{tr}}Reglement{{/tr}}
        </button>

        {{if $consultation->chrono != 64 && $consultation->_date == $dnow}}
          <form method="post" name="finish_consult_{{$consultation->_id}}" onsubmit="return onSubmitFormAjax(this, {})">
            <input type="hidden" name="chrono" value="64"/>
            {{mb_key object=$consultation}}
            {{mb_class object=$consultation}}
            <button type="button" class="tick" onclick="this.form.onsubmit();">
              Terminer
            </button>
          </form>
        {{/if}}

      {{if @$modules.brancardage->_can->read && $consultation->sejour_id}}
        {{mb_script module=brancardage script=creation_brancardage ajax="true"}}
        {{assign var=service_id value=$consultation->_ref_sejour->service_id}}
        <div id="demande_brancard-{{$consultation->sejour_id}}" style="float: right;">
          <form name="changeItemBrancard" method="post" action="">
            <input type="hidden" name="brancardage_item_id" value="" />
            <input type="hidden" name="@class" value="CBrancardageItem" />
            <input type="hidden" name="brancardage_id" value="{{$consultation->_ref_brancardage->_id}}" />
            <input type="hidden" name="demande_brancard" value="now" />
          </form>
          {{mb_include module=brancardage template=inc_exist_brancard colonne="demande_brancard" sejour_id=$consultation->sejour_id
          brancardage=$consultation->_ref_brancardage see_sejour=true destination="CService" destination_guid="CService-$service_id"}}
        </div>
      {{/if}}
    </td>
  </tr>
  {{/if}}
</table>

{{if $object->_can->edit}}

{{mb_include module=cabinet template=inc_list_actes_ccam subject=$consultation vue=view}}
{{mb_include module=cabinet template=inc_list_actes_ngap subject=$consultation }}
 
{{assign var=examaudio value=$consultation->_ref_examaudio}}
{{if $examaudio && $examaudio->_id}}
  <script type="text/javascript">
    newExam = function(sAction, consultation_id) {
      if (sAction) {
        var url = new Url("dPcabinet", sAction);
        url.addParam("consultation_id", consultation_id);
        url.popup(900, 600, "Examen");  
      }
    }
  </script>
  <a href="#{{$examaudio->_guid}}" onclick="newExam('exam_audio', '{{$consultation->_id}}')">
    <strong>Audiogramme</strong>
  </a>
{{/if}}

{{/if}}
