<script language="Javascript" type="text/javascript">
function verifIntubDifficileAndSave(oForm){
  if(oForm.mallampati[2].checked || oForm.mallampati[3].checked
    || oForm.bouche[0].checked || oForm.bouche[1].checked
    || oForm.distThyro[0].checked){
  
    // Avertissement d'intubatino difficile
    $('divAlertIntubDiff').style.visibility = "visible";
  }else{
    $('divAlertIntubDiff').style.visibility = "hidden";
  }
  submitFormAjax(oForm, 'systemMsg')
}

var SchemaDentaire = {
  sId: null,
  iDossierMedicalId: 53{{*$consultation->_ref_patient->_ref_dossier_medical->_id*}},
  oListEtats: {11: 'bridge'} {{*$etat_des_dents|@json*}},
  fAlpha: 0.4,
  iSelectedDent: null,
  
  initialize: function(id, states) {
    // Class attributes
    this.sId = id;
    
    // Elements
    var oSchema = $(this.sId);
    oSchema.addClassName('schema-dentaire');
    var oMap = $(this.sId+"-map");
    
    // Menu initialization
    var oMenu = new Element('div');
    oMenu.id = this.sId+'-menu';
    oMenu.addClassName('dent-menu');
    
    // For each possible state, we add a link in the menu
    var oClose = new Element('a');
    oClose.innerHTML = 'x';
    oClose.addClassName('cancel');
    oMenu.insert({bottom: oClose});
    
    states.each (function (o) {
      var oOption = new Element('a');
      if (o) {
        oOption.innerHTML = o.capitalize();
      } else {
        oOption.innerHTML = 'Aucun';
      }
      oOption.addClassName(o);
      oOption.onclick = this.onSelectState;
      oMenu.insert({bottom: oOption});
    });
    oSchema.insert({bottom: oMenu});
    oMenu.hide();
  
    /* For each area in the map */
    oMap.childElements().each(
      function (o) {
        // We parse the coords attribute to get coordinates and radius of the circle area
        var area = o.coords.split(',');
        var x = parseInt(area[0]);
        var y = parseInt(area[1]);
        var r = parseInt(area[2]);
        
        // New div for the tooth
        var oDent = new Element('div');
        oDent.addClassName('dent');
        oDent.setStyle({
          marginTop: y-r-1+'px',
          marginLeft: x-r-1+'px',
          width: r*2+'px',
          height: r*2+'px'
        });
        oSchema.insert({top: oDent});
        oDent.id = SchemaDentaire.sId+'-dent-'+o.id.substr(5);
        oDent.dentId = o.id.substr(5);
        
        if (etat = SchemaDentaire.oListEtats[oDent.dentId]) {
          SchemaDentaire.setState(oDent.dentId, etat);
        }
        
        // Callbacks on the tooth
        oDent.onmouseover = SchemaDentaire.onMouseOver;
        oDent.onmouseout = SchemaDentaire.onMouseOut;
        oDent.onclick = SchemaDentaire.onClick;
      }
    );
    
    // Callback on the menu
    oMenu.onclick = this.onSelectState;
  },
  
  // Change the state of a tooth
  setState: function (id, state) {
    var dent = $(SchemaDentaire.sId+'-dent-'+id);
    
    if (state != 'cancel') {
      dent.setOpacity(SchemaDentaire.fAlpha);
      dent.className = 'dent';
      if (state != 'null') {
        dent.addClassName(state);
      }
      else {
        dent.setOpacity(1);
      }
    } else {
      dent.removeClassName('focus');
    }
  },
  
  onMouseOver: function (e) {
    e.target.addClassName('hover');
  },
  
  onMouseOut: function (e) {
    e.target.removeClassName('hover');
  },
  
  // Show the menu
  onClick: function (e) {
    if (!SchemaDentaire.iSelectedDent) {
      var dent = e.target;
      var menu = $(SchemaDentaire.sId+'-menu');
      
      dent.addClassName('focus');
      menu.setStyle({
       top: dent.cumulativeOffset().top + 'px',
       left: dent.cumulativeOffset().left + dent.getWidth() + 4 + 'px'
      });
      menu.show();
      
      SchemaDentaire.iSelectedDent = dent.dentId;
    }
  },
  
  // Selection of a new state in the menu
  onSelectState: function (e) {
    $(SchemaDentaire.sId+'-menu').hide();
    SchemaDentaire.setState(SchemaDentaire.iSelectedDent, e.target.className);
    SchemaDentaire.iSelectedDent = null;
  }
};

