<script type="text/javascript">

openScoreIGS = function(igs_id){
  var url = new Url("dPcabinet", "exam_igs");
  url.addParam("sejour_id", "{{$sejour->_id}}");
  url.addParam("exam_igs_id", igs_id);
  url.requestModal();
}  
 
Main.add(function(){
 Control.Tabs.create('tab-fiches');
});

</script>

<ul id="tab-fiches" class="control_tabs small">
  <li><a href="#score_igs">Score IGS</a></li>
</ul>
<hr class="control_tabs" />

<table class="tbl" id="score_igs">
<tr>
  <th class="title" colspan="18">
    <button type="button" style="float: right" class="add" onclick="openScoreIGS()">
      Ajouter un score IGS
    </button>
    Score IGS
  </th>
</tr>



<tr>
  <th style="font-weight: bold; text-align: center;">{{mb_label class="CExamIgs" field="scoreIGS"}}</th>  
  <th class="text">Date</th>
  {{foreach from="CExamIGS"|static:fields item=_field}}
  <th class="text">{{mb_label class="CExamIgs" field=$_field}}</th>
  {{/foreach}}
  <th class="narrow"></th>
</tr>

{{foreach from=$sejour->_ref_exams_igs item=_igs}}
<tr>
  <td style="font-weight: bold; font-size: 1.3em; text-align: center;">
    {{mb_value object=$_igs field="scoreIGS"}}
  </td>  
  <td class="text" style="text-align: center;">
    {{if $_igs->date}}
      {{mb_value object=$_igs field=date}}
    {{else}}
      {{mb_value object=$_igs->_ref_last_log field="date"}}
    {{/if}}
  </td>
  {{foreach from="CExamIGS"|static:fields item=_field}}
  <td class="text {{if $_igs->$_field == ''}}empty{{/if}}" style="text-align: center;">{{mb_value object=$_igs field=$_field}}</td>
  {{/foreach}}
  <td>
    <button type="button" class="edit notext" onclick="openScoreIGS('{{$_igs->_id}}')"></button>
  </td>
</tr>
{{foreachelse}}
<tr>
  <td class="empty" colspan="18">
    Aucun score IGS
  </td>
</tr>
{{/foreach}}
</table>