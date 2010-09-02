{{assign var=consultation value=$exam_possum->_ref_consult}}

<script type="text/javascript">

// Lancement du reload
window.opener.ExamDialog.reload('{{$consultation->_id}}');

var listScorePhysio = {{$exam_possum->_score_possum_physio|@json}};
var listScoreOper   = {{$exam_possum->_score_possum_oper|@json}};
var scorePhysio = {{$exam_possum->_score_physio}};
var scoreOper   = {{$exam_possum->_score_oper}};
</script>

{{mb_include_script module=$m script="exam_possum"}}


<form name="editFrmPossum" action="?m=dPcabinet&amp;a=exam_possum&amp;dialog=1" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="dosql" value="do_exam_possum_aed" />
<input type="hidden" name="del" value="0" />
{{mb_key   object=$exam_possum}}
{{mb_field object=$exam_possum field="consultation_id" hidden=1}}

<table class="form">
  <tr>
    {{if $exam_possum->_id}} 
      <th class="title modify text" colspan="6">
        {{mb_include module=system template=inc_object_idsante400 object=$exam_possum}}
        {{mb_include module=system template=inc_object_history    object=$exam_possum}}
        {{mb_include module=system template=inc_object_notes      object=$exam_possum}}

        Consultation de '{{$consultation->_ref_patient}}'<br />
        le {{$consultation->_date|date_format:$dPconfig.longdate}}
        par le Dr {{$consultation->_ref_chir}}
      </th>
    {{else}}
      <th class="title text" colspan="6">
        Consultation de '{{$consultation->_ref_patient}}'<br />
        le {{$consultation->_date|date_format:$dPconfig.longdate}}
        par le Dr {{$consultation->_ref_chir}}
      </th>
    {{/if}}
  </tr>

  <tr>
    <th class="title" colspan="6">Score Physiologique : <div id="score_physio">{{$exam_possum->_score_physio}}</div></th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="age"}}</th>
    <td>
      {{mb_field object=$exam_possum field="age" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="kaliemie"}}</th>
    <td>
      {{mb_field object=$exam_possum field="kaliemie" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="signes_respiratoires"}}</th>
    <td>
      {{mb_field object=$exam_possum field="signes_respiratoires" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="uree"}}</th>
    <td>
      {{mb_field object=$exam_possum field="uree" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="natremie"}}</th>
    <td>
      {{mb_field object=$exam_possum field="natremie" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="signes_cardiaques"}}</th>
    <td>
      {{mb_field object=$exam_possum field="signes_cardiaques" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="hb"}}</th>
    <td>
      {{mb_field object=$exam_possum field="hb" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="freq_cardiaque"}}</th>
    <td>
      {{mb_field object=$exam_possum field="freq_cardiaque" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="ecg"}}</th>
    <td>
      {{mb_field object=$exam_possum field="ecg" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="leucocytes"}}</th>
    <td>
      {{mb_field object=$exam_possum field="leucocytes" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <td colspan="2"></td>
    <th>{{mb_label object=$exam_possum field="pression_arterielle"}}</th>
    <td>
      {{mb_field object=$exam_possum field="pression_arterielle" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="6">Glasgow</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="ouverture_yeux"}}</th>
    <td>
      {{mb_field object=$exam_possum field="ouverture_yeux" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="rep_verbale"}}</th>
    <td>
      {{mb_field object=$exam_possum field="rep_verbale" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="rep_motrice"}}</th>
    <td>
      {{mb_field object=$exam_possum field="rep_motrice" defaultOption="&mdash;" onchange="calculPhysio()"}}
    </td>  
  </tr>

  <tr><td colspan="6" style="height:30px"></td></tr>

  <tr>
    <th class="title" colspan="6">Score Opératoire : <div id="score_oper">{{$exam_possum->_score_oper}}</div></th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="gravite"}}</th>
    <td>
      {{mb_field object=$exam_possum field="gravite" defaultOption="&mdash;" onchange="calculOper()"}}
    </td>  
    <th>{{mb_label object=$exam_possum field="nb_interv"}}</th>
    <td>
      {{mb_field object=$exam_possum field="nb_interv" defaultOption="&mdash;" onchange="calculOper()"}}
    </td>  
    <th>{{mb_label object=$exam_possum field="pertes_sanguines"}}</th>
    <td>
      {{mb_field object=$exam_possum field="pertes_sanguines" defaultOption="&mdash;" onchange="calculOper()"}}
    </td>  
  </tr>
  
  <tr>
    <th>{{mb_label object=$exam_possum field="contam_peritoneale"}}</th>
    <td>
      {{mb_field object=$exam_possum field="contam_peritoneale" defaultOption="&mdash;" onchange="calculOper()"}}
    </td> 
    <th>{{mb_label object=$exam_possum field="cancer"}}</th>
    <td>
      {{mb_field object=$exam_possum field="cancer" defaultOption="&mdash;" onchange="calculOper()"}}
    </td>
    <th>{{mb_label object=$exam_possum field="circonstances_interv"}}</th>
    <td>
      {{mb_field object=$exam_possum field="circonstances_interv" defaultOption="&mdash;" onchange="calculOper()"}}
    </td>
  </tr>
  
  <tr><td colspan="6" style="height:30px"></td></tr>
  
  <tr>
    <th class="title" colspan="6">Résultats</th>
  </tr>
  
  <tr>
    <th colspan="2"><strong>Morbidité</strong></th>
    <td id="morbidite">{{$exam_possum->_morbidite}} %</td>
    <th colspan="2"><strong>Mortalité</strong></th>
    <td id="mortalite">{{$exam_possum->_mortalite}} %</td>
  </tr>
  <tr>
    <td class="button" colspan="6">
      {{if $exam_possum->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'cet examen complementaire'})">{{tr}}Delete{{/tr}}</button>
      {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>

</form>