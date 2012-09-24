/* $Id: fckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbplay',{
  requires: ['iframedialog'],
  init:function(editor){ 
   editor.addCommand('mbplay', {exec: mbplay_onclick});
   editor.ui.addButton('mbplay', {label:'Mode play', command:'mbplay',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mbplay/images/mbplay_on.png' });
   editor.on("instanceReady", function() {
     window.parent.not_found = new Array();
     // Si aucune zone de texte libre ni de liste de choix, on désactive le plugin
     if (window.parent.nb_lists == 0 && window.parent.nb_textes_libres == 0) {
       endModePlay(editor);
     }
   });
  }
});

function unescapeHtml(str) {
  var temp = document.createElement("div");
  if (/^\s*$/.test(str) || str == null) {
    return '';
  }
  temp.innerHTML = str;
  var result = temp.childNodes[0].nodeValue;
  temp.removeChild(temp.firstChild);
  return result;
}

function endModePlay(editor) {
  var window_parent = window.parent;
  Control.Modal.close();
  // Changement de l'icône et désactivation du bouton
  var commande = editor.getCommand('mbplay');
  window_parent.$(commande.uiItems[0]._.id).down().style.backgroundImage =
    "url("+CKEDITOR.getUrl("../../modules/dPcompteRendu/fcke_plugins/mbplay/images/mbplay_off.png")+")";
  commande.setState(CKEDITOR.TRISTATE_DISABLED);
}

function mbplay_onclick(editor) {
  
  var window_parent = window.parent;
  var bodyEditor = editor.document.getBody().$;
  
  // Si la modale a été fermée, alors on relance l'étape courante,
  // à moins qu'un petit malin ait supprimé le champ
  // On entoure d'un try car s'il y a eu un enregistrement du document
  // entre 2 étapes, une erreur "Permission refusée" apparaît sous internet explorer.
  try {
    if (window_parent.current_playing && window_parent.current_playing.innerHTML) {
      Element.setStyle(current_playing, {
        backgroundColor: "#ffd700"
      });
      window_parent.modal_mode_play.open();
      setTimeout(function(){
        bodyEditor.scrollTop = window_parent.save_scroll
      }, 10);
      return;
    }
  } catch (e) {}
  
  var content = editor.getData();
  var field,re = /\[Liste - ([^\]]+)\]|\[\[Texte libre - ([^\]]+)\]\]/g;
  
  /*while (field = re.exec(content)) {
    console.log(field);
  }*/
  while (field = re.exec(content)) {
    // On sort de la fonction s'il n'y a plus de champ
    if (!field) {
      endModePlay(editor);
      return;
    }
    
    var search = field[0];
    var name = field[1]||field[2];
    var name_escape = unescapeHtml(name);
    var class_span = "field";
  
    if (field[1]) {
      class_span = "name";
    }
  
    // Recherche de l'élément dans l'éditeur pour le scroll
    var elements = editor.document.getBody().$.querySelectorAll("span."+class_span);
    //console.log(elements);
    var editor_element = null;
    
    
    window_parent.$A(elements).each(function(cur) {
      var cur_name = unescapeHtml(cur.innerHTML);
      if ( cur_name && cur_name.indexOf(unescapeHtml(search)) != -1) {
        editor_element = cur;
        throw window_parent.$break;
      }
    });
    
    // Recherche de l'élément dans la liste existante
    var element = null;
    
    // Cas des listes de choix
    if (class_span == "name") {
      if (window_parent.$$("div.listeChoixCR").length != 0) {
        var listes = window_parent.$$("div.listeChoixCR")[0].select("select");
        window_parent.$A(listes).each(function(list) {
          var list_name = unescapeHtml(list.getAttribute("data-nom"));
          if (list_name && list_name.indexOf(name_escape) != -1) {
            // On supprime la 1ère option (nom de la liste de choix)
            window_parent.Element.remove(list.down());
            list.selectedIndex = -1;
            Element.setStyle(list, {width: "100%"});
            element = list;
            throw window_parent.$break;
          }
        });
      }
    }
    // Zones de texte libre
    else {
      var textes_libres = window_parent.$$("td.textelibreCR")[0].childElements();
      window_parent.$A(textes_libres).each(function(cur){
        var cur_name = unescapeHtml(cur.getAttribute("data-nom"));
        if (cur_name.indexOf(name_escape) != -1) {
          element = cur;
          throw $break;
        }
      });
    }
    //console.log(element);
    // Si pas de correspondance
    if (!element) {
      if (window_parent.not_found.join(" ").indexOf(name) == -1) {
        alert("Liste non existante : " + editor_element.innerHTML);
        // Sauvegarde de l'élémént non trouvé pour ne pas avoir la popup la prochaine fois
        window_parent.not_found.push(name);
      }
      continue;
    }
    break;
  }
  
  // Si plus de correspondances, le mode play s'arrête
  if (!element) {
    endModePlay(editor);
    return;
  }
  // On met en surbrillance l'élément dans l'éditeur
  Element.setStyle(editor_element, {backgroundColor: "#ffd700"});
  
  // Sauvegarde de l'étape courante (si fermeture de la modale avant application étape)
  window_parent.current_playing = editor_element;
  
  // Ouverture modale
  window_parent.playField(element, class_span, editor_element, name);
  
  // Scroll dans l'éditeur
  // Besoin d'un délai pour IE.
  window_parent.save_scroll = editor_element.offsetTop;
  setTimeout(function() { bodyEditor.scrollTop = editor_element.offsetTop }, 10);
  
}

