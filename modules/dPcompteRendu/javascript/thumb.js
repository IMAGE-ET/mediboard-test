var Thumb = {
  compte_rendu_id: 0,
  file_id: 0,
  thumb_up2date: true,
  thumb_refreshing: false,
	nb_thumbs: 0,
	first_time: 1,
	changed: false,
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
    this.thumb_refreshing = true;

    // TODO: changer en classes CSS
    if (first_time != 1) {
      for (var i = 0; i < Thumb.nb_thumbs; i++) {
        $("thumb_" + i).stopObserving("click");
      }
			if ($('mess'))
      $('mess').stopObserving("click");
    }
    
    $("thumbs").setOpacity(1);
    var form = getForm("editFrm");
    var url = new Url("dPcompteRendu", "ajax_pdf_and_thumbs");
    url.addParam("compte_rendu_id", compte_rendu_id || modele_id);
    
    var content = '';
    
    if (window.CKEDITOR && CKEDITOR.instances.htmlarea.getData) {
      restoreStyle();
      content = CKEDITOR.instances.htmlarea.getData();
      deleteStyle();
    } else {
      content = $V(form._source);
    }
    
    url.addParam("content", encodeURIComponent(content));
    url.addParam("mode", mode);
    
    if (mode == "modele") {
      url.addParam("type",      $V(form.elements.type));
      url.addParam("header_id", $V(form.elements.header_id));
      url.addParam("footer_id", $V(form.elements.footer_id));
      url.addParam("height",    $V(form.elements.height));
    }
    
    url.addParam("stream", 0);
    url.addParam("generate_thumbs", 1);
    url.addParam("first_time", first_time);
    url.addParam("user_id", user_id);
    url.addParam("margins[]",[form.elements.margin_top.value,
                              form.elements.margin_right.value,
                              form.elements.margin_bottom.value,
                              form.elements.margin_left.value]);
                              
    url.addParam("orientation", $V(PageFormat.form._orientation));
    url.addParam("page_format", form.elements._page_format.value);
    url.addParam("page_width",  form.elements.page_width.value);
    url.addParam("page_height", form.elements.page_height.value);
    
    url.requestUpdate("thumbs", {
      method: "post",
      getParameters: {
        m: "dPcompteRendu", 
        a: "ajax_pdf_and_thumbs"
      },
      onComplete: function() {
          Thumb.thumb_refreshing = false;
          if(!Thumb.thumb_up2date) {
            Thumb.thumb_up2date = true;
            Thumb.old();
          }
          else {
          Thumb.init();
         }
      }
    });
  },
  old: function() {
    if (window.pdf_thumbnails == 1) {
      if (this.thumb_refreshing) {
        this.thumb_up2date = false;
        return;
      }
      var on_click = function(){
    	  CKEDITOR.instances.htmlarea.on("keypress", loadOld);
    	  Thumb.changed = true;
        Thumb.first_time = 0;
        Thumb.refreshThumbs(0, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);
      }
      
      $$(".thumb").each(function(t, i){
        t.stopObserving("click").observe("click", on_click);
      });
      var mess = $('mess');
      if (this.thumb_up2date && mess) {
        $("thumbs").setOpacity(0.5);
        mess.show();
        mess.stopObserving("click");
        mess.observe("click", on_click);
      }
    }
  },
  init: function(){
    $$(".thumb").each(function(t, i) {
      t.stopObserving("click").
        observe("click", function(){
        (new Url).ViewFilePopup('CCompteRendu', Thumb.compte_rendu_id || Thumb.modele_id, 'CFile', Thumb.file_id, i);
      });
    });
  }
}


function loadOld() {
  var instance = CKEDITOR.instances.htmlarea;
  if (!instance.checkDirty()) return;
  var html = instance.getData();
  if (html != Thumb.content) {
    Thumb.changed = true;
    instance.removeListener("keypress", loadOld);
    if (Thumb.modele_id == 0 || Thumb.modele_id == null)
      instance.getCommand("save").setState(CKEDITOR.TRISTATE_OFF);
    Thumb.content = html;
    Thumb.old();
  }
}

function restoreStyle() {
  var instance = CKEDITOR.instances.htmlarea;
  
  if (!window.save_style) return;
  window.save_style.insertBefore(instance.document.getBody().getFirst());
}

function deleteStyle() {
  var instance = CKEDITOR.instances.htmlarea;
  var styleTag = instance.document.getBody().getFirst();
  if (styleTag.$.tagName == "STYLE")
    styleTag.remove();
}