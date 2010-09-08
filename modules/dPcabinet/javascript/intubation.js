/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

verifIntubDifficileAndSave = function(oForm){
  // Avertissement d'intubatino difficile
  $('divAlertIntubDiff').setVisibility(
    oForm.mallampati[2].checked || 
    oForm.mallampati[3].checked || 
    oForm.bouche[0].checked || 
    oForm.bouche[1].checked || 
    oForm.distThyro[0].checked
  );
  
  onSubmitFormAjax(oForm);
  
  for (var i = 0; i < 4; i++) {
    var o = oForm.mallampati[i];
    var bg = $('mallampati_bg_classe'+(i+1));
    bg.setClassName('mallampati-selected', o.checked);
  }
}

resetIntubation = function(form) {
  var fields = ["mallampati", "bouche", "distThyro", "etatBucco", "conclusion"]
  fields.each(function(f){
    $V(form[f], '');
  });
  verifIntubDifficileAndSave(form);
}

SchemaDentaire = {
  sId: null,
  oListEtats: null,
  aStates: null,
  fAlpha: 0.5,
  iSelectedDent: null,
  aDentsId: null,
  sPaint: null,
  aDentsNumbers: [
    // Adulte
    10,
    11, 12, 13, 14, 15, 16, 17, 18, // haut droite
    21, 22, 23, 24, 25, 26, 27, 28, // haut gauche
    30,
    31, 32, 33, 34, 35, 36, 37, 38, // bas gauche
    41, 42, 43, 44, 45, 46, 47, 48, // bas droite
    
    // Enfant
    50,
    51, 52, 53, 54, 55, // haut droite
    61, 62, 63, 64, 65, // haut gauche
    70,
    71, 72, 73, 74, 75, // bas gauche
    81, 82, 83, 84, 85  // bas droite
  ],
  
  initialize: function(id, states) {
    // Class attributes
    this.sId = id;
    this.aStates = states;
    
    // Elements
    var oSchema = $(this.sId),
        oMap = $(this.sId+"-map"),
        oImage = $(this.sId+"-image");
        
    oSchema.addClassName('schema-dentaire');
    
    if (true || Prototype.Browser.Gecko || Prototype.Browser.WebKit) {
    // Clone the image's size to the container
    var img = new Image();
    img.src = oImage.src;

    if (img.width != 0) {
      oSchema.setStyle({width: img.width+'px'});
    } else {
      oSchema.setStyle({width: '407px'});
    }
    
    // Menu initialization
    var oMenu = new Element('div', {id: this.sId+'-menu'}).addClassName('dent-menu'),
        oLegend = new Element('div', {id: this.sId+'-legend'}).addClassName('dent-legend');
    
    // Buttons initialization
    var oActions = new Element('div').addClassName('dent-buttons'),
        oButton = new Element('a', {href: '#1'}).addClassName('button cancel').update($T('Reset')).observe('click', this.reset.bindAsEventListener(this));
    oActions.insert({top: oButton});
    
    // For each possible state, we add a link in the menu and an item in the legend
    var oClose = new Element('a').addClassName('cancel').update('x').observe('click', this.closeMenu.bindAsEventListener(this));
    oMenu.insert({bottom: oClose});
    
    // Options and legend items
    states.each (function (o) {
      var oOption = new Element('a');
      
      var className = o || 'none',
          label = o ? o.capitalize() : 'Aucun';
      var oItem = new Element('a', {href: '#1', style: 'display: block;'}).addClassName(className).update(label).observe('click', (function(){this.setPaint(className)}).bindAsEventListener(this));
      oLegend.insert({bottom: oItem});

      oOption.addClassName(className).update(label);
        
      oOption.observe('click', this.onSelectState.bindAsEventListener(this));
      oMenu.insert({bottom: oOption});
    }, this);
    
    oSchema.insert({bottom: oMenu.hide()})
           .insert({top: oLegend})
           .insert({top: oActions});
    
    this.aDentsId = [];
    
    /* For each area in the map */
    oMap.childElements().each(
      function (o) {
        // We parse the coords attribute to get coordinates and radius of the circle area
        var area = o.coords.split(','),
            x = parseInt(area[0]),
            y = parseInt(area[1]),
            r = parseInt(area[2]);
        
        // New div for the tooth
        var oDent = new Element('div');
        oDent.addClassName('dent');
        oDent.setStyle({
          top: y-r+'px',
          left: x-r+'px',
          width: r*2+'px',
          height: r*2+'px'
        });
        oSchema.insert({top: oDent});
        
        var id = parseInt(o.id.substr(5)), etat;
        oDent.id = this.sId+'-dent-'+id;
        oDent.dentId = id;
        this.aDentsId[id] = oDent.id;
        
        if (etat = this.oListEtats[oDent.dentId]) {
          this.setState(oDent.dentId, etat, true);
        }
        
        // Callbacks on the tooth
        oDent.observe('mouseover', this.onMouseOver.bindAsEventListener(this))
             .observe('mouseout', this.onMouseOut.bindAsEventListener(this))
             .observe('click', this.onClick.bindAsEventListener(this));
      }
    , this);
    } else {
      oSchema.innerHTML = '' + oSchema.innerHTML;
    }
  },
  
  setPaint: function(state) {
    $('dents-schema-legend').childElements().invoke('removeClassName', 'active');
    if (this.sPaint != state) {
      this.sPaint = state;
      $('dents-schema-legend').select('.'+state).first().addClassName('active');
    }
    else {
      this.sPaint = null;
    }
  },
  
  getDent: function (id) {
    return $(this.sId+'-dent-'+id);
  },
  
  // Change the state of a tooth
  setState: function (id, state, displayOnly) {
    var dent = this.getDent(id);
    if (dent) {
      dent.setOpacity(this.fAlpha);
      dent.className = 'dent';
      
      if (state)
        dent.addClassName(state);
      else
        dent.setOpacity(1);
      
      if (!displayOnly) {
        var oForm = document.forms['etat-dent-edit'];
        if (oForm) {
          $V(oForm.dent, id);
          $V(oForm.etat, (((state != 'none') && state) ? state : ''));
          submitFormAjax(oForm, 'systemMsg');
        }
      }
    }
  },
  
  onMouseOver: function (e) {
    var el = e.element().addClassName('hover'), 
    style = {
      width: parseInt(el.getStyle('width'))-1, 
      height: parseInt(el.getStyle('height'))-1
    };

    el.setStyle({
      width: style.width+'px',
      height: style.height+'px'
    });
  },
  
  onMouseOut: function (e) {
    var el = e.element().removeClassName('hover'),
    style = {
      width: parseInt(el.getStyle('width'))+1, 
      height: parseInt(el.getStyle('height'))+1
    };
    
    el.setStyle({
      width: style.width+'px',
      height: style.height+'px'
    });
  },
  
  // Show the menu
  onClick: function (e) {
    var dent = e.element(),
        menu = $(this.sId+'-menu');

    if (!this.sPaint) {
      if (this.iSelectedDent) {
        this.getDent(this.iSelectedDent).removeClassName('focus');
      }
      dent.addClassName('focus');
      menu.setStyle({
       top: dent.getStyle('top'),
       left: (parseInt(dent.getStyle('left')) + dent.getWidth() + 4) + 'px'
      }).show();
      
      this.iSelectedDent = dent.dentId;
    }
    else {
      this.setState(dent.dentId, this.sPaint);
    }
  },
  
  // Selection of a new state in the menu
  onSelectState: function (e) {
    this.setState(this.iSelectedDent, Event.element(e).className);
    this.closeMenu();
  },
  
    // Close the menu
  closeMenu: function (e) {
    $(this.sId+'-menu').hide();
    this.getDent(this.iSelectedDent).removeClassName('focus');
    this.iSelectedDent = null;
  },
  
  // Reset the teeth state
  reset: function () {
    this.aDentsId.each(function (name, key) {
      key = this.aDentsNumbers[key];
      var oDent = $(name);
      this.aStates.each(function (state) {
        if (oDent.hasClassName(state)) {
          this.setState(key, null);
          return;
        }
      }, this);
    }, this);
    return false;
  }
};