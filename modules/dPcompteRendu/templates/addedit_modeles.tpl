{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
<script type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

var Modele = {
  copy: function (oForm) {
    oForm = oForm || document.editFrm;
    
    {{if $droit}}
      if(confirm('{{tr}}CCompteRendu-already-access{{/tr}}')){
        oForm.compte_rendu_id.value = "";
        
        {{if $isPraticien}}
        oForm.chir_id.value = "{{$user_id}}";
        oForm.function_id.value = "";
        {{/if}}
        
        oForm.nom.value = "Copie de "+oForm.nom.value;
        oForm.onsubmit(); 
      }
    {{else}}
      oForm.compte_rendu_id.value = "";
      oForm.chir_id.value = "{{$user_id}}";
      oForm.nom.value = "Copie de "+oForm.nom.value;
      oForm.onsubmit();
    {{/if}}
  },
  
  create: function() {
    var url = new Url;
    url.setModuleTab("dPcompteRendu", "addedit_modeles");
    url.addParam("compte_rendu_id", "0");
    url.redirect();
  },
  
  preview: function(id) {
    var url = new Url("dPcompteRendu", "print_cr");
    url.addParam("compte_rendu_id", id);
    url.popup(800, 800);
  },

  preview_layout: function() {
    var header_size = parseInt($("editFrm_height").value);
    $("header_footer_content").style["height"] = ((header_size / 728.5)*80).round() + "px";
    $("body_content").style["height"] = (((728.5 - header_size) / 728.5)*80).round() + "px";
  },

  generate_auto_height: function() {
    var content = window.FCKeditorAPI ? FCKeditorAPI.Instances.source.GetHTML() : $V(form.source);
    var container = new Element("div", {style: "width: 17cm; padding: 0; margin: 0; position: absolute; left: -1500px; bottom: 200px;"}).insert(content);
    $$('body')[0].insert(container);
    // Calcul approximatif de la hauteur
    getForm("editFrm").height.value = (container.getHeight() * 1.4).round();
  }
};

// Taleau des categories en fonction de la classe du compte rendu
var listObjectClass = {{$listObjectClass|@json}};
var aTraducClass = {{$listObjectAffichage|@json}};

function loadObjectClass(value) {
  var form = document.editFrm;
  var select = $(form.elements.object_class);
  var children = select.childElements();
  
  if (children.length > 0)
    children[0].nextSiblings().invoke('remove');
  
  // Insert new ones
  $H(listObjectClass).each(function(pair){
    select.insert(new Element('option', {value: pair.key, selected: pair.key == value}).update(aTraducClass[pair.key]));
  });
  
  // Check null position
  select.fire("ui:change");
 
  loadCategory();
}

function loadCategory(value) {
  var form = document.editFrm;
  var select = $(form.elements.file_category_id);
  var children = select.childElements();
  
  if (children.length > 0)
    children[0].nextSiblings().invoke('remove');
  
  // Insert new ones
  $H(listObjectClass[form.elements.object_class.value]).each(function(pair){
    select.insert(new Element('option', {value: pair.key, selected: pair.key == value}).update(pair.value));
  });
}

function submitCompteRendu(){
  (function(){
    var form = getForm("editFrm");
    if(checkForm(form) && User.id) {
      form.submit();
    }
  }).defer();
}

// Catches Ctrl+s and Command+s
document.observe('keydown', function(e){
  var keycode = Event.key(e);
  if(keycode == 83 && (e.ctrlKey || e.metaKey)){
    submitCompteRendu();
    Event.stop(e);
  }
});

{{if $pdf_thumbnails == 1}}
var Thumb = {
  thumb_up2date: 1,
  choixaffiche: function() {
    $("thumbs").toggle();
		var editeur = $("htmlarea");
    var colspan_editeur = editeur.readAttribute("colspan");
    colspan_editeur == '1' ? editeur.writeAttribute("colspan",'2') : editeur.writeAttribute("colspan",'1');
  },
  
  refreshthumbs: function(first_time) {
	  this.thumb_up2date = 1;
	  $("thumbs").setOpacity(1);
    var form = getForm("editFrm");
    var url = new Url("dPcompteRendu", "ajax_pdf_and_thumbs");
    var content = (window.FCKeditorAPI && FCKeditorAPI.Instances.source.GetHTML()) ? FCKeditorAPI.Instances.source.GetHTML() : $V(form.source);
    url.addParam("compte_rendu_id", '{{$compte_rendu->_id}}');
    url.addParam("content"        , content);
    url.addParam("mode"           , "modele");
    url.addParam("header_id"      , $V(form.editFrm_header_id));
    url.addParam("footer_id"      , $V(form.editFrm_footer_id));
    url.addParam("type"           , $V(form.editFrm_type));
    url.addParam("height"         , $V(form.editFrm_height));
    url.addParam("stream"         , 0);
    url.addParam("generate_thumbs", 1);
    url.addParam("user_id", '{{$user_id}}');
    url.addParam("first_time", first_time);
		
    if (form.type.value == "body") {
      url.addParam("page_width"     , form.page_width.value);
      url.addParam("page_height"    , form.page_height.value);
      url.addParam("page_format"    , form._page_format.value);
      url.addParam("margins[]",[form.margin_top.value,
                                form.margin_right.value,
                                form.margin_bottom.value,
                                form.margin_left.value]);
      url.addParam("orientation", $V(PageFormat.form._orientation));
    }
    url.requestUpdate("thumbs",{method: "post", getParameters: {m: "dPcompteRendu", a: "ajax_pdf_and_thumbs"}});
  },
	
	old: function() {
		if(this.thumb_up2date) {
		  thumb_0 = $("thumb_0");
			thumbs = $("thumbs");
		  thumbs.setOpacity(0.5);
			this.save_onclick = thumb_0.getAttribute("onclick");
			thumb_0.setAttribute("onclick", "");
			var mess = new Element('div', {id: 'mess', style: 'position: absolute; width: 160px; font-size: 12pt; font-weight: bold;'}).update("<br/><br/>Vignettes obsolètes : cliquez sur le bouton pour réactualiser.<br/>");
			mess = mess.insert({bottom: new Element('button', {id: 'refresh', class: 'change notext', type: 'button', title: 'Rafraîchir les vignettes', onclick: 'Thumb.refreshthumbs();'})});
			thumbs.insert({top: mess});
			this.thumb_up2date = 0;
		}
	}
}

function FCKeditor_OnComplete(editorInstance) {
  editorInstance.Events.AttachEvent('OnSelectionChange', loadold);
	Thumb.content = editorInstance.GetHTML(false);

	editorInstance.Events.AttachEvent('OnSelectionChange', FCKeventChanger );
  var fck_iframe = document.getElementById('source___Frame');
  var fck_editing_area = fck_iframe.contentDocument.getElementById('xEditingArea');
  fck_editing_area.style.height = '100.1%';
  setTimeout(function() {fck_editing_area.style.height = '100%'}, 100); 
	
  Thumb.refreshthumbs(1);
}

function loadold(editorInstance) {
  if (editorInstance.IsDirty() && editorInstance.GetHTML(false) != Thumb.content) {
	  Thumb.old();
	}
}

function FCKeventChanger(editorInstance) {
  if(editorInstance.LastOnChangeTimer) {
    FormObserver.FCKChanged(editorInstance.LastOnChangeTimer);
  }
}
{{else}}
var Thumb = {
  old: function(){}
};
{{/if}}

Main.add(function () {
  loadObjectClass('{{$compte_rendu->object_class}}');
  loadCategory('{{$compte_rendu->file_category_id}}');
  {{if $compte_rendu->_id && $droit && $pdf_thumbnails}}
    PageFormat.init(getForm("editFrm"));
  {{/if}}
});

</script>

{{if $pdf_thumbnails == 1}}
  <form style="display: none;" name="download-pdf-form" target="_blank" method="post"
    action="?m=dPcompteRendu&amp;a=ajax_pdf_and_thumbs"
    onsubmit="PageFormat.completeForm(); this.submit();">
    <input type="hidden" name="content" value="" />
    <input type="hidden" name="compte_rendu_id" value="{{$compte_rendu->_id}}"/>
    <input type="hidden" name="suppressHeaders" value="1" />
    <input type="hidden" name="save_file" value="0" />
    <input type="hidden" name="header_id" value="0" />
    <input type="hidden" name="footer_id" value="0" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="type" value="" />
    <input type="hidden" name="height" value="0" />
    <input type="hidden" name="generate_thumbs" value="0" />
    <input type="hidden" name="stream" value="1" />
  </form>
{{/if}}

<form name="editFrm" action="?m={{$m}}" method="post" 
 onsubmit="Url.ping({onComplete: submitCompteRendu}); return false;"
 class="{{$compte_rendu->_spec}}">

<table class="main">
  <tr>
    <td style="width: 0.1%;">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_modele_aed" />
      {{mb_key object=$compte_rendu}}
      {{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
      {{if $compte_rendu->compte_rendu_id}}
      <button class="new" type="button" onclick="Modele.create()">
        {{tr}}CAideSaisie.create{{/tr}}
      </button>
      {{/if}}
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{if $compte_rendu->_id}}
              {{mb_include module=system template=inc_object_idsante400 object=$compte_rendu}}
              {{mb_include module=system template=inc_object_history object=$compte_rendu}}
            {{/if}}
            {{tr}}CCompteRendu-informations{{/tr}}
          </th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="nom"}}</th>
          <td>
          {{if $droit}}
            {{mb_field object=$compte_rendu field="nom" style="width: 15em"}}
          {{else}}
            {{mb_field object=$compte_rendu field="nom" readonly="readonly"}}
          {{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="group_id"}}</th>
          <td>
            {{if !$droit}}
               <input type="hidden" name="group_id" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="group_id" class="{{$compte_rendu->_props.group_id}}" style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-set-etab{{/tr}}</option>
              {{foreach from=$listEtab item=curr_etab}}
              <option value="{{$curr_etab->_id}}" {{if $curr_etab->_id == $compte_rendu->group_id}} selected="selected" {{/if}}>
              {{$curr_etab->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$compte_rendu field="function_id"}}</th>
          <td>
            {{if !$droit}}
               <input type="hidden" name="function_id" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="function_id" class="{{$compte_rendu->_props.function_id}}" style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-set-function{{/tr}}</option>
              {{foreach from=$listFunc item=curr_func}}
              <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->_id}}" {{if $curr_func->_id == $compte_rendu->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
                
        <tr>
          <th>{{mb_label object=$compte_rendu field="chir_id"}}</th>
          <td>
            {{if !$droit}}
              <input type="hidden" name="chir_id" value="{{$mediuser->_id}}" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="chir_id" class="{{$compte_rendu->_props.chir_id}}" style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-set-user{{/tr}}</option>
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->_id}}" {{if $curr_prat->_id == $compte_rendu->chir_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field=type}}</th>
          <td>
            {{if $droit}}
              {{mb_field object=$compte_rendu field=type onchange="updateType();  Thumb.old();" style="width: 15em;"}}
            {{else}}
              {{mb_field object=$compte_rendu field=type disabled="disabled" style="width: 15em;"}}
            {{/if}}
          
            {{if $compte_rendu->_id}}
            <script type="text/javascript">
            function updateType() {
              var oForm = document.editFrm;
              var bBody = oForm.type.value == "body";
              var bHeader = oForm.type.value == "header";
              {{if $pdf_thumbnails == 1}}
              if(bHeader) {
                $("preview_page").insert({top: $("header_footer_content").remove()});
                $("preview_page").insert({bottom: $("body_content").remove()});
              }
              else {
                $("preview_page").insert({bottom: $("header_footer_content").remove()});
                $("preview_page").insert({top: $("body_content").remove()});
              }
              // layout
              $("page_layout").setVisible(bBody);
              {{/if}}
              $("layout_header_footer").setVisible(!bBody);
              
              // Height
              $("height").setVisible(!bBody);
              if (bBody) $V(oForm.height, '');

              // Footers
              var oFooter = $("footers");
              if (oFooter) {
                oFooter.setVisible(bBody);
                if (!bBody) $V(oForm.footer_id, '');
              }

              // Headers
              var oHeader = $("headers");
              if (oHeader) {
                oHeader.setVisible(bBody);
                if (!bBody) $V(oForm.header_id, '');
              }
              Modele.preview_layout();           
            }
            
            Main.add(updateType);
            </script>
            {{/if}}
          </td>
        </tr>
        
        

        {{if $headers|@count}}
        <tr id="headers">
          <th>{{mb_label object=$compte_rendu field=header_id}}</th>
          <td>
            <select name="header_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.header_id}}" {{if !$droit}}disabled="disabled"{{/if}} style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-set-header{{/tr}}</option>
              {{foreach from=$headers item=headersByOwner key=owner}}
              <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                {{foreach from=$headersByOwner item=_header}}
                <option value="{{$_header->_id}}" {{if $compte_rendu->header_id == $_header->_id}}selected="selected"{{/if}}>{{$_header->nom}}</option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}None{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
          
        {{if $footers|@count}}
        <tr id="footers">
          <th>{{mb_label object=$compte_rendu field=footer_id}}</th>
          <td>
            <select name="footer_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.footer_id}}" {{if !$droit}}disabled="disabled"{{/if}} style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-set-footer{{/tr}}</option>
              {{foreach from=$footers item=footersByOwner key=owner}}
              <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                {{foreach from=$footersByOwner item=_footer}}
                <option value="{{$_footer->_id}}" {{if $compte_rendu->footer_id == $_footer->_id}}selected="selected"{{/if}}>{{$_footer->nom}}</option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}None{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}
          
        <tr>
          <th>{{mb_label object=$compte_rendu field="object_class"}}</th>
          <td>
            <select name="object_class" class="{{$compte_rendu->_props.object_class}}" onchange="loadCategory()" style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-set-object{{/tr}}</option>
            </select>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$compte_rendu field="file_category_id"}}</th>
          <td>
            <select name="file_category_id" class="{{$compte_rendu->_props.file_category_id}}" style="width: 15em;">
              <option value="">&mdash; {{tr}}CCompteRendu-no-category{{/tr}}</option>
            </select>
          </td>
        </tr>
        
        {{if $compte_rendu->_id}}
        
        
        {{if $pdf_thumbnails == 1}}
        <tr>
          <th class="category" colspan="2">
          	{{tr}}CCompteRendu-Pagelayout{{/tr}}
					  <button class="hslip notext" type="button" title="Afficher / Masquer les vignettes"
                    onclick = "Thumb.choixaffiche();"></button>	
					</th>
        </tr>
				<tr id="page_layout" style="display: none;">
          <td colspan="2">
            {{include file="inc_page_layout.tpl"}}
          </td>
        </tr>
        {{/if}}
        <tr id="height"  style="display: none;">
          <th>{{mb_label object=$compte_rendu field=height}}</th>
          <td>
          {{if $droit}}
            <button type="button" class="change" onclick="Thumb.old(); Modele.generate_auto_height(); Modele.preview_layout();">{{tr}}Générer hauteur auto{{/tr}}</button><br/>
              {{mb_field object=$compte_rendu field=height increment=true form=editFrm onchange="Thumb.old(); Modele.preview_layout();" step="10" onkeyup="Modele.preview_layout();"}}
          {{else}}
            {{mb_field object=$compte_rendu field=height readonly="readonly"}}
          {{/if}}
          </td>
        </tr>
        <tr id="layout_header_footer" style="display: none;">
          <th>{{tr}}CCompteRendu-preview-header-footer{{/tr}}</th>
          <td>
            <div id="preview_page" style="color: #000; height: 84px; padding: 7px; width: 58px; background: #fff; border: 1px solid #000; overflow: hidden;">
              <div id="header_footer_content" style="color: #000; white-space: normal; background: #fff; overflow: hidden; margin: -1px; height: 30px; width: 100%; font-size: 3px;">
                {{include file="lorem_ipsum.tpl"}}
              </div>
              <hr style="width: 100%; margin-top: 3px; margin-bottom: 3px;"/>
              <div id="body_content" style="margin: -1px; color: #999; height: 50px; width: 100%; font-size: 3px; white-space: normal; overflow: hidden;">
                {{include file="lorem_ipsum.tpl"}}  
              </div>
            </div>
          </td>
        </tr>
        {{/if}}

        <tr>
          {{if $droit}}
            <td class="button" colspan="2">
            {{if $compte_rendu->_id}}
            <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le modèle',objName:'{{$compte_rendu->nom|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
            </td>
          {{/if}}
        </tr>

        <tr>
          <th class="category" colspan="2">{{tr}}CCompteRendu-other-actions{{/tr}}</th>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
             <button type="button" class="add" onclick="Modele.copy(this.form)">{{tr}}Duplicate{{/tr}}</button>
             <button type="button" class="search" onclick="Modele.preview($V(this.form.compte_rendu_id))">{{tr}}Preview{{/tr}}</button>
          </td>
        </tr>
        
      </table>
    </td>
    
    <td class="greedyPane" style="height: 500px">
      {{if $compte_rendu->_id}}
        {{if !$droit}}
          <div class="big-info">
            Le présent modèle est en lecture seule. 
            <br/>Il comporte en l'état {{$compte_rendu->source|count_words}} mots.
            <br/>Vous pouvez le copier pour votre propre usage en cliquant sur <strong>Dupliquer</strong>. 
          </div>
          <hr/>
        {{/if}}
        {{mb_field object=$compte_rendu field="source" id="htmlarea"}}
      {{/if}}
    </td>
    {{if $pdf_thumbnails == 1 && $compte_rendu->_id}}
      <td style="width: 0.1%;">
        <div id="thumbs" style="overflow: auto; overflow-x: hidden; width: 160px; height: 580px; text-align: center;">
        </div>
      </td>
    {{/if}}
  </tr>
</table>    
</form>     
