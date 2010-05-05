var Thumb = {
  thumb_up2date: true,
  choixAffiche: function(isNotModele) {
    $("thumbs").toggle();
    if (isNotModele == 1) {
      $("thumbs_button").toggle();
      var editeur = $("editeur");
    }
    else {
      var editeur = $("htmlarea");
    }
    var colspan_editeur = editeur.readAttribute("colspan");
    colspan_editeur == '1' ? editeur.writeAttribute("colspan",'2') : editeur.writeAttribute("colspan",'1');
  },

  refreshThumbs: function(first_time, compte_rendu_id, modele_id, user_id, mode) {
    this.thumb_up2date = true;

    // TODO: changez en classes CSS
    if (first_time != 1) {
      for (var i = 0; i < Thumb.nb_thumbs; i++) {
        $("thumb_" + i).onclick = null;
      }
     $('mess').onclick = null;
    }
    
    
    $("thumbs").setOpacity(1);
    var form = getForm("editFrm");
    var url = new Url("dPcompteRendu", "ajax_pdf_and_thumbs");
    url.addParam("compte_rendu_id", compte_rendu_id||modele_id);
    
    var content = (window.FCKeditorAPI && FCKeditorAPI.Instances.source.GetHTML()) ? FCKeditorAPI.Instances.source.GetHTML() : $V(form.source);
    
    url.addParam("content", content);
    url.addParam("mode", mode);
		if (mode == "modele") {
			url.addParam("type", $V(form.editFrm_type));
			url.addParam("header_id", $V(form.editFrm_header_id));
			url.addParam("footer_id", $V(form.editFrm_footer_id));
			url.addParam("height", $V(form.editFrm_height));
		}
    url.addParam("stream", 0);
    url.addParam("generate_thumbs", 1);
    url.addParam("first_time", first_time);
    url.addParam("user_id", user_id);
    url.addParam("margins[]",[form.margin_top.value,
                              form.margin_right.value,
                              form.margin_bottom.value,
                              form.margin_left.value]);
    url.addParam("orientation", $V(PageFormat.form._orientation));
    url.addParam("page_format", form._page_format.value);
    url.requestUpdate("thumbs",{method: "post", getParameters: {m: "dPcompteRendu", a: "ajax_pdf_and_thumbs"}});
  },
  old: function() {
    if(!this.thumb_up2date) return;
    var on_click = function(){
      Thumb.refreshThumbs(0, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);
    }
    $("thumbs").setOpacity(0.5);

    for(var i = 0; i < Thumb.nb_thumbs; i++) {
      $("thumb_" + i).onclick = null;
      $("thumb_" + i).observe("click", on_click);
    }

    var mess = $('mess').show();
    mess.observe("click", on_click);
    this.thumb_up2date = false;
  }
}

function FCKeditor_OnComplete(editorInstance) {
  editorInstance.Events.AttachEvent('OnSelectionChange', loadOld);
  Thumb.content = editorInstance.GetHTML(false);

  editorInstance.Events.AttachEvent('OnSelectionChange', FCKeventChanger );
  var fck_iframe = document.getElementById('source___Frame');
  var fck_editing_area = fck_iframe.contentDocument.getElementById('xEditingArea');
  fck_editing_area.style.height = '100.1%';
  setTimeout(function() {fck_editing_area.style.height = '100%'}, 100); 
  Thumb.refreshThumbs(1, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);
}

function loadOld(editorInstance) {
  if (editorInstance.IsDirty() && editorInstance.GetHTML(false) != Thumb.content) {
    Thumb.content = editorInstance.GetHTML(false);
    Thumb.old();
  }
	
}

function FCKeventChanger(editorInstance) {
  if(editorInstance.LastOnChangeTimer) {
    FormObserver.FCKChanged(editorInstance.LastOnChangeTimer);
  }
}