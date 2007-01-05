<script type="text/javascript">
var listScorePhysio = {{$exam_possum->_score_possum_physio|@json}};
var listScoreOper   = {{$exam_possum->_score_possum_oper|@json}};
var scorePhysio = {{$exam_possum->_score_physio}};
var scoreOper   = {{$exam_possum->_score_oper}};
</script>
<script type="text/javascript" src="modules/{{$m}}/javascript/exam_possum.js?build={{$mb_version_build}}"></script>

<form name="editFrmPossum" action="?m=dPcabinet&amp;a=exam_possum&amp;dialog=1" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="dosql" value="do_exam_possum_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="exampossum_id" value="{{$exam_possum->exampossum_id}}" />
<input type="hidden" name="consultation_id" value="{{$exam_possum->consultation_id}}" />

<table class="form">
  <tr>
    <th class="title" colspan="6">
      Consultation de <span style="color:#f00;">{{$exam_possum->_ref_consult->_ref_patient->_view}}</span>
      le {{$exam_possum->_ref_consult->_date|date_format:"%A %d/%m/%Y"}}
      par le Dr. {{$exam_possum->_ref_consult->_ref_chir->_view}}
    </th>
  </tr>

  <tr>
    <th class="title" colspan="6">Score Physiologique : <div id="score_physio">{{$exam_possum->_score_physio}}</div></th>
  </tr>
  
  <tr>
    <th><label for="age" title="Age du patient">Age</label></th>
    <td>
      <select name="age" title="{{$exam_possum->_props.age}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->age options=$exam_possum->_enumsTrans.age}}
      </select>
    </td>
    <th><label for="kaliemie" title="Kaliémie (mEql/L)">Kaliémie</label></th>
    <td>
      <select name="kaliemie" title="{{$exam_possum->_props.kaliemie}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->kaliemie options=$exam_possum->_enumsTrans.kaliemie}}
      </select>
    </td>
    <th><label for="signes_respiratoires" title="Signes respiratoires">Signes respiratoires</label></th>
    <td>
      <select name="signes_respiratoires" title="{{$exam_possum->_props.signes_respiratoires}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->signes_respiratoires options=$exam_possum->_enumsTrans.signes_respiratoires}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="uree" title="Urée">Urée</label></th>
    <td>
      <select name="uree" title="{{$exam_possum->_props.uree}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->uree options=$exam_possum->_enumsTrans.uree}}
      </select>
    </td>
    <th><label for="natremie" title="Natrémie (mEql/L)">Natrémie</label></th>
    <td>
      <select name="natremie" title="{{$exam_possum->_props.natremie}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->natremie options=$exam_possum->_enumsTrans.natremie}}
      </select>
    </td>
    <th><label for="signes_cardiaques" title="Signes cardiaques">Signes cardiaques</label></th>
    <td>
      <select name="signes_cardiaques" title="{{$exam_possum->_props.signes_cardiaques}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->signes_cardiaques options=$exam_possum->_enumsTrans.signes_cardiaques}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="hb" title="Hb (g/dL)">Hb</label></th>
    <td>
      <select name="hb" title="{{$exam_possum->_props.hb}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->hb options=$exam_possum->_enumsTrans.hb}}
      </select>
    </td>
    <th><label for="freq_cardiaque" title="Fréquence cardiaque">Fréquence cardiaque</label></th>
    <td>
      <select name="freq_cardiaque" title="{{$exam_possum->_props.freq_cardiaque}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->freq_cardiaque options=$exam_possum->_enumsTrans.freq_cardiaque}}
      </select>
    </td>
    <th><label for="ecg" title="ECG">ECG</label></th>
    <td>
      <select name="ecg" title="{{$exam_possum->_props.ecg}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->ecg options=$exam_possum->_enumsTrans.ecg}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th><label for="leucocytes" title="Leucocytes">Leucocytes</label></th>
    <td>
      <select name="leucocytes" title="{{$exam_possum->_props.leucocytes}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->leucocytes options=$exam_possum->_enumsTrans.leucocytes}}
      </select>
    </td>
    <td colspan="2"></td>
    <th><label for="pression_arterielle" title="Pression Arterielle">Pression Arterielle</label></th>
    <td>
      <select name="pression_arterielle" title="{{$exam_possum->_props.pression_arterielle}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->pression_arterielle options=$exam_possum->_enumsTrans.pression_arterielle}}
      </select>
    </td>
  </tr>
  
  <tr>
    <th class="category" colspan="6">Glasgow</th>
  </tr>
  
  <tr>
    <th><label for="ouverture_yeux" title="Ouverture des yeux">Ouverture des yeux</label></th>
    <td>
      <select name="ouverture_yeux" title="{{$exam_possum->_props.ouverture_yeux}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->ouverture_yeux options=$exam_possum->_enumsTrans.ouverture_yeux}}
      </select>
    </td>
    <th><label for="rep_verbale" title="Réponse verbale">Réponse verbale</label></th>
    <td>
      <select name="rep_verbale" title="{{$exam_possum->_props.rep_verbale}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->rep_verbale options=$exam_possum->_enumsTrans.rep_verbale}}
      </select>
    </td>
    <th><label for="rep_motrice" title="Meilleure réponse motrice">Réponse motrice</label></th>
    <td>
      <select name="rep_motrice" title="{{$exam_possum->_props.rep_motrice}}" onchange="calculPhysio()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->rep_motrice options=$exam_possum->_enumsTrans.rep_motrice}}
      </select>
    </td>  
  </tr>

  <tr><td colspan="6" style="height:30px"></td></tr>

  <tr>
    <th class="title" colspan="6">Score Opératoire : <div id="score_oper">{{$exam_possum->_score_oper}}</div></th>
  </tr>
  
  <tr>
    <th><label for="gravite" title="Gravité de l'intervention">Gravité</label></th>
    <td>
      <select name="gravite" title="{{$exam_possum->_props.gravite}}" onchange="calculOper()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->gravite options=$exam_possum->_enumsTrans.gravite}}
      </select>
    </td>  
    <th><label for="nb_interv" title="Nombre d'interventions">Nombre d'interventions</label></th>
    <td>
      <select name="nb_interv" title="{{$exam_possum->_props.nb_interv}}" onchange="calculOper()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->nb_interv options=$exam_possum->_enumsTrans.nb_interv}}
      </select>
    </td>  
    <th><label for="pertes_sanguines" title="Pertes sanguines">Pertes sanguines</label></th>
    <td>
      <select name="pertes_sanguines" title="{{$exam_possum->_props.pertes_sanguines}}" onchange="calculOper()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->pertes_sanguines options=$exam_possum->_enumsTrans.pertes_sanguines}}
      </select>
    </td>  
  </tr>
  
  <tr>
    <th><label for="contam_peritoneale" title="Contamination péritonéale">Contamination</label></th>
    <td>
      <select name="contam_peritoneale" title="{{$exam_possum->_props.contam_peritoneale}}" onchange="calculOper()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->contam_peritoneale options=$exam_possum->_enumsTrans.contam_peritoneale}}
      </select>
    </td> 
    <th><label for="cancer" title="Cancer">Cancer</label></th>
    <td>
      <select name="cancer" title="{{$exam_possum->_props.cancer}}" onchange="calculOper()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->cancer options=$exam_possum->_enumsTrans.cancer}}
      </select>
    </td>
    <th><label for="circonstances_interv" title="Circonstances de l'intervention">Circonstances</label></th>
    <td>
      <select name="circonstances_interv" title="{{$exam_possum->_props.circonstances_interv}}" onchange="calculOper()">
        <option value="">&mdash;</option>
        {{html_options selected=$exam_possum->circonstances_interv options=$exam_possum->_enumsTrans.circonstances_interv}}
      </select>
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
      {{if $exam_possum->exampossum_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'cet examen complementaire',target:'systemMsg'})">{{tr}}Delete{{/tr}}</button>
      {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>
</table>

</form>