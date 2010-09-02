<form name="newExamen" action="?m=dPcabinet">
			            
<select name="type_examen" onchange="ExamDialog.init(this.value)" style="float:right">
  <option value="">&mdash; Choisir un type d'examen</option>
  {{if $_is_anesth}}
    <option value="exam_possum">Score Possum</option>
    <option value="exam_nyha">Classification NYHA</option>
    <option value="exam_igs">Score IGS</option>
  {{else}}
    <option value="exam_audio">Audiogramme</option>          
  {{/if}}
</select>

<label for="type_examen" title="Type d'examen complémentaire à effectuer"><strong>Fiches d'examens</strong></label>
<input type="hidden" name="consultation_id" value="{{$consult->_id}}" />

<script type="text/javascript">
   ExamDialog.init = function(type_exam){
     this.sForm      = "newExamen";
     this.sConsultId = "consultation_id";
     this.pop(type_exam);
	 }
</script>
</form>

<ul>
  {{if !$consult->_count_fiches_examen}}
  <li>
    <em>Aucune fiche complémentaire</em>
  </li>
  {{/if}}
	{{assign var=examaudio value=$consult->_ref_examaudio}}
  {{if $examaudio->_id}}
  <li>    
    <form name="Delete-{{$examaudio->_guid}}" action="?m=dPcabinet" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_exam_audio_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_key   object=$examaudio}}
      {{mb_field object=$examaudio field="_view" hidden=1}}
      <input type="hidden" name="_conduction" value="" />
      <input type="hidden" name="_oreille" value="" />
      <button class="trash notext" type="button" onclick="ExamDialog.remove(this,'{{$consult->_id}}')">{{tr}}Delete{{/tr}}</button>
    </form>
    <a href="#nothing" onclick="ExamDialog.init('exam_audio');">
      Audiogramme
    </a>
  </li>
  {{/if}}
  {{if $consult->_ref_exampossum->_id}}
  <li>   
    <a href="#nothing" onclick="ExamDialog.init('exam_possum');">
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