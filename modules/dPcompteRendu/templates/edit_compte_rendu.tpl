{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

{{mb_include_script module=dPcompteRendu script=thumb}}

<script type="text/javascript">
window.same_print = {{$conf.dPcompteRendu.CCompteRendu.same_print}};
window.pdf_thumbnails = {{$pdf_thumbnails|@json}};

function submitCompteRendu(callback){
  CKEDITOR.instances.htmlarea.document.getBody().setStyle("background", "#ddd");
	  
  (function(){
    restoreStyle();
	  var html = CKEDITOR.instances.htmlarea.getData();
	  deleteStyle();
	  $V($("htmlarea"), html, false);
	  
    var form = getForm("editFrm");
    
    if(checkForm(form) && User.id) {
      form.onsubmit=function(){ return true; };
      submitFormAjax(form, $("systemMsg"), { onComplete: function() {
        Thumb.changed = false;
        window.callback = callback ? callback : null;
      }});
      if (window.pdf_thumbnails == 0 && window.opener.Document.refreshList) {
        window.opener.setTimeout(window.opener.Document.refreshList.curry($V(form.object_class), $V(form.object_id), 1000));
      }
    }
  }).defer();
  
}

function refreshZones(id, obj) {
  if (window.pdf_thumbnails != 0) {
    Thumb.compte_rendu_id = id;
    Thumb.modele_id = null;
    Thumb.refreshThumbs(0, Thumb.compte_rendu_id, null, Thumb.user_id, Thumb.mode);
  }
  // Remise du content sauvegardé, avec l'impression en callback
  CKEDITOR.instances.htmlarea.setData(obj._source, window.callback);
  
	var url = new Url("dPcompteRendu", "edit_compte_rendu");
	url.addParam("compte_rendu_id", id);
	url.addParam("reloadzones", 1);
	url.requestUpdate("reloadzones",
			{onComplete: function(){
         window.resizeEditor();
		     var form = getForm("editFrm");
		     form.onsubmit = function() { Url.ping({onComplete: submitCompteRendu}); return false;};
		     $V(form.compte_rendu_id, id);
  }});
}

function openWindowMail() {
  var url = new Url("dPcompteRendu", "ajax_view_mail");
  url.popup(700, 320, "Envoi mail");
}

{{if $pdf_thumbnails == 1}}

  togglePageLayout = function() {
    $("page_layout").toggle();
  }
  
  completeLayout = function() {
    var tab_margin = ["top", "right", "bottom", "left"];
    var form = getForm("editFrm");
    var dform = getForm('download-pdf-form');
    for(var i=0; i < 4; i++) {
      if ($("input_margin_"+tab_margin[i])) {
        $("input_margin_"+tab_margin[i]).remove();
      }
      dform.insert({bottom: new Element("input",{id: "input_margin_"+tab_margin[i],type: 'hidden', name: 'margins[]', value: $("editFrm_margin_"+tab_margin[i]).value})});
    }
    $V(dform.orientation, $V(form._orientation));
    $V(dform.page_format, form._page_format.value);
  }
  
  save_page_layout = function() {
    page_layout_save = { 
      margin_top:    PageFormat.form.margin_top.value,
      margin_left:   PageFormat.form.margin_left.value,
      margin_right:  PageFormat.form.margin_right.value,
      margin_bottom: PageFormat.form.margin_bottom.value,
      page_format:   PageFormat.form._page_format.value,
      page_width:    PageFormat.form.page_width.value,
      page_height:   PageFormat.form.page_height.value,
      orientation:   $V(PageFormat.form._orientation)
    };
  }
  
  cancel_page_layout = function() {
    $V(PageFormat.form.margin_top,    page_layout_save.margin_top);
    $V(PageFormat.form.margin_left,   page_layout_save.margin_left);
    $V(PageFormat.form.margin_right,  page_layout_save.margin_right);
    $V(PageFormat.form.margin_bottom, page_layout_save.margin_bottom);
    $V(PageFormat.form._page_format,  page_layout_save.page_format);
    $V(PageFormat.form.page_height,   page_layout_save.page_height);
    $V(PageFormat.form.page_width,    page_layout_save.page_width);
    $V(PageFormat.form._orientation,  page_layout_save.orientation);

    if(!Thumb.thumb_up2date && !Thumb.oldContent) {

      Thumb.thumb_up2date = true;
      $('mess').toggle();
      $('thumbs').setOpacity(1);
      Thumb.init();
    }
    Control.Modal.close();
  }
{{/if}} 
  Main.add(function(){
      window.onbeforeunload = function() {
        if (Thumb.changed == false && (Thumb.modele_id == '' || Thumb.modele_id == null)) return;

        {{if $pdf_thumbnails}}
          Thumb.old();
  
          // La requête de vidage de pdf doit être faite dans le scope
          // de la fenêtre principale, car on est en train de fermer la popup
          var f = getForm("download-pdf-form");
          if (CKEDITOR.env.ie)
            var url = new Url();
          else
            var url = new window.opener.Url();
  
          url.addParam("m", "dPcompteRendu");
          url.addParam("dosql", "do_modele_aed");
          url.addParam("_do_empty_pdf", 1);
          url.addParam("compte_rendu_id", f.compte_rendu_id.value);
          
          url.requestJSON(function(){}, { method: "post"});
        {{/if}}
        
        return '';
      };
    {{if $pdf_thumbnails}}
      PageFormat.init(getForm("editFrm")); 
      Thumb.compte_rendu_id = '{{$compte_rendu->_id}}';
      Thumb.modele_id = '{{$modele_id}}';
      Thumb.user_id = '{{$user_id}}';
      Thumb.mode = "doc";
      Thumb.object_class = '{{$compte_rendu->object_class}}';
      Thumb.object_id = '{{$compte_rendu->object_id}}';
    {{/if}}
    {{if !$compte_rendu->_id && $switch_mode == 1}}
      if (window.opener.linkFields) {
        
        from = window.opener.linkFields();
        var to = getForm("editFrm");
        if (from[0].any(function(elt){ return elt.size > 1; }))        
          toggleOptions();
        from.each(function(elt) {
          elt.each(function(select) {
            if (select) {
              $V(to[select.name], $V(select));
            }
          })
        });
      }
    {{/if}}
  });

</script>

<form style="display: none;" name="download-pdf-form" target="_blank" method="post" action="?m=dPcompteRendu&amp;a=ajax_pdf_and_thumbs"
      onsubmit="completeLayout(); this.submit();">
  <input type="hidden" name="content" value=""/>
  <input type="hidden" name="compte_rendu_id" value='{{if $compte_rendu->_id != ''}}{{$compte_rendu->_id}}{{else}}{{$modele_id}}{{/if}}' />
  <input type="hidden" name="object_id" value="{{$compte_rendu->object_id}}"/>
  <input type="hidden" name="suppressHeaders" value="1"/>
  <input type="hidden" name="stream" value="1"/>
  <input type="hidden" name="generate_thumbs" value="0"/>
  <input type="hidden" name="page_format" value=""/>
  <input type="hidden" name="orientation" value=""/>
</form>

<form name="editFrm" action="?m={{$m}}" method="post"
      onsubmit="Url.ping({onComplete: submitCompteRendu}); return false;" 
      class="{{$compte_rendu->_spec}}">
  <input type="hidden" name="m" value="dPcompteRendu" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  <input type="hidden" name="function_id" value="" />
  <input type="hidden" name="chir_id" value="" />
  <input type="hidden" name="group_id" value="" />
  <input type="hidden" name="fast_edit" value="0"/>
  <input type="hidden" name="fast_edit_pdf" value="0"/>
  <input type="hidden" name="switch_mode" value='{{$switch_mode}}'/>
  <input type="hidden" name="callback" value="refreshZones" />
  
  {{mb_key object=$compte_rendu}}
  {{mb_field object=$compte_rendu field="object_id" hidden=1}}
  {{mb_field object=$compte_rendu field="object_class" hidden=1}}
  <table class="form">
    <tr>
    <th class="category" colspan="2">
      {{if $compte_rendu->_id}}
        {{mb_include module=system template=inc_object_idsante400 object=$compte_rendu}}
        {{mb_include module=system template=inc_object_history object=$compte_rendu}}
      {{/if}}
      {{mb_label object=$compte_rendu field=nom}}
      {{mb_field object=$compte_rendu field=nom}}
      
      &mdash;
      {{mb_label object=$compte_rendu field=file_category_id}}
      <select name="file_category_id">
        <option value=""{{if !$compte_rendu->file_category_id}} selected="selected"{{/if}}>&mdash; Aucune Catégorie</option>
        {{foreach from=$listCategory item=currCat}}
          <option value="{{$currCat->file_category_id}}"{{if $currCat->file_category_id==$compte_rendu->file_category_id}} selected="selected"{{/if}}>{{$currCat->nom}}</option>
        {{/foreach}}
      </select>
      
      &mdash;
      <label>
        {{tr}}CCompteRendu-private{{/tr}}
        {{mb_field object=$compte_rendu field=private typeEnum="checkbox"}}
      </label>
      {{if $pdf_thumbnails}}
        &mdash;
        <button class="pagelayout" type="button" title="Mise en page"
                onclick="save_page_layout(); modal($('page_layout'), {
                closeOnClick: $('page_layout').down('button.tick')
                });">
        {{tr}}CCompteRendu-Pagelayout{{/tr}}
        </button>
        {{if $compte_rendu->_id != null}}
          &mdash;
          <button class="hslip" type="button" title="Afficher / Masquer les vignettes"
                  onclick = "Thumb.choixAffiche(1);">Vignettes</button>
        {{/if}}

        <div id="page_layout" style="display: none;">
          {{include file="inc_page_layout.tpl" droit=1}}
          <button class="tick" type="button">{{tr}}Validate{{/tr}}</button>
          <button class="cancel" type="button" onclick="cancel_page_layout();">{{tr}}Cancel{{/tr}}</button>
        </div>
      {{/if}}
    </th>
  </tr>

  <tr>
    <td colspan="2">
      <div id="reloadzones">
        {{mb_include template=inc_zones_fields}}
      </div>
    </td>
  </tr>

  {{if $compte_rendu->_id && $conf.dPfiles.system_sender}}
  <tr>
    <th style="width: 50%">
      <script type="text/javascript">
        refreshSendButton = function() {
          var url = new Url("dPfiles", "ajax_send_button");
          url.addParam("item_guid", "{{$compte_rendu->_guid}}");
          url.addParam("onComplete", "refreshSendButton()");
          url.requestUpdate("sendbutton");
          refreshList();
        }
      </script>
      <label title="{{tr}}config-dPfiles-system_sender{{/tr}}">
        {{tr}}config-dPfiles-system_sender{{/tr}}
        <em>({{tr}}{{$conf.dPfiles.system_sender}}{{/tr}})</em>
      </label>
    </th>
    <td id="sendbutton">
      {{mb_include module=dPfiles template=inc_file_send_button 
                   _doc_item=$compte_rendu
                   onComplete="refreshSendButton()"
                   notext=""}}
    </td>
  </tr>
  {{/if}}
  <tr>
    <td class = "greedyPane" style="height: 600px; width: 1200px;" {{if $pdf_thumbnails==0}} colspan="2" {{else}} colspan="1" {{/if}} id="editeur">
      <textarea id="htmlarea" name="_source">
        {{$templateManager->document}}
      </textarea>
    </td>
    {{if $pdf_thumbnails == 1}}
      <td id="thumbs_button" class="narrow">
        <div id="thumbs" style="overflow: auto; overflow-x: hidden; width: 160px; text-align: center; white-space: normal;">
        </div>
      </td>
    {{/if}}
  </tr>  
</table>

</form>