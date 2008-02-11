{{mb_include_script module="dPcabinet" script="exam_dialog"}}

<form name="newExamen" action="?m=dPcabinet">
			            
<label for="type_examen" title="Type d'examen complémentaire à effectuer"><strong>Fiches d'examens</strong></label>
<input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
<input type="hidden" name="callback" value="{{$callback}}" />

<select name="type_examen" onchange="ExamDialog.init(this.value)">
  <option value="">&mdash; Choisir un type d'examen</option>
  {{if $_is_anesth}}
    <option value="exam_possum">Score Possum</option>
    <option value="exam_nyha">Classification NYHA</option>
    <option value="exam_igs">Score IGS</option>
  {{else}}
    <option value="exam_audio">Audiogramme</option>          
  {{/if}}
</select>
<script type="text/javascript">
   ExamDialog.init = function(type_exam){
     this.sForm      = "newExamen";
     this.sConsultId = "consultation_id";
     this.sCallback  = "callback";
     this.pop(type_exam);
	 }
</script>
</form>

<ul>
  {{if !$consult->_ref_examaudio->_id && !$consult->_ref_examnyha->_id && !$consult->_ref_exampossum->_id && !$consult->_ref_examigs}}
  <li>
    Aucun examen
  </li>
  {{/if}}
  {{if $consult->_ref_examaudio->_id}}
  <li>    
    <a href="#nothing" onclick="ExamDialog.init('exam_audio');">Audiogramme</a>
    <form name="delFrm{{$consult->_ref_examaudio->_id}}" action="?m=dPcabinet" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_exam_audio_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_field object=$consult->_ref_examaudio field="_view" hidden=1 prop=""}}
      {{mb_field object=$consult->_ref_examaudio field="examaudio_id" hidden=1 prop=""}}
      <input type="hidden" name="_conduction" value="" />
      <input type="hidden" name="_oreille" value="" />
      <button class="trash notext" type="button" onclick="confirmFileDeletion(this)">{{tr}}Delete{{/tr}}</button>
    </form>
  </li>
  {{/if}}
  {{if $consult->_ref_exampossum->_id}}
  <li>   
    <a href="#nothing" onclick="ExamDialog.init('exam_possum');" />
      {{$consult->_ref_exampossum->_view}}
    </a>
  </li>
  {{/if}}
  {{if $consult->_ref_examnyha->_id}}
  <li>
    <a href="#nothing" onclick="ExamDialog.init('exam_nyha');">
      {{$consult->_ref_examnyha->_view}}
    </a>
  </li>
  {{/if}}
  {{if $consult->_ref_examigs->_id}}
  <li>
    <a href="#nothing" onclick="ExamDialog.init('exam_igs');">
      {{$consult->_ref_examigs->_view}}
    </a>
  </li>
  {{/if}}
</ul>