//Remplacement des champs
window.parent.replaceField = function(elt, class_name, empty) {
  var window_parent = window.parent;
  window_parent.current_playing = null;
  
  //Le contenu de l'éditeur a changé.
  window_parent.Thumb.contentChanged = true;
  window_parent.Thumb.changed = true;
  window_parent.Thumb.old();
  CKEDITOR.instances.htmlarea.removeListener("key", loadOld);
  
  var textReplacement = "";
  if (!empty) {
    switch(class_name) {
      case "name":
        textReplacement = window_parent.Form.Element.getValue(elt);
        
        if (textReplacement == null || elt.selectedIndex == -1) {
          alert("Veuillez choisir une / des valeur(s) pour la liste de choix, ou cliquez sur Vider.");
          return;
        }
        if (typeof textReplacement == "object") {
          textReplacement = textReplacement.join(', ');
        }
        break;
      case "field":
        textReplacement = elt.down("textarea").getValue().replace(/\n/g,"<br/>");
        if (textReplacement == "") {
          alert("Veuillez saisir du texte, ou cliquer sur Vider.");
          return;
        }
        break;
      default:
    }
  }
  var correspondances = CKEDITOR.instances.htmlarea.document.getBody().$.querySelectorAll("span."+class_name);
  
  window_parent.$A(correspondances).each(function(corr) {
    // Remplacement de toutes occurrences
    var corr_name = unescapeHtml(corr.innerHTML);
    var nom = unescapeHtml(elt.getAttribute("data-nom"));
    
    if (corr_name && corr_name.indexOf("- " + nom + "]") != -1) {
      var pattern = "";
      if (class_name == "name") {
        pattern = "[Liste - " + nom + "]";
      }
      else {
        pattern = "[[Texte libre - " + nom + "]]";
      }
      
      // Ne remplacer que la sous-chaîne,
      // car des spans peuvent être imbriqués
      var begin = corr.innerHTML.indexOf(pattern);
      var end = begin + pattern.length;
      
      if (begin > -1 && end > -1) {
        if (textReplacement == "") {
          corr.parentNode.removeChild(corr);
          return;
        }
        corr.innerHTML = corr.innerHTML.substr(0, begin) +
          unescapeHtml(corr.innerHTML.substr(begin, end)).replace(pattern, textReplacement) +
          corr.innerHTML.substr(end);
      }
    }
    // On efface le background apposé lors du lancement du mode play
    Element.setStyle(corr, {background: ''});
  });
  
  Element.remove(elt);
  
  /*var purge_field = document.forms.editFrm.purge_field.value;
  
  purge_field = purge_field.replace(/\//, "\/");
  purge_field = purge_field.replace(/</, "\<");
  purge_field = purge_field.replace(/>/, "\>");
  
  if (purge_field != "") {
    var data = CKEDITOR.instances.htmlarea.getData();
    data = data.replace("/"+purge_field+"/", "");
    CKEDITOR.instances.htmlarea.setData(data, function() {});
  }
  */
  CKEDITOR.instances.htmlarea.getCommand('mbplay').exec();    
}
