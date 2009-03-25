<script type="text/javascript">
function verifIntubDifficileAndSave(oForm){
  if(oForm.mallampati[2].checked || oForm.mallampati[3].checked
    || oForm.bouche[0].checked || oForm.bouche[1].checked
    || oForm.distThyro[0].checked){
  
    // Avertissement d'intubatino difficile
    $('divAlertIntubDiff').style.visibility = "visible";
  }else{
    $('divAlertIntubDiff').style.visibility = "hidden";
  }
  
  submitFormAjax(oForm, 'systemMsg');
  
  var i = null;
  for (i = 0; i < 4; i++) {
    var o = oForm.mallampati[i];
    var bg = $('mallampati_bg_classe'+(i+1));
    
    if (o.checked) {
      bg.addClassName('mallampati-selected');
    }
    else {
      bg.removeClassName('mallampati-selected');
    }
  }
}

var SchemaDentaire = {
  sId: null,
  oListEtats: {{$list_etat_dents|@json}},
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
    
    if (Prototype.Browser.Gecko || Prototype.Browser.WebKit) {
    // Clone the image's size to the container
    var img = new Image();
    img.src = oImage.src;

    if (img.width != 0) {
      oSchema.setStyle({width: img.width+'px'});
    } else {
      oSchema.setStyle({width: '407px'});
    }
    
    // Menu initialization
    var oMenu = new Element('div', {id: this.sId+'-menu', className: 'dent-menu'}),
		    oLegend = new Element('div', {id: this.sId+'-legend', className: 'dent-legend'});
    
    // Buttons initialization
    var oActions = new Element('div', {className: 'dent-buttons'}),
		    oButton = new Element('a', {className: 'buttoncancel', href: '#1'}).update("Réinitialiser").observe('click', this.reset.bind(this));
    oActions.insert({top: oButton});
    
    // For each possible state, we add a link in the menu and an item in the legend
    var oClose = new Element('a', {className: 'cancel'}).update('x').observe('click', this.closeMenu.bind(this));
    oMenu.insert({bottom: oClose});
    
    // Options and legend items
    states.each (function (o) {
      var oOption = new Element('a');
      
			var className = o ? o : 'none',
			    label = o ? o.capitalize() : 'Aucun';
			var oItem = new Element('a', {className: className, href: '#1', style: 'display: block;'}).update(label).observe('click', (function(){this.setPaint(className)}).bind(this));
      oLegend.insert({bottom: oItem});

      oOption.addClassName(className).update(label);
				
      oOption.observe('click', this.onSelectState.bind(this));
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
        
        var id = parseInt(o.id.substr(5));
        oDent.id = this.sId+'-dent-'+id;
        oDent.dentId = id;
        this.aDentsId[id] = oDent.id;
        
        if (etat = this.oListEtats[oDent.dentId]) {
          this.setState(oDent.dentId, etat, true);
        }
        
        // Callbacks on the tooth
        oDent.observe('mouseover', this.onMouseOver.bind(this))
				     .observe('mouseout', this.onMouseOut.bind(this))
						 .observe('click', this.onClick.bind(this));
      }
    , this);
    } else {
      oSchema.innerHTML = '' + oSchema.innerHTML;
    }
  },
	
	setPaint: function(state) {
		$('dents-schema-legend').childElements().each(function(e){e.removeClassName('active')});
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
    var el = e.element(), 
		style = {
      top: parseInt(el.getStyle('top')), 
      left: parseInt(el.getStyle('teft')), 
      width: parseInt(el.getStyle('width')), 
      height: parseInt(el.getStyle('height'))
    };

    el.addClassName('hover')
		  .setStyle({
      top: style.top-1+'px',
      left: style.left-1+'px',
      width: style.width-1+'px',
      height: style.height-1+'px'
    });
  },
  
  onMouseOut: function (e) {
    var el = e.element(),
		style = {
      top: parseInt(el.getStyle('top')), 
      left: parseInt(el.getStyle('left')), 
      width: parseInt(el.getStyle('width')), 
      height: parseInt(el.getStyle('height'))
    };
    
    el.removeClassName('hover')
		  .setStyle({
      top: style.top+1+'px',
      left: style.left+'px',
      width: style.width+1+'px',
      height: style.height+1+'px'
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
	    });
	    menu.show();
	    
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

Main.add(function () {
  var states = [0, 'bridge', 'pivot', 'mobile', 'appareil'];
  SchemaDentaire.initialize("dents-schema", states);
} );
</script>
<form name="etat-dent-edit" action="?" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_etat_dent_aed" />
  <input type="hidden" name="etat_dent_id" value="" />
  <input type="hidden" name="_patient_id" value="{{$consult->_ref_patient->_id}}" />
  <input type="hidden" name="dent" value="" />
  <input type="hidden" name="etat" value="" />
</form>

<form name="editFrmIntubation" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
{{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}

<table class="form">
  <tr><th colspan="5" class="category">Condition d'intubation</th></tr>
  
  <tr>
    <td class="button" rowspan="20" style="width: 1%;">
      <div id="dents-schema" style="position: relative;">
        <img id="dents-schema-image" src="images/pictures/dents.png?build={{$version.build}}" border="0" usemap="#dents-schema-map" alt="" /> 
        <map id="dents-schema-map" name="dents-schema-map">
          <area shape="circle" coords="127,112, 30" href="#1" alt="" id="dent-10" /><!-- Central haut adulte -->
          <area shape="circle" coords="116,33, 11" href="#1" alt="" id="dent-11" />
          <area shape="circle" coords="97,44, 11" href="#1" alt="" id="dent-12" />
          <area shape="circle" coords="79,55, 12" href="#1" alt="" id="dent-13" />
          <area shape="circle" coords="70,74, 12" href="#1" alt="" id="dent-14" />
          <area shape="circle" coords="61,93, 13" href="#1" alt="" id="dent-15" />
          <area shape="circle" coords="55,118, 17" href="#1" alt="" id="dent-16" />
          <area shape="circle" coords="51,146, 16" href="#1" alt="" id="dent-17" />
          <area shape="circle" coords="50,174, 15" href="#1" alt="" id="dent-18" />
          <area shape="circle" coords="137,33, 11" href="#1" alt="" id="dent-21" />
          <area shape="circle" coords="156,44, 11" href="#1" alt="" id="dent-22" />
          <area shape="circle" coords="174,55, 12" href="#1" alt="" id="dent-23" />
          <area shape="circle" coords="183,74, 12" href="#1" alt="" id="dent-24" />
          <area shape="circle" coords="192,94, 13" href="#1" alt="" id="dent-25" />
          <area shape="circle" coords="198,118, 17" href="#1" alt="" id="dent-26" />
          <area shape="circle" coords="201,146, 16" href="#1" alt="" id="dent-27" />
          <area shape="circle" coords="203,174, 15" href="#1" alt="" id="dent-28" />
          <area shape="circle" coords="127,272, 30" href="#1" alt="" id="dent-30" /><!-- Central bas adulte -->
          <area shape="circle" coords="135,356, 9" href="#1" alt="" id="dent-31" />
          <area shape="circle" coords="150,349, 9" href="#1" alt="" id="dent-32" />
          <area shape="circle" coords="164,338, 11" href="#1" alt="" id="dent-33" />
          <area shape="circle" coords="177,322, 11" href="#1" alt="" id="dent-34" />
          <area shape="circle" coords="186,303, 12" href="#1" alt="" id="dent-35" />
          <area shape="circle" coords="195,279, 18" href="#1" alt="" id="dent-36" />
          <area shape="circle" coords="199,250, 16" href="#1" alt="" id="dent-37" />
          <area shape="circle" coords="203,222, 15" href="#1" alt="" id="dent-38" />
          <area shape="circle" coords="118,356, 9" href="#1" alt="" id="dent-41" />
          <area shape="circle" coords="103,348, 9" href="#1" alt="" id="dent-42" />
          <area shape="circle" coords="89,338, 11" href="#1" alt="" id="dent-43" />
          <area shape="circle" coords="76,323, 11" href="#1" alt="" id="dent-44" />
          <area shape="circle" coords="66,304, 12" href="#1" alt="" id="dent-45" />
          <area shape="circle" coords="58,279, 18" href="#1" alt="" id="dent-46" />
          <area shape="circle" coords="54,250, 16" href="#1" alt="" id="dent-47" />
          <area shape="circle" coords="49,223, 15" href="#1" alt="" id="dent-48" />
          <area shape="circle" coords="324,162, 19" href="#1" alt="" id="dent-50" /><!-- Central haut enfant -->
          <area shape="circle" coords="318,114, 7" href="#1" alt="" id="dent-51" />
          <area shape="circle" coords="307,120, 8" href="#1" alt="" id="dent-52" />
          <area shape="circle" coords="298,131, 9" href="#1" alt="" id="dent-53" />
          <area shape="circle" coords="290,147, 11" href="#1" alt="" id="dent-54" />
          <area shape="circle" coords="285,166, 12" href="#1" alt="" id="dent-55" />
          <area shape="circle" coords="331,114, 7" href="#1" alt="" id="dent-61" />
          <area shape="circle" coords="342,120, 8" href="#1" alt="" id="dent-62" />
          <area shape="circle" coords="351,131, 9" href="#1" alt="" id="dent-63" />
          <area shape="circle" coords="357,147, 11" href="#1" alt="" id="dent-64" />
          <area shape="circle" coords="363,166, 12" href="#1" alt="" id="dent-65" />
          <area shape="circle" coords="324,231, 19" href="#1" alt="" id="dent-70" /><!-- Central haut enfant -->
          <area shape="circle" coords="330,271, 6" href="#1" alt="" id="dent-71" />
          <area shape="circle" coords="339,265, 7" href="#1" alt="" id="dent-72" />
          <area shape="circle" coords="350,255, 8" href="#1" alt="" id="dent-73" />
          <area shape="circle" coords="357,243, 8" href="#1" alt="" id="dent-74" />
          <area shape="circle" coords="365,227, 10" href="#1" alt="" id="dent-75" />
          <area shape="circle" coords="319,271, 6" href="#1" alt="" id="dent-81" />
          <area shape="circle" coords="309,265, 7" href="#1" alt="" id="dent-82" />
          <area shape="circle" coords="298,255, 8" href="#1" alt="" id="dent-83" />
          <area shape="circle" coords="291,242, 8" href="#1" alt="" id="dent-84" />
          <area shape="circle" coords="282,228, 10" href="#1" alt="" id="dent-85" />
        </map>
      </div>
    </td>
    
    <td colspan="2">
      <table style="width: 100%">
        <tr>
          {{foreach from=$consult_anesth->_specs.mallampati->_locales key=curr_mallampati item=trans_mallampati}}
          <td class="button">
            <div id="mallampati_bg_{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}class="mallampati-selected"{{/if}}>
            <label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{$trans_mallampati}}">
              <img src="images/pictures/{{$curr_mallampati}}.png?build={{$version.build}}" alt="{{$trans_mallampati}}" />
              <br />
              <input type="radio" name="mallampati" value="{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}checked="checked" {{/if}} onclick="verifIntubDifficileAndSave(this.form);" />
              {{$trans_mallampati}}
            </label>
            </div>
          </td>
          {{/foreach}}
        </tr>
      </table>
    </td>
  </tr>
  
  <tr>
    <th style="width: 1%;">{{mb_label object=$consult_anesth field="bouche" defaultFor="bouche_m20"}}</th>
    <td>
      {{mb_field object=$consult_anesth field="bouche" typeEnum="radio" separator="<br />" onclick="verifIntubDifficileAndSave(this.form);"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$consult_anesth field="distThyro" defaultFor="distThyro_m65"}}</th>
    <td>
      {{mb_field object=$consult_anesth field="distThyro" typeEnum="radio" separator="<br />" onclick="verifIntubDifficileAndSave(this.form);"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$consult_anesth field="etatBucco"}}</th>
    <td>
      <select name="_helpers_etatBucco" size="1" onchange="pasteHelperContent(this);this.form.etatBucco.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.etatBucco.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.etatBucco)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="etatBucco" onchange="submitFormAjax(this.form, 'systemMsg')"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$consult_anesth field="conclusion"}}</th>
    <td>
      <select name="_helpers_conclusion" size="1" onchange="pasteHelperContent(this);this.form.conclusion.onchange();">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$consult_anesth->_aides.conclusion.no_enum}}
      </select>
      <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.conclusion)">{{tr}}New{{/tr}}</button><br />
      {{mb_field object=$consult_anesth field="conclusion" onchange="submitFormAjax(this.form, 'systemMsg')"}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <div id="divAlertIntubDiff" style="float:right;color:#F00;{{if !$consult_anesth->_intub_difficile}}visibility:hidden;{{/if}}"><strong>Intubation Difficile Prévisible</strong></div>
    </td>
  </tr>
</table>
</form>