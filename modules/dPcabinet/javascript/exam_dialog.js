// $Id: $

var ExamDialog = {
  sForm            : null,
  sConsultId       : null,
  sCallback        : null,
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

  // Lancement de la fonction de callback
  callback: function() {
    var oForm = document[this.sForm];
    var callback_function = oForm[this.sCallback].value;
    window[callback_function]();
  }
}