Main.add(function () {
  var states = [null, 'bridge', 'pivot', 'mobile', 'appareil'];
  SchemaDentaire.initialize("dents-schema", states);
} );
</script>
<form name="editFrmIntubation" action="?m=dPcabinet" method="post">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consult_anesth_aed" />
{{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
<table class="form">
  <tr>
    <th colspan="6" class="category">Condition d'intubation</th>
  </tr>
  <tr>
    {{foreach from=$consult_anesth->_enumsTrans.mallampati|smarty:nodefaults key=curr_mallampati item=trans_mallampati}}
    <td rowspan="2" class="button">
      <label for="mallampati_{{$curr_mallampati}}" title="Mallampati de {{$trans_mallampati}}">
        <img src="images/pictures/{{$curr_mallampati}}.gif" alt="{{$trans_mallampati}}" />
        <br />
        <input type="radio" name="mallampati" value="{{$curr_mallampati}}" {{if $consult_anesth->mallampati == $curr_mallampati}}checked="checked"{{/if}} onclick="verifIntubDifficileAndSave(this.form);" />
        {{$trans_mallampati}}
      </label>
    </td>
    {{/foreach}}

    <th>{{mb_label object=$consult_anesth field="bouche" defaultFor="bouche_m20"}}</th>
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
    <td colspan="4" rowspan="2" class="button">
      <div id="dents-schema">
        <img src="images/pictures/dents.png" width="503" height="420" border="0" usemap="#dents-schema-map" alt="" /> 
        <map name="dents-schema-map" id="dents-schema-map">
          <area shape="circle" coords="164,52, 11" href="#1" alt="" id="dent-11" />
          <area shape="circle" coords="145,63, 11" href="#1" alt="" id="dent-12" />
          <area shape="circle" coords="127,74, 12" href="#1" alt="" id="dent-13" />
          <area shape="circle" coords="118,93, 12" href="#1" alt="" id="dent-14" />
          <area shape="circle" coords="109,112, 13" href="#1" alt="" id="dent-15" />
          <area shape="circle" coords="103,137, 17" href="#1" alt="" id="dent-16" />
          <area shape="circle" coords="99,165, 16" href="#1" alt="" id="dent-17" />
          <area shape="circle" coords="98,193, 15" href="#1" alt="" id="dent-18" />
          <area shape="circle" coords="185,52, 11" href="#1" alt="" id="dent-21" />
          <area shape="circle" coords="204,63, 11" href="#1" alt="" id="dent-22" />
          <area shape="circle" coords="222,74, 12" href="#1" alt="" id="dent-23" />
          <area shape="circle" coords="231,93, 12" href="#1" alt="" id="dent-24" />
          <area shape="circle" coords="240,113, 13" href="#1" alt="" id="dent-25" />
          <area shape="circle" coords="246,137, 17" href="#1" alt="" id="dent-26" />
          <area shape="circle" coords="249,165, 16" href="#1" alt="" id="dent-27" />
          <area shape="circle" coords="251,193, 15" href="#1" alt="" id="dent-28" />
          <area shape="circle" coords="183,375, 9" href="#1" alt="" id="dent-31" />
          <area shape="circle" coords="198,368, 9" href="#1" alt="" id="dent-32" />
          <area shape="circle" coords="212,357, 11" href="#1" alt="" id="dent-33" />
          <area shape="circle" coords="225,341, 11" href="#1" alt="" id="dent-34" />
          <area shape="circle" coords="234,322, 12" href="#1" alt="" id="dent-35" />
          <area shape="circle" coords="243,298, 18" href="#1" alt="" id="dent-36" />
          <area shape="circle" coords="247,269, 16" href="#1" alt="" id="dent-37" />
          <area shape="circle" coords="251,241, 15" href="#1" alt="" id="dent-38" />
          <area shape="circle" coords="166,375, 9" href="#1" alt="" id="dent-41" />
          <area shape="circle" coords="151,367, 9" href="#1" alt="" id="dent-42" />
          <area shape="circle" coords="137,357, 11" href="#1" alt="" id="dent-43" />
          <area shape="circle" coords="124,342, 11" href="#1" alt="" id="dent-44" />
          <area shape="circle" coords="114,323, 12" href="#1" alt="" id="dent-45" />
          <area shape="circle" coords="106,298, 18" href="#1" alt="" id="dent-46" />
          <area shape="circle" coords="102,269, 16" href="#1" alt="" id="dent-47" />
          <area shape="circle" coords="97,242, 15" href="#1" alt="" id="dent-48" />
          <area shape="circle" coords="366,133, 7" href="#1" alt="" id="dent-51" />
          <area shape="circle" coords="355,139, 8" href="#1" alt="" id="dent-52" />
          <area shape="circle" coords="346,150, 9" href="#1" alt="" id="dent-53" />
          <area shape="circle" coords="338,166, 11" href="#1" alt="" id="dent-54" />
          <area shape="circle" coords="333,185, 12" href="#1" alt="" id="dent-55" />
          <area shape="circle" coords="379,133, 7" href="#1" alt="" id="dent-61" />
          <area shape="circle" coords="390,139, 8" href="#1" alt="" id="dent-62" />
          <area shape="circle" coords="399,150, 9" href="#1" alt="" id="dent-63" />
          <area shape="circle" coords="405,166, 11" href="#1" alt="" id="dent-64" />
          <area shape="circle" coords="411,185, 12" href="#1" alt="" id="dent-65" />
          <area shape="circle" coords="378,290, 6" href="#1" alt="" id="dent-71" />
          <area shape="circle" coords="387,284, 7" href="#1" alt="" id="dent-72" />
          <area shape="circle" coords="398,274, 8" href="#1" alt="" id="dent-73" />
          <area shape="circle" coords="405,262, 8" href="#1" alt="" id="dent-74" />
          <area shape="circle" coords="413,246, 10" href="#1" alt="" id="dent-75" />
          <area shape="circle" coords="367,290, 6" href="#1" alt="" id="dent-81" />
          <area shape="circle" coords="357,284, 7" href="#1" alt="" id="dent-82" />
          <area shape="circle" coords="346,274, 8" href="#1" alt="" id="dent-83" />
          <area shape="circle" coords="339,261, 8" href="#1" alt="" id="dent-84" />
          <area shape="circle" coords="330,247, 10" href="#1" alt="" id="dent-85" />
        </map>
      </div>
    </td>
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
    <td colspan="6" class="button">
      <div id="divAlertIntubDiff" style="float:right;color:#F00;{{if !$consult_anesth->_intub_difficile}}visibility:hidden;{{/if}}"><strong>Intubation Difficile Prévisible</strong></div>
    </td>
  </tr>
</table>
</form>