{{if $templateManager->editor == "ckeditor" }}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{mb_script path="lib/ckeditor/ckeditor.js"}}

<style type="text/css">
/* Ugly hack to display labels with plugin buttons */
  .cke_button_mbfields span.cke_label{display:inline !important}
  .cke_button_mbhelpers span.cke_label{display:inline !important}
  .cke_button_mbfreetext span.cke_label{display:inline !important}
  .cke_button_mbfreetext span.cke_label{display:inline !important}
  .cke_button_mblists span.cke_label{display:inline !important}
</style>

<script type="text/javascript">

window.time_before_thumbs = {{$conf.dPcompteRendu.CCompteRendu.time_before_thumbs}};
window.time_before_thumbs *= 1000;

function initCKEditor() {
  CKEDITOR.ispasting = false;
  window.old_source = $("htmlarea").value;
	var editor = CKEDITOR.replace("htmlarea", {customConfig: "../../?m=dPcompteRendu&a=mb_fckeditor&suppressHeaders=1"});
	editor.on("instanceReady", function(e) {
    
    window.resizeEditor = function () {
      var dims = document.viewport.getDimensions();
      var greedyPane = $$(".greedyPane")[0];
      
      CKEDITOR.instances.htmlarea.resize('', (dims["height"] - greedyPane.cumulativeOffset().top - 10));
      if (window.pdf_thumbnails && window.Preferences.pdf_and_thumbs == 1) {
        $("thumbs").style.height = (dims["height"] - greedyPane.cumulativeOffset().top - 10) +"px";
      }
    }
    
	  var ck_instance = CKEDITOR.instances.htmlarea;

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
      CKEDITOR.instances.htmlarea.document.getBody().$.contentEditable=false;
    {{else}}
		  {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
  		  Thumb.content = ck_instance.getData();
  	    window.thumbs_timeout = setTimeout(function() {
          Thumb.refreshThumbs(1);
  	    }, time_before_thumbs);
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
  	    {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
  	      if (keycode == 80 && (e.ctrlKey || e.metaKey)) {
  	        ck_instance.getCommand("mbprintPDF").exec();
  	        Event.stop(e);
  	      }
  	    {{/if}}
	    });

    // Surveillance de modification de l'éditeur de texte
    ck_instance.on("key", loadOld);

    // Redéfinition du copier-coller dans CKEditor pour firefox, car le comportement par défaut ne convient pas
    if (Prototype.Browser.Gecko) {
      ck_instance.on("paste", function(evt) {
        if (CKEDITOR.ispasting) {
          return;
        }
        CKEDITOR.ispasting = true;
        
        evt.cancel();
  
        var paste = evt.data.html;
        var alltags = paste.match(/<[a-z]+/g);
             
        if (alltags) {
          alltags=alltags.uniq();
        }
  
        if (alltags == null || (alltags.length == 1 && alltags.indexOf("br") != -1)) {
          paste = paste.replace(/<br(\s)*(\\)*>/g, '\n');
          paste = paste.replace(/&nbsp/g, ' ');
          CKEDITOR.instances.htmlarea.fire("paste", {'text': paste.replace(/<br(\s)*(\\)*>/, '\n')});
        }
        else {
          CKEDITOR.instances.htmlarea.fire("paste", {'html': paste});
        }
        CKEDITOR.ispasting = false;
      });
    }
    {{/if}}
	});
}

Main.add(initCKEditor);

</script>
{{/if}}