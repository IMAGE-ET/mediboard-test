{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

<script type="text/javascript">
window.same_print = {{$conf.dPcompteRendu.CCompteRendu.same_print}};
window.pdf_thumbnails = {{$pdf_thumbnails|@json}} == 1;

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

var Modele = {
  copy: function (oForm) {
    oForm = oForm || document.editFrm;
    
    {{if $droit}}
      if(confirm('{{tr}}CCompteRendu-already-access{{/tr}}')){
        $V(oForm.compte_rendu_id, "");
        
        {{if $isPraticien}}
        $V(oForm.user_id, "{{$user_id}}");
        $V(oForm.function_id, "");
        {{/if}}
        
        $V(oForm.nom, "Copie de "+ $V(oForm.nom));
        oForm.onsubmit(); 
      }
    {{else}}
      $V(oForm.compte_rendu_id, "");
      $V(oForm.user_id, "{{$user_id}}");
      $V(oForm.nom, "Copie de "+ $V(oForm.nom));
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
    var header_size = parseInt($V(getForm("editFrm").elements.height));
    if (!isNaN(header_size)) {
      $("header_footer_content").style["height"] = ((header_size / 728.5)*80).round() + "px";
    }
    $("body_content").style["height"] =  "80px";
  },

  generate_auto_height: function() {
    var content = window.CKEDITOR.instances.htmlarea ? CKEDITOR.instances.htmlarea.getData() : $V(form.source);
    var container = new Element("div", {style: "width: 17cm; padding: 0; margin: 0; position: absolute; left: -1500px; bottom: 200px;"}).insert(content);
    $$('body')[0].insert(container);
    // Calcul approximatif de la hauteur
    $V(getForm("editFrm").height, (container.getHeight()).round());
  },
  showUtilisation: function() {
    var url = new Url("dPcompteRendu", "ajax_show_utilisation");
    url.addParam("compte_rendu_id", "{{$compte_rendu->_id}}");
    url.requestModal(640, 480);
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

function submitCompteRendu(callback){
  // Do not store the content editable of the class field spans.
  {{if $compte_rendu->_id}}
    window.toggleContentEditable(true);
  {{/if}}
  
  (function(){
    var form = getForm("editFrm");
    if(checkForm(form) && User.id) {
      if (callback)
        callback();
      form.submit();
    }
  }).defer();
}

function reloadHeadersFooters() {
  {{if $compte_rendu->_id}}
    if ($("headers") && $("footers") && $("prefaces") && $("endings")) {
      var oForm = getForm("editFrm");
      var compte_rendu_id = $V(oForm.compte_rendu_id);
      var object_class = $V(oForm.object_class);
      
      var url = new Url("dPcompteRendu", "ajax_headers_footers");
      url.addParam("compte_rendu_id", compte_rendu_id);
      url.addParam("object_class", object_class);
      url.addParam("type", "header");
      url.requestUpdate(oForm.header_id);
      
      url = new Url("dPcompteRendu", "ajax_headers_footers");
      url.addParam("compte_rendu_id", compte_rendu_id);
      url.addParam("object_class", object_class);
      url.addParam("type", "preface");
      url.requestUpdate(oForm.preface_id);
      
      url = new Url("dPcompteRendu", "ajax_headers_footers");
      url.addParam("compte_rendu_id", compte_rendu_id);
      url.addParam("object_class", object_class);
      url.addParam("type", "ending");
      url.requestUpdate(oForm.ending_id);
      
      url = new Url("dPcompteRendu", "ajax_headers_footers");
      url.addParam("compte_rendu_id", compte_rendu_id);
      url.addParam("object_class", object_class);
      url.addParam("type", "footer");
      url.requestUpdate(oForm.footer_id);
    } 
  {{/if}}
}

function setTemplateName(object_class, name, type) {
  var form = getForm("editFrm");
  $V(form.object_class, object_class);
  $V(form.nom, name);
  $V(form.type, type);
  Control.Modal.close();
}

</script>

{{mb_script module=compteRendu script=thumb}}

<script type="text/javascript">

Main.add(function () {
  loadObjectClass('{{$compte_rendu->object_class}}');
  loadCategory('{{$compte_rendu->file_category_id}}');
  {{if $compte_rendu->_id && $droit && $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
    Thumb.modele_id = '{{$compte_rendu->_id}}';
    Thumb.user_id = '{{$user_id}}';
    Thumb.mode = "modele";
    PageFormat.init(getForm("editFrm"));
  {{/if}}
});

Main.add(Control.Tabs.create.curry('tabs-edit'));

</script>

<div id="choose_template_name" class="modal" style="display: none; width: 600px;">
  <table class="tbl">
    <tr>
      <th class="title narrow">Nom</th>
      <th class="title">Description</th>
      <th class="title narrow"></th>
    </tr>
    {{foreach from=$special_names item=_names_by_class key=_class}}
      <tr>
        <th class="category" colspan="3">{{tr}}{{$_class}}{{/tr}}</th>
      </tr>
      {{foreach from=$_names_by_class key=_name item=_type}}
        <tr>
          <td>
            {{$_name}}
          </td>
          <td class="text">
            {{tr}}CCompteRendu.description_{{$_name}}{{/tr}}
          </td>
          <td class="narrow">
            <button type="button" class="tick notext"
              onclick="setTemplateName('{{$_class}}', '{{$_name}}', '{{$_type}}');"></button>
          </td>
        </tr>
      {{/foreach}}
    {{/foreach}}
    <tr>
      <td class="button" colspan="3">
        <button class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>

{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
  <form style="display: none;" name="download-pdf-form" target="_blank" method="post"
    action="?m=dPcompteRendu&amp;a=ajax_pdf"
    onsubmit="PageFormat.completeForm();">
    <input type="hidden" name="content" value="" />
    <input type="hidden" name="compte_rendu_id" value="{{$compte_rendu->_id}}"/>
    <input type="hidden" name="suppressHeaders" value="1" />
    <input type="hidden" name="save_file" value="0" />
    <input type="hidden" name="header_id" value="0" />
    <input type="hidden" name="footer_id" value="0" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="type" value="" />
    <input type="hidden" name="height" value="0" />
    <input type="hidden" name="stream" value="1" />
  </form>
{{/if}}

<form name="editFrm" action="?m={{$m}}" method="post" 
 onsubmit="Url.ping({onComplete: submitCompteRendu}); return false;"
 class="{{$compte_rendu->_spec}}">

{{if (!$pdf_thumbnails || !$app->user_prefs.pdf_and_thumbs || $compte_rendu->type != "body")}}
  <input type="hidden" name="fast_edit_pdf" value="{{$compte_rendu->fast_edit_pdf}}" />
{{/if}}

{{if $compte_rendu->type != "body"}}
  <input type="hidden" name="fast_edit" value="{{$compte_rendu->fast_edit}}" />
{{/if}}

<table class="main">
  <tr>
    <td style="width: 300px;">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_modele_aed" />
      {{mb_key object=$compte_rendu}}
      {{mb_field object=$compte_rendu field="object_id" hidden=1}}
      
      {{if $compte_rendu->_id}}
        <button class="new" type="button" onclick="Modele.create()">
          {{tr}}CCompteRendu-title-create{{/tr}}
        </button>
        {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
          <button class="hslip notext" type="button" title="Afficher / Masquer les vignettes"
            onclick = "Thumb.choixAffiche(0);" style="float: right;"></button>  
        {{/if}}
      {{/if}}
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{if $compte_rendu->_id}}
              {{mb_include module=system template=inc_object_notes      object=$compte_rendu}}
              {{mb_include module=system template=inc_object_idsante400 object=$compte_rendu}}
              {{mb_include module=system template=inc_object_history    object=$compte_rendu}}
            {{/if}}
            {{tr}}CCompteRendu-informations{{/tr}}
          </th>
        </tr>
      </table>

      <ul id="tabs-edit" class="control_tabs small">
        <li><a href="#info">Informations</a></li>
        <li><a href="#layout">Mise en page</a></li>
      </ul>
    
      <hr class="control_tabs" />
      
      <table class="form" id="info" style="display: none;">
        <tr>
          <th>{{mb_label object=$compte_rendu field="nom"}}</th>
          <td>
          {{if $droit}}
            {{mb_field object=$compte_rendu field="nom" style="width: 12em"}}
            <button type="button" class="search notext" title="Choisir un nom réservé" onclick="Modal.open('choose_template_name')"></button>
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
              <option value="">&mdash; {{tr}}Associate{{/tr}}</option>
              {{foreach from=$listEtab item=curr_etab}}
              <option value="{{$curr_etab->_id}}" {{if $curr_etab->_id == $compte_rendu->group_id}} selected="selected" {{/if}}>
              {{$curr_etab}}
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
              <option value="">&mdash; {{tr}}Associate{{/tr}}</option>
              {{foreach from=$listFunc item=curr_func}}
              <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->_id}}" {{if $curr_func->_id == $compte_rendu->function_id}} selected="selected" {{/if}}>
              {{if $smarty.session.browser.name == "msie"}}
                {{$curr_func->_view|truncate:45}}
              {{else}}
                {{$curr_func->_view}}
              {{/if}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
                
        <tr>
          <th>{{mb_label object=$compte_rendu field="user_id"}}</th>
          <td>
            {{if !$droit}}
              <input type="hidden" name="user_id" value="{{$mediuser->_id}}" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="user_id" class="{{$compte_rendu->_props.user_id}}" style="width: 15em;">
              <option value="">&mdash; {{tr}}Associate{{/tr}}</option>
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->_id}}" {{if $curr_prat->_id == $compte_rendu->user_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        {{if $compte_rendu->type == "body" || !$compte_rendu->_id}}
          <tr>
            <th>{{mb_label object=$compte_rendu field="fast_edit"}}</th>
            <td>
              {{mb_field object=$compte_rendu field="fast_edit"}}
            </td>
          </tr>
        
          {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
            <tr>
              <th style="text-align: right;">
                {{mb_label object=$compte_rendu field="fast_edit_pdf" style="display: none"}}
                <label class="notNullOK" title="{{tr}}CCompteRendu-fast_edit_pdf-desc{{/tr}}">
                  <strong>PDF</strong>
                </label>
              </th>
              <td>
                {{mb_field object=$compte_rendu field="fast_edit_pdf"}}
              </td>
            </tr>
          {{/if}}
        {{/if}}
        <tr>
          <th>{{mb_label object=$compte_rendu field="purgeable"}}</th>
          <td>{{mb_field object=$compte_rendu field="purgeable"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="font"}}</th>
          <td>{{mb_field object=$compte_rendu field="font" emptyLabel="Choose" style="width: 15em"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="size"}}</th>
          <td>{{mb_field object=$compte_rendu field="size" emptyLabel="Choose" style="width: 15em"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$compte_rendu field=type}}</th>
          <td>
            {{if $droit}}
              {{mb_field object=$compte_rendu field=type onchange="updateType();  Thumb.old();" style="width: 15em;"}}
            {{else}}
              {{mb_field object=$compte_rendu field=type disabled="disabled" style="width: 15em;"}}
            {{/if}}
          
            <script type="text/javascript">
              function updateType() {
                {{if $compte_rendu->_id}}
                  var oForm = document.editFrm;
                  var bBody = oForm.type.value == "body";
                  var bHeader = oForm.type.value == "header";
                  var bOther  = (oForm.type.value == "preface" || oForm.type.value == "ending");
                  
                  if (bHeader) {
                    $("preview_page").insert({top   : $("header_footer_content").remove()});
                    $("preview_page").insert({bottom: $("body_content").remove()});
                  }
                  else {
                    $("preview_page").insert({bottom: $("header_footer_content").remove()});
                    $("preview_page").insert({top   : $("body_content").remove()});
                  }
                  
                  // General Layout
                  $("layout").down('.fields').setVisible(!bOther);
                  $("layout").down('.notice').setVisible(bOther);
                  
                  // Page layout
                  if (window.pdf_thumbnails && window.Preferences.pdf_and_thumbs == 1) {
                    $("page_layout").setVisible(bBody);
                  }
                  $("layout_header_footer").setVisible(!bBody && !bOther);
                  
                  
                  // Height
                  $("height").setVisible(!bBody && !bOther);
                  if (bBody) $V(oForm.height, '');
    
                  // Headers, Footers, Prefaces and Endings
                  var oComponent = $("components");
                  if (oComponent) {
                    oComponent.setVisible(bBody);
                    if (!bBody) {
                      $V(oForm.header_id , '');
                      $V(oForm.footer_id , '');
                      $V(oForm.preface_id, '');
                      $V(oForm.ending_id , '');
                    }
                  }
                  
                  Modele.preview_layout();
                {{/if}}
              }
              
              Main.add(updateType);
            </script>
            
          </td>
        </tr>
        
        <tbody id="components">

          {{if $headers|@count}}
            <tr id="headers">
              <th>{{mb_label object=$compte_rendu field=header_id}}</th>
              <td>
                <select name="header_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.header_id}}" {{if !$droit}}disabled="disabled"{{/if}} style="width: 15em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
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
          
          {{if $prefaces|@count}}
            <tr id="prefaces">
              <th>{{mb_label object=$compte_rendu field=preface_id}}</th>
              <td>
                <select name="preface_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.preface_id}}" {{if !$droit}}disabled="disabled"{{/if}} style="width: 15em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{foreach from=$prefaces item=prefacesByOwner key=owner}}
                  <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                    {{foreach from=$prefacesByOwner item=_preface}}
                    <option value="{{$_preface->_id}}" {{if $compte_rendu->preface_id == $_preface->_id}}selected="selected"{{/if}}>{{$_preface->nom}}</option>
                    {{foreachelse}}
                    <option value="" disabled="disabled">{{tr}}None{{/tr}}</option>
                    {{/foreach}}
                  </optgroup>
                  {{/foreach}}
                </select>
              </td>
            </tr>
          {{/if}}
          
          {{if $endings|@count}}
            <tr id="endings">
              <th>{{mb_label object=$compte_rendu field=ending_id}}</th>
              <td>
                <select name="ending_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.ending_id}}" {{if !$droit}}disabled="disabled"{{/if}} style="width: 15em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{foreach from=$endings item=endingsByOwner key=owner}}
                  <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                    {{foreach from=$endingsByOwner item=_ending}}
                    <option value="{{$_ending->_id}}" {{if $compte_rendu->ending_id == $_ending->_id}}selected="selected"{{/if}}>{{$_ending->nom}}</option>
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
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
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
        </tbody>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="object_class"}}</th>
          <td>
            <select name="object_class" class="{{$compte_rendu->_props.object_class}}" onchange="loadCategory(); reloadHeadersFooters();" style="width: 15em;">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            </select>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$compte_rendu field="file_category_id"}}</th>
          <td>
            <select name="file_category_id" class="{{$compte_rendu->_props.file_category_id}}" style="width: 15em;">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="purge_field"}}</th>
          <td>{{mb_field object=$compte_rendu field="purge_field"}}</td>
        </tr>
      </table>
      
        {{if $compte_rendu->_id}}
          <table class="form" id="layout" style="display: none;">
            <tr class="notice">
              <td>
                <div class="small-info">
                  Ce modèle n'est pas un corps de texte.
                </div>
              </td>
            </tr>
            
            <tbody class="fields">
              {{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
              <tr>
                <th class="category" colspan="2">
                  {{tr}}CCompteRendu-Pagelayout{{/tr}}
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
                  <button type="button" class="change" onclick="Thumb.old(); Modele.generate_auto_height(); Modele.preview_layout();">{{tr}}CCompteRendu.auto_height{{/tr}}</button><br/>
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
            </tbody>
          </table>
        {{else}}
          <div id="layout" style="display: none; " class="small-info">
            Aucune mise en page possible
          </div>
        {{/if}}

    <hr />
    <table class="form" style="width: 265px;">
    
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
             <button type="button" class="duplicate" onclick="Modele.copy(this.form)">{{tr}}Duplicate{{/tr}}</button>
             <button type="button" class="search" onclick="Modele.preview($V(this.form.compte_rendu_id))">{{tr}}Preview{{/tr}}</button>
             <br />
             <button type="button" class="search" onclick="Modele.showUtilisation()">Utilisation ({{$compte_rendu->_count_utilisation}})</button>
          </td>
        </tr>
        
      </table>
    </td>
    
    <td style="height: 500px; max-width: 600px !important;" class="greedyPane">
      {{if $compte_rendu->_id}}
        {{if !$droit}}
          <div class="big-info">
            Le présent modèle est en lecture seule. 
            <br/>Il comporte en l'état {{$compte_rendu->_source|count_words}} mots.
            <br/>Vous pouvez le copier pour votre propre usage en cliquant sur <strong>Dupliquer</strong>. 
          </div>
          <hr/>
        {{/if}}
        {{mb_field object=$compte_rendu field="_source" id="htmlarea" name="_source"}}
      {{/if}}
    </td>
    {{if $pdf_thumbnails && $compte_rendu->_id && $app->user_prefs.pdf_and_thumbs}}
      <td id="thumbs_button" class="narrow">
        <div id="mess" class="oldThumbs opacity-60" style="display: none;">
        </div>
        <div id="thumbs" style="overflow: auto; overflow-x: hidden; width: 160px; text-align: center; white-space: normal;">
        </div>
      </td>
    {{/if}}
  </tr>
</table>    
</form>     
