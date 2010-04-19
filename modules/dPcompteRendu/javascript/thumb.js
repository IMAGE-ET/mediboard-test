var Thumb = {
  thumb_up2date: true,
  choixaffiche: function() {
    $("thumbs").toggle();
    $("thumbs_button").toggle();
    var editeur = $("editeur");
    var colspan_editeur = editeur.readAttribute("colspan");
    colspan_editeur == '1' ? editeur.writeAttribute("colspan",'2') : editeur.writeAttribute("colspan",'1');
  },

  refreshthumbs: function(first_time, compte_rendu_id, modele_id, user_id, mode) {
    this.thumb_up2date = true;
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
    
    var thumb_0 = $("thumb_0");
    var thumbs = $("thumbs"); 
    
    thumbs.setOpacity(0.5);
    thumb_0.onclick = null;
    var mess = new Element('div', {id: 'mess', style: 'position: absolute; width: 160px; font-size: 12pt; font-weight: bold;'}).update("<br/><br/>Vignettes obsolètes : cliquez sur le bouton pour réactualiser.<br/>");
    mess = mess.insert( {bottom: new Element('button', {id: 'refresh', class: 'change notext', type: 'button', title: 'Rafraîchir les vignettes', onclick: 'Thumb.refreshthumbs(0, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);'})});
    thumbs.insert({top: mess});
    this.thumb_up2date = false;
  }
}

function FCKeditor_OnComplete(editorInstance) {
  editorInstance.Events.AttachEvent('OnSelectionChange', loadold);
  Thumb.content = editorInstance.GetHTML(false);

  editorInstance.Events.AttachEvent('OnSelectionChange', FCKeventChanger );
  var fck_iframe = document.getElementById('source___Frame');
  var fck_editing_area = fck_iframe.contentDocument.getElementById('xEditingArea');
  fck_editing_area.style.height = '100.1%';
  setTimeout(function() {fck_editing_area.style.height = '100%'}, 100); 
  Thumb.refreshthumbs(1, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);
}

function loadold(editorInstance) {
  if (editorInstance.IsDirty() && editorInstance.GetHTML(false) != Thumb.content) {
    Thumb.old();
  }
}

function FCKeventChanger(editorInstance) {
  if(editorInstance.LastOnChangeTimer) {
    FormObserver.FCKChanged(editorInstance.LastOnChangeTimer);
  }
}