var Thumb = {
  compte_rendu_id: 0,
  file_id: 0,
  thumb_up2date: true,
  thumb_refreshing: false,
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
      $('mess').stopObserving("click");
    }

    $("thumbs").setOpacity(1);
    var form = getForm("editFrm");
    var url = new Url("dPcompteRendu", "ajax_pdf_and_thumbs");
    url.addParam("compte_rendu_id", compte_rendu_id || modele_id);
    
    var content = (window.FCKeditorAPI && FCKeditorAPI.Instances._source.GetHTML()) ? FCKeditorAPI.Instances._source.GetHTML() : $V(form._source);
    
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
        Main.add(function() {
          Thumb.thumb_refreshing = false;
          if(!Thumb.thumb_up2date) {
            Thumb.thumb_up2date = true;
            Thumb.old();
          }
          else {
          Thumb.init();
        }
      })}
    });
  },
  old: function() {
    if (window.pdf_thumbnails == 1) {
      if (this.thumb_refreshing) {
        this.thumb_up2date = false;
        return;
      }

      var on_click = function(){
        FCKeditorAPI.GetInstance('_source').Events.AttachEvent('OnSelectionChange', loadOld);
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

function FCKeditor_OnComplete(editorInstance){
  var boutons = editorInstance.EditorWindow.parent.FCKToolbarItems.LoadedItems;
  
  // Rajout du raccourci clavier dans la tooltip des boutons de FCKEditor.
  $A(editorInstance.Config.Keystrokes).each(function(k){
    if (k[1] === true) 
      return;
    var key = k[0];
    var title = " (";
    var mac = navigator.userAgent.match(/mac/i);
    if (key > 4000){
      key -= 4000;
      title += "ALT + ";
    }
    if (key > 2000){
      key -= 2000;
      title += "SHIFT + ";
    }
    if (key > 1000) {
      key -= 1000;
      if(mac) {
        title += String.fromCharCode(8984);
      } else {
        title += "CTRL + ";
      }
    }
    if (boutons[k[1]] && ((boutons[k[1]]._UIButton.MainElement.title).match(/\(/i)) == null) {
      var char_from_key = String.fromCharCode(key) 
      if (key == 13) {
        char_from_key = "Entrée";            
      }
      boutons[k[1]]._UIButton.MainElement.title += title + char_from_key + ")";
      }
  });
  
  var fck_iframe = document.getElementById('_source___Frame');

  var fck_editing_area = fck_iframe.contentDocument.getElementById('xEditingArea');
  
  fck_editing_area.style.height = '100.1%';
  setTimeout(function(){
    fck_editing_area.style.height = '100%'
  }, 100);
  
  if (window.pdf_thumbnails == 1) {
    Thumb.content = editorInstance.GetHTML(false);
    Thumb.refreshThumbs(1, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);
    editorInstance.Events.AttachEvent('OnSelectionChange', loadOld);
    
  }
  // Don't close the window with escape
  document.stopObserving('keydown', closeWindowByEscape);
  
  // Don't allow escape or alt+f4 to cancel the request
  document.observe('keydown', function(e){
    var keycode = Event.key(e);
    if (keycode == 27 || keycode == 115 && e.altKey) {
      return Event.stop(e);
    }
    // Catches Ctrl+s and Command+s
    if (keycode == 83 && (e.ctrlKey || e.metaKey)) {
      submitCompteRendu();
      Event.stop(e);
    }
    if (window.pdf_thumbnails == 1) {
      if (keycode == 80 && (e.ctrlKey || e.metaKey)) {
        editorInstance.Commands.GetCommand("mbPrintPDF").Execute();
        Event.stop(e);
      }
    }
  });
}

function loadOld(editorInstance) {
  if (!editorInstance.IsDirty()) return;
  
  var html = editorInstance.GetHTML(false);
  if (html != Thumb.content) {
    Thumb.content = html;
    Thumb.old();
    FormObserver.FCKChanged(editorInstance.LastOnChangeTimer);
    editorInstance.Events.FireEvent('OnSelectionChange', '');
  }
}

function emptyPDFonChanged(){
  FormObserver.onChanged = function(){
    var f = getForm("download-pdf-form");
    var url = new Url();
    url.addParam("m", "dPcompteRendu");
    url.addParam("dosql", "do_modele_aed");
    url.addParam("_do_empty_pdf", 1);
    url.addParam("compte_rendu_id", f.compte_rendu_id.value);
    url.requestJSON(function(){}, {method: "post"});
  }
}

function resizeEditor() {
  var dims = document.viewport.getDimensions();
  var greedyPane = $$(".greedyPane")[0]; 
  greedyPane.style.height = (dims["height"] - greedyPane.cumulativeOffset().top - 10) +"px";
  if (window.pdf_thumbnails == 1)
    $("thumbs").style.height = (dims["height"] - greedyPane.cumulativeOffset().top - 10) +"px";
}

Event.observe(window, "resize", function(e){
  resizeEditor();
});