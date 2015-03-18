{{if $templateManager->editor != "ckeditor"}}
  {{mb_return}}
{{/if}}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

{{mb_script path="lib/ckeditor/ckeditor.js"}}

<style type="text/css">
  #cke_htmlarea {
    border: none;
  }
  .cke_dialog_ui_vbox {
    height: 100%;
  }
  /* Ugly hack to display labels with plugin buttons */
  .cke_button__mbfields_label   { display: inline !important }
  .cke_button__mbhelpers_label  { display: inline !important }
  .cke_button__mbfreetext_label { display: inline !important }
  .cke_button__mblists_label    { display: inline !important }
</style>

<script>
  window.time_before_thumbs = {{$conf.dPcompteRendu.CCompteRendu.time_before_thumbs}};
  window.time_before_thumbs *= 1000;

  window.nb_lists = {{$templateManager->usedLists|@count}};
  window.nb_textes_libres = {{$templateManager->textes_libres|@count}};

  initCKEditor = function() {
    window.old_source = $("htmlarea").value;
    var editor = CKEDITOR.replace("htmlarea", {customConfig: "../../?m=compteRendu&raw=mb_fckeditor"});

    {{if $templateManager->font != ""}}
      CKEDITOR.addCss( 'body { font-family: {{$templateManager->font}} }' );
    {{else}}
      CKEDITOR.addCss( 'body { font-family: {{$conf.dPcompteRendu.CCompteRendu.default_font}} }' );
    {{/if}}

    {{if $templateManager->size != ""}}
      CKEDITOR.addCss( 'body { font-size: {{$templateManager->size}} }' );
    {{else}}
      CKEDITOR.addCss( 'body { font-size: {{$conf.dPcompteRendu.CCompteRendu.default_size}} }' );
    {{/if}}

    CKEDITOR.on("instanceReady", function(e) {
      // Onbeforeunload called on IE after closing a dialog box
      if (CKEDITOR.env.ie) {
        e.editor.on('dialogShow', function(dialogShowEvent) {
          $(dialogShowEvent.data._.element.$).select('a[href*="void(0)"]').each(function(elt) { elt.removeAttribute('href') });
        });
      }

      window.resizeEditor = function () {
        var greedyPane = $$(".greedyPane")[0];

        if (!greedyPane) {
          return;
        }

        var dims = document.viewport.getDimensions();
        if (greedyPane.hasClassName('message_input')) {
          CKEDITOR.instances.htmlarea.resize('', (dims["height"] - greedyPane.cumulativeOffset().top - 50));
        }
        else {
          CKEDITOR.instances.htmlarea.resize('', (dims["height"] - greedyPane.cumulativeOffset().top - 10));
        }
        if (window.pdf_thumbnails && window.Preferences.pdf_and_thumbs == 1) {
          $("thumbs").style.height = (dims["height"] - greedyPane.cumulativeOffset().top - 10) +"px";
        }
      }

      var ck_instance = e.editor;

      {{if !$templateManager->valueMode}}
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

          if (spans.length) {
            for (var i = 0; i < spans.length; i++) {
              var span = spans[i];

              if (state) {
                span.removeAttribute("contentEditable");
              }
              else {
                span.contentEditable = false;
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
      ck_instance.on("beforeRenderColors", window.toggleContentEditable.curry(true));
      ck_instance.on("afterRenderColors" , window.toggleContentEditable.curry(false));
      {{/if}}

      // Redimensionnement de l'éditeur
      window.resizeEditor();

      // Redimensionnement automatique de l'éditeur en même temps que celui de la fenêtre.
      Event.observe(window, "resize", function(e) {
        window.resizeEditor();
      });

      {{if $templateManager->printMode}}
        ck_instance.setReadOnly();
        var mbprintpdf  = ck_instance.getCommand('mbprintPDF');
        var usermessage = ck_instance.getCommand('usermessage');
        if (mbprintpdf) {
          mbprintpdf.setState(CKEDITOR.TRISTATE_OFF);
        }
        if (usermessage) {
          usermessage.setState(CKEDITOR.TRISTATE_OFF);
        }
      {{else}}
        ck_instance.document.getBody().on('keydown', autoCapHelper);
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
          ck_instance.on("afterPreview", function()  { window.save_style = deleteStyle(); });
          ck_instance.on("beforeSource", function()  { ck_instance.fire("beforePreview");});
          ck_instance.on("afterSource", function()   { ck_instance.fire("afterPreview");});
        }

        // Don't close the window with escape
        document.stopObserving('keydown', closeWindowByEscape);

        // Surveillance de modification de l'éditeur de texte
        if (window.Thumb) {
          ck_instance.on("key", loadOld);
        }
      {{/if}}
    });
  }

  Main.add(initCKEditor);

  autoCapHelper = function(event) {
    var editor = CKEDITOR.instances.htmlarea;

    var mbcap     = editor.getCommand('mbcap');
    var mbreplace = editor.getCommand('mbreplace');

    if (!mbcap) {
      return;
    }

    if (mbcap.state === CKEDITOR.TRISTATE_OFF && mbreplace.state === CKEDITOR.TRISTATE_OFF) {
      return;
    }

    var keystroke = event.data.getKeystroke();

    // Majuscule auto
    if (mbcap.state === CKEDITOR.TRISTATE_ON && keystroke >= 65 && keystroke <= 90) {
      autoCapInsert(event, keystroke);
    }

    // Remplacement d'aide à la saisie (après un espace, virgule, point, deux points, point d'exclamation, point d'interrogation)
    var keystrokes = {32:'', 188:',', 2228414:'.', 186:':', 191:':', 49:'!', 223:'!',2228415:'?', 2228412:'?'}

    if (mbreplace.state === CKEDITOR.TRISTATE_ON && keystroke in keystrokes) {
      helperInsert(event, keystroke);
    }
  }

  autoCapInsert = function(event, keystroke) {
    var editor = CKEDITOR.instances.htmlarea;
    var range, walker, selection, native, chars, data;

    selection = editor.getSelection();
    range = selection.getRanges()[0];
    range.setStartAt(editor.document.getBody(), CKEDITOR.POSITION_AFTER_START);
    walker = new CKEDITOR.dom.walker(range);

    var node = walker.previous();

    if (!node) {
      return insertUpperCase(editor, event, keystroke);
    }

    native = selection.getNative();

    if ("focusNode" in native && native.focusNode.data) {
      chars = native.focusNode.data.substr(native.anchorOffset-2, +2);
    }

    var elt = node.$;

    if (!Object.isUndefined(elt.innerHTML)) {
      data = elt.innerHTML;
    }
    else if (!Object.isUndefined(elt.data)) {
      data = elt.data;
    }

    if (!Prototype.Browser.IE) {
      data = data.strip();
    }

    // Escape des zero width space characters
    data = data.replace(/[\u200B-\u200D\uFEFF]/g, '');

    if (!Object.isUndefined(elt.data) && !data) {
      if (elt.wholeText.replace(/[\u200B-\u200D\uFEFF]/g, '').trim().length) {
        return;
      }
    }

    var previous = elt.previousElementSibling;
    if (data == "" && elt.nodeName != "BR" && previous && previous.nodeName == "SPAN" && previous.className == "field") {
      return;
    }

    if (
    /* Commence par un retour chariot ou une ligne verticale */
      elt.nodeName === "BR" ||
        data == ""       ||
        (data && data.length == 0) ||
        (Prototype.Browser.IE && !Object.isUndefined(data) && /[\.\?!]\s/.test(data.substr(-2))) ||
        /(<br|<hr)/.test(data) ||
        (native.focusNode && native.focusNode.length == 0) ||
        /* Les 2 derniers caractères sont :
         - un point ou
         - un point d'exclamation ou
         - un point d'interrogation
         et un espace */
        (/[\.\?!]\s/.test(chars))) {
      insertUpperCase(editor, event, keystroke);
    }
  }

  helperInsert = function(event, keystroke) {
    var editor = CKEDITOR.instances.htmlarea;
    var range, selection, selected_ranges, container, chars, text, last_char, last_space;

    selection = editor.getSelection();
    selected_ranges = selection.getRanges();
    range = selected_ranges[0];
    container = range.startContainer;
    chars = text = container.getText();
    chars = chars.strip().trim();
    last_char = keystrokes[keystroke];

    // Espace insécable pour IE
    if (Prototype.Browser.IE) {
      last_space = chars.lastIndexOf(" ");
    }
    else {
      last_space = chars.lastIndexOf(" ");
    }

    if (last_space != -1) {
      chars = chars.substr(last_space+1);
    }

    chars = chars.toLowerCase();

    $H(helpers[0].options).each(function(categ) {
      var helpers = categ[1];
      if (Object.isUndefined(helpers.length)) {
        $H(helpers).each(function(helper) {
          var key = helper[0];
          if (key.toLowerCase() === chars) {

            var pattern = new RegExp(key+"$", "gi");

            // On insère un espace insécable après le remplacement de l'aide
            container.setText(text.replace(pattern, helper[1] + last_char + " "));
            selection.selectElement(container);
            selected_ranges = selection.getRanges();
            selected_ranges[0].collapse(false);
            selection.selectRanges(selected_ranges);

            event.data.preventDefault();
            throw $break;
          }
        });
      }
    });
  }
</script>
