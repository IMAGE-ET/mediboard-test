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

window.nb_lists = {{$templateManager->usedLists|@count}};
window.nb_textes_libres = {{$templateManager->textes_libres|@count}};

function initCKEditor() {
  CKEDITOR.ispasting = false;
  window.old_source = $("htmlarea").value;
  var editor = CKEDITOR.replace("htmlarea", {customConfig: "../../?m=dPcompteRendu&a=mb_fckeditor&suppressHeaders=1"});
  editor.addCss( 'body { font-family: {{$conf.dPcompteRendu.CCompteRendu.default_font}} }' );
  editor.addCss( 'body { font-size: {{$conf.dPcompteRendu.CCompteRendu.default_size}} }' );
  
  editor.on("instanceReady", function(e) {  
    window.resizeEditor = function () {
      var dims = document.viewport.getDimensions();
      var greedyPane = $$(".greedyPane")[0];
      
      if (!greedyPane) {
        return;
      }
      
      CKEDITOR.instances.htmlarea.resize('', (dims["height"] - greedyPane.cumulativeOffset().top - 10));
      if (window.pdf_thumbnails && window.Preferences.pdf_and_thumbs == 1) {
        $("thumbs").style.height = (dims["height"] - greedyPane.cumulativeOffset().top - 10) +"px";
      }
    }
    
    var ck_instance = CKEDITOR.instances.htmlarea;

    {{if !$templateManager->valueMode}}
  
      // Le content editable des champs
      // Les plugins qui ne doivent pas être pris en compte pour le changement de valeur pour contentEditable
      var plugins = ["source", "undo", "redo", "pastefromword", "mbprint"];    
      window.toggleContentEditable = function(state, obj) {
        if (Object.isUndefined(obj)) {
          obj = {data: null};
        }
        
        if (ck_instance.document == null || (obj.data && plugins.indexOf(obj.data.name) != -1)) return;
        
        if (Prototype.Browser.IE) {
          var spans = ck_instance.document.getBody().getElementsByTag("span").$;
          for (var i in spans) {
            var span = spans[i];
            if (span && span.className && (Element.hasClassName(span, "field") || Element.hasClassName(span, "name"))) {
              if (state) {
                span.removeAttribute("contentEditable");
              }
              else {
                span.contentEditable = false;
              }              
            }
          }
          return;          
        }          
        var spans_by_class = [];
        spans_by_class[0] = ck_instance.document.$.getElementsByClassName("field");
        spans_by_class[1] = ck_instance.document.$.getElementsByClassName("name");
        
        for (var s = 0; s < spans_by_class.length; s++) {
          var spans = spans_by_class[s];
          
          // Bug : Firefox n'efface pas un span en contentEditable à false.
          // Il faut passer par un double span.
          if (spans.length) {
            for (var i = 0; i < spans.length; i++) {
              var span = spans[i];
              
              if (state) {
                if (Prototype.Browser.Gecko) {
                  var span_parent = Element.up(span);
                  var parent = Element.up(span_parent);
                  var span_copy = span.cloneNode(true);
                  span_copy.removeAttribute("contentEditable");
                  parent.insertBefore(span_copy, span_parent);
                  Element.remove(span_parent);
                }
                else {
                  span.removeAttribute("contentEditable");
                }
              }
              else {
                if (Prototype.Browser.Gecko) {
                  var parent = Element.up(span);
                  var span_insert = new DOM.span();
                  var span_copy = span.cloneNode(true);
                  
                  span_insert.contentEditable = false;
                  span_copy.contentEditable = true;
                  
                  span_insert.insert(span_copy);
                  parent.insertBefore(span_insert, span);
                  
                  Element.remove(span);
                }
                else {           
                  span.contentEditable = false;
                }
              }
            }
          }            
        }
      };
      
      window.toggleContentEditable(false);
      
      ck_instance.on('beforeCommandExec' , window.toggleContentEditable.curry(true));
      ck_instance.on('afterCommandExec'  , window.toggleContentEditable.curry(false));
      ck_instance.on("beforeCombo"       , window.toggleContentEditable.curry(true));
      ck_instance.on("afterCombo"        , window.toggleContentEditable.curry(false));
      ck_instance.on("beforerenderColors", window.toggleContentEditable.curry(true));
      ck_instance.on("afterrenderColors" , window.toggleContentEditable.curry(false));
    {{/if}}
    
    // Redimensionnement de l'éditeur
    window.resizeEditor();

    // Redimensionnement automatique de l'éditeur en même temps que celui de la fenêtre.
    Event.observe(window, "resize", function(e){
      window.resizeEditor();
    });
    
    {{if $templateManager->printMode}}
      CKEDITOR.instances.htmlarea.element.$.disabled=true;
      CKEDITOR.instances.htmlarea.element.$.contentEditable=false;
      CKEDITOR.instances.htmlarea.element.$.designMode="Off";
      ck_instance.on('key', function(keyEvent) {
        keyEvent.cancel();
      });
    {{else}}
      {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
        if (window.Thumb) {
          Thumb.content = ck_instance.getData();
          window.thumbs_timeout = setTimeout(function() {
            Thumb.refreshThumbs(1);
          }, time_before_thumbs);
        }
      {{/if}}

      if (window.pdf_thumbnails && Prototype.Browser.IE) {
        window.save_style = deleteStyle();
        ck_instance.on("beforePreview", function() { restoreStyle(); });
        ck_instance.on("afterPreview", function() { window.save_style = deleteStyle(); });
      }
      
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
    if (window.Thumb) {
      ck_instance.on("key", loadOld);
    }
    
    // Redéfinition du copier-coller dans CKEditor pour firefox, car le comportement par défaut ne convient pas
    if (Prototype.Browser.Gecko) {
      ck_instance.on("paste", function(evt) {
        if (CKEDITOR.ispasting) {
          return;
        }
        CKEDITOR.ispasting = true;
        
        // Tenir compte de la façon dont on colle :
        // - sans mise en forme
        // - depuis word
        
        var paste = evt.data.html || evt.data.text;
        
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