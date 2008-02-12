// $Id: $

var ExamDialog = {
  sForm            : null,
  sConsultId       : null,
  options : {
    width : 800,
    height: 600
  },

  // Ouverture de la popup en fonction du type d'examen
  pop: function(type_exam) {
    var oForm = document[this.sForm];     
    var url = new Url();
    url.setModuleAction("dPcabinet", type_exam);
    url.addParam("consultation_id", oForm[this.sConsultId].value);
    url.popup(this.options.width, this.options.height, type_exam);
  }, 
  
  // Reload
  reload: function(){
    var oForm = document[this.sForm];     
    var url = new Url();
    url.setModuleAction("dPcabinet", "httpreq_vw_examens_comp");
    url.addParam("consultation_id", oForm[this.sConsultId].value);
    url.requestUpdate("exam_comp", { waitingText: null } );
  }
}