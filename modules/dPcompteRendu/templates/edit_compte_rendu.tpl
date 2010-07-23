{{assign var=pdf_thumbnails value=$dPconfig.dPcompteRendu.CCompteRendu.pdf_thumbnails}}

<script type="text/javascript">
window.same_print = {{$dPconfig.dPcompteRendu.CCompteRendu.same_print}};
window.pdf_thumbnails = {{$pdf_thumbnails|@json}};

function submitCompteRendu(){
  (function(){
    var form = getForm("editFrm");
    if(checkForm(form) && User.id) {
      form.submit();
    }
  }).defer();
}
</script>

{{mb_include_script module=dPcompteRendu script=thumb}}

<script type="text/javascript">
{{if $pdf_thumbnails == 1}}
  emptyPDFonChanged();
  
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
      /*for(var i = 0; i < Thumb.nb_thumbs; i++) {
        var thumbI = $("thumb_" + i);
        //thumbI.onclick = null;
        thumbI.stopObserving("click");
        thumbI.observe("click", Thumb.oldOnclick[i]);
      }*/
		}
		Control.Modal.close();
	}
{{/if}}	
	Main.add(function(){
	  resizeEditor();
	  {{if $pdf_thumbnails}}
	    PageFormat.init(getForm("editFrm")); 
      Thumb.compte_rendu_id = '{{$compte_rendu->_id}}';
      Thumb.modele_id = '{{$modele_id}}';
      Thumb.user_id = '{{$user_id}}';
      Thumb.mode = "doc";
      Thumb.object_class = '{{$compte_rendu->object_class}}';
      Thumb.object_id = '{{$compte_rendu->object_id}}';
    {{/if}}
    {{if $compte_rendu->_id && !$pdf_thumbnails}}
      try {
        setTimeout("window.opener.Document.refreshList(Thumb.object_class,Thumb.object_id)",100);
       }
      catch (e) {}
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

  {{mb_key object=$compte_rendu}}
  {{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
  {{mb_field object=$compte_rendu field="object_class" hidden=1 prop=""}}
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
    {{if $destinataires|@count}}
      <td class="destinataireCR text" id="destinataire" colspan="2">
        {{foreach from=$destinataires key=curr_class_name item=curr_class}}
          &bull; <strong>{{tr}}{{$curr_class_name}}{{/tr}}</strong> :
          {{foreach from=$curr_class key=curr_index item=curr_dest}}
            <input type="checkbox" name="_dest_{{$curr_class_name}}_{{$curr_index}}" />
              <label for="_dest_{{$curr_class_name}}_{{$curr_index}}">
                {{$curr_dest->nom}} ({{tr}}CDestinataire.tag.{{$curr_dest->tag}}{{/tr}});
              </label>
          {{/foreach}}
          <br />
        {{/foreach}}
      </td>
    {{else}}
      <td colspan="2"></td>
		{{/if}}
  </tr>
  {{if $lists|@count}}
    <tr>
      <td id="liste" colspan="2">
        <!-- The div is required because of a Webkit float issue -->
        <div class="listeChoixCR">
          {{foreach from=$lists item=curr_list}}
            <select name="_{{$curr_list->_class_name}}[{{$curr_list->_id}}][]">
              <option value="undef">&mdash; {{$curr_list->nom}}</option>
              {{foreach from=$curr_list->_valeurs item=curr_valeur}}
                <option value="{{$curr_valeur}}" title="{{$curr_valeur}}">{{$curr_valeur|truncate}}</option>
              {{/foreach}}
            </select>
          {{/foreach}}
        </div>
      </td>
    </tr>
    <tr>
      <td class="button text" colspan="2">
        <div id="multiple-info" class="small-info" style="display: none;">
				{{tr}}CCompteRendu-use-multiple-choices{{/tr}}
        </div>
        <script type="text/javascript">
          function toggleOptions() {
            $$("#liste select").each(function(select) {
              select.size = select.size != 4 ? 4 : 1;
              select.multiple = !select.multiple;
              select.options[0].selected = false;
            } );
            $("multiple-info").toggle();
          }
        </script>
        <button class="hslip" type="button" onclick="toggleOptions();">{{tr}}Multiple options{{/tr}}</button>
        <button class="tick" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  {{/if}}

  <tr>
    <td class = "greedyPane" style="height: 600px" {{if $pdf_thumbnails==0}} colspan="2" {{else}} colspan="1" {{/if}} id="editeur">
      <textarea id="htmlarea" name="_source">
        {{$templateManager->document}}
      </textarea>
    </td>
    {{if $pdf_thumbnails == 1}}
      <td id="thumbs_button" style="width: 0.1%;">
        <div id="thumbs" style="overflow: auto; overflow-x: hidden; width: 160px; text-align: center; white-space: normal;">
        </div>
      </td>
    {{/if}}
  </tr>  
</table>

{{if $compte_rendu->_id && $dPconfig.dPfiles.system_sender}}
  <table class="form">
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
          <em>({{tr}}{{$dPconfig.dPfiles.system_sender}}{{/tr}})</em>
        </label>
      </th>
      <td id="sendbutton">
        {{mb_include module=dPfiles template=inc_file_send_button 
                     _doc_item=$compte_rendu
                     onComplete="refreshSendButton()"
                     notext=""}}
      </td>
    </tr>
  </table>
{{/if}} 

</form>