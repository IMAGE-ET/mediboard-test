Affectation = {
  delAffectation: function(affectation_id, lit_id, sejour_guid) {
     var form = getForm("delAffect_"+affectation_id);
     $V(form.affectation_id, affectation_id);
     
     return onSubmitFormAjax(form, {onComplete: function(){
       if (window.refreshMouvements) {
         refreshMouvements(loadNonPlaces, lit_id);
       }
       if (sejour_guid) {
         $("view_affectations").select("."+sejour_guid).each(function(div) {
           var div_lit_id = div.get("lit_id");
           if (div_lit_id != lit_id) {
             refreshMouvements(loadNonPlaces, div_lit_id);
           }
         });
       }
     }});
   }
}
