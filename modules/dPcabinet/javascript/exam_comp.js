// $Id: exam_audio.js 8209 2010-03-04 20:01:54Z phenxdesign $

var ExamComp = {
  del: function(form) {
	  form.del.value = "1";
	  ExamComp.submit(form);
	},
	
	toggle: function(form){
	  form.fait.value = (form.fait.value == 1) ? 0 : 1;
	  ExamComp.submit(form);
	},

	submit: function(form) {
	  if (form.examen) {
	    var examen = form.examen.value;
	    var realisation = form.realisation.value;
	  }
		
	  onSubmitFormAjax(form, { onComplete : ExamComp.refresh} );
	  form.reset();
	  if (form.examen){
	    form._hidden_examen.value = examen;
	    form.realisation.value = realisation;
	  }
	},
	
	refresh: function () {
	  var url = new Url("dPcabinet", "httpreq_vw_list_exam_comp");
	  url.addParam("selConsult", document.editFrmFinish.consultation_id.value);
	  url.requestUpdate('listExamComp');
	}

}
