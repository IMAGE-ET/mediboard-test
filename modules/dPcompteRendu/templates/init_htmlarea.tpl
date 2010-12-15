{{if $templateManager->editor == "ckeditor" }}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{mb_include_script path="lib/ckeditor/ckeditor.js"}}

<style type="text/css">
/* Ugly hack to display labels with plugin buttons */
  .cke_button_mbfields span.cke_label{display:inline !important}
  .cke_button_mbhelpers span.cke_label{display:inline !important}
  .cke_button_mbfreetext span.cke_label{display:inline !important}
  .cke_button_mbfreetext span.cke_label{display:inline !important}
  .cke_button_mblists span.cke_label{display:inline !important}
</style>

<script type="text/javascript">

function initCKEditor() {
  window.old_source = $("htmlarea").value;
	var editor = CKEDITOR.replace("htmlarea", {customConfig: "../../?m=dPcompteRendu&a=mb_fckeditor&suppressHeaders=1"});
	editor.on("instanceReady", function(e) {
    
    window.resizeEditor = function () {
      var dims = document.viewport.getDimensions();
      var greedyPane = $$(".greedyPane")[0];
      
      CKEDITOR.instances.htmlarea.resize('', (dims["height"] - greedyPane.cumulativeOffset().top - 10)); 
      if (window.pdf_thumbnails == 1)
        $("thumbs").style.height = (dims["height"] - greedyPane.cumulativeOffset().top - 10) +"px";
    }
    
	  var ck_instance = CKEDITOR.instances.htmlarea;
    
    // Hack pour la balise style qui pose problème dans ckeditor
    var element = ck_instance.document.getBody().getFirst();

    if (element.$.tagName == "STYLE") {
      window.save_style = element;
      element.remove();
    }
    
    // Les plugins qui ne doivent pas être pris en compte pour le changement de valeur pour contentEditable
    //var plugins = ["source", "undo", "redo", "pastefromword"];

    // Le content editable des champs
    /*var toggleContentEditable = function(state, obj) {
      if (ck_instance.document == null || (obj.data && plugins.indexOf(obj.data.name) != -1)) return;
      var spans = ck_instance.document.getBody().getElementsByTag("span").$;
      for(var i in spans) {
        var span = spans[i];
        if (span && span.className && Element.hasClassName(span, "field"))
          span.contentEditable = state;
      }
    };
    
    ck_instance.on('beforeCommandExec' , toggleContentEditable.curry(true));
    ck_instance.on('afterCommandExec'  , toggleContentEditable.curry(false));
    ck_instance.on("beforeCombo"       , toggleContentEditable.curry(true));
    ck_instance.on("afterCombo"        , toggleContentEditable.curry(false));
    ck_instance.on("beforerenderColors", toggleContentEditable.curry(true));
    ck_instance.on("afterrenderColors" , toggleContentEditable.curry(false));*/

	  // Redimensionnement de l'éditeur
		window.resizeEditor();

    // Redimensionnement automatique de l'éditeur en même temps que celui de la fenêtre.
		Event.observe(window, "resize", function(e){
		  window.resizeEditor();
    });
		{{if $templateManager->printMode}}
      CKEDITOR.instances.htmlarea.document.getBody().contentEditable=false;
    {{else}}
		  {{if $pdf_thumbnails}}
  		  Thumb.content = ck_instance.getData();
  	    Thumb.refreshThumbs(1, Thumb.compte_rendu_id, Thumb.modele_id, Thumb.user_id, Thumb.mode);
  		{{/if}}
  		// Don't close the window with escape
  	  document.stopObserving('keydown', closeWindowByEscape);
  	  
  	  // Don't allow escape or alt+f4 to cancel the request
  	  document.observe('keydown', function(e){
  	    var keycode = Event.key(e);
  	    if (keycode == 27 || keycode == 115 && e.altKey) {
  	      return Event.stop(e);
  	    }
  	    // Catches command+s
  	    if (keycode == 83 && e.metaKey) {
  	      submitCompteRendu();
  	      Event.stop(e);
  	    }
  	    {{if $pdf_thumbnails}}
  	      if (keycode == 80 && (e.ctrlKey || e.metaKey)) {
  	        ck_instance.getCommand("mbprintPDF").exec();
  	        Event.stop(e);
  	      }
  	    {{/if}}
	    });
		// Surveillance de modification de l'éditeur de texte
		ck_instance.on("key", loadOld);

    {{/if}}
    // Après l'impression, le focus revient sur l'éditeur
    // On peut donc enlever le style à ce moment-là.
    ck_instance.on("focus", function(){ deleteStyle(); });
	});
}

Main.add(initCKEditor);

</script>
{{/if}}