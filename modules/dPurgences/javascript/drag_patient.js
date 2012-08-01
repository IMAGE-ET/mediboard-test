var element_select;
var zone_select;
Main.add(function(){
  var elements;
  //Tous les éléments draggables
  elements=$$('div.patient');
  elements.each(function(e) {
    new Draggable( e, {  revert:true, 
              scroll: window, 
              ghosting: true});
  });
  
  //Toutes les zones droppables
  elements=$$('td.chambre');
  elements.each(function(e) {
      Droppables.add(e,{onDrop:TraiterDrop});      
  });
});

function TraiterDrop(element, zoneDrop)
{
  if(zoneDrop.get("chambre-id")!=element.parentNode.get("chambre-id")){
    element_select = element;
    zone_select = zoneDrop;
    var nb_chambres_libres = parseInt(zoneDrop.getAttribute("data-nb-lits")) - parseInt(zoneDrop.select('div.patient').length);
    
    if(nb_chambres_libres >= 2){
      element.style.width = "92%";
      ChoiceLit.edit(zoneDrop.get("chambre-id"), element.get("patient-id"), getForm("changeDate").date.value);    
    }
    else{
      element.style.width = "92%";
      ChoiceLit.submitRPU(zoneDrop.get("lit-id"));
    }
  }  
}

ChoiceLit  = {
  modal: null,
  edit: function(chambre_id, patient_id, date) {
    var url = new Url("dPhospi", "ajax_choice_lit");
    url.addParam("chambre_id", chambre_id);
    url.addParam("patient_id", patient_id);
    url.addParam("date", date);
    url.requestModal(); 
    this.modal = url.modalObject;
  },
  finish: function(lit_id) {
    ChoiceLit.submitRPU(lit_id);
    return ChoiceLit.modal.close();
  },

  submitRPU: function(lit_id) {
    zone_select.appendChild(element_select);
    var guid = element_select.get("rpu-guid");
    var form = getForm(guid);
    form.box_id.value = lit_id;
    return onSubmitFormAjax(form);
  }
};