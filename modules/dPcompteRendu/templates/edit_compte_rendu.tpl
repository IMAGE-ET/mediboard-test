{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}
{{assign var=header_footer_fly value=$conf.dPcompteRendu.CCompteRendu.header_footer_fly}}

{{mb_script module=compteRendu script=thumb}}
{{mb_script module=compteRendu script=compte_rendu}}
{{if $pdf_thumbnails && $pdf_and_thumbs}}
  {{mb_script module=compteRendu script=layout}}
{{/if}}

<script>
  window.same_print = {{$conf.dPcompteRendu.CCompteRendu.same_print}};
  window.pdf_thumbnails = {{$pdf_thumbnails|@json}} == 1;
  window.nb_printers = {{$nb_printers|@json}};
  window.modal_mode_play = null;
  window.documentGraphs = {{$templateManager->graphs|@json}};
  window.saving_doc = false;
  document.title = "{{$compte_rendu->_ref_object}} - {{$compte_rendu->nom}}";

  function openWindowMail() {
    {{if $exchange_source->_id}}
      var form = getForm("editFrm");
      var url = new Url("compteRendu", "ajax_view_mail");
      url.addParam("object_guid", "CCompteRendu-"+$V(form.compte_rendu_id));
      url.requestModal(700, 320);
    {{else}}
      alert("Veuillez paramétrer votre compte mail (source smtp dans les préférences utilisateur).");
    {{/if}}
  }

  function openWindowApicrypt() {
    {{assign var=user value='CMediusers::get'|static_call:null}}
    {{if ($user->isPraticien() && $exchange_source->_id) || !$user->isPraticien()}}
      var form = getForm("editFrm");
      var url = new Url("apicrypt", "ajax_view_apicrypt_mail");
      url.addParam("object_id", '{{$compte_rendu->object_id}}');
      url.addParam("object_class", '{{$compte_rendu->object_class}}');
      url.addParam("doc_id", $V(form.compte_rendu_id));
      url.requestModal(700, 320);
    {{else}}
      alert("Veuillez paramétrer votre compte mail (source smtp dans les préférences utilisateur).");
    {{/if}}
  }

  function refreshListDocs() {
    {{if $compte_rendu->_id && !$compte_rendu->valide}}
      var form = getForm("editFrm");
      if (window.opener.Document && window.opener.Document.refreshList) {
        window.opener.Document.refreshList($V(form.file_category_id), $V(form.object_class), $V(form.object_id));
      }
      if (window.opener.reloadListFileEditPatient) {
        window.opener.reloadListFileEditPatient("load");
      }
    {{/if}}
  }

  Main.add(function() {
    Thumb.instance = CKEDITOR.instances.htmlarea;

    {{if $compte_rendu->valide}}
      Thumb.doc_lock = true;
    {{/if}}

    if (window.pdf_thumbnails && window.Preferences.pdf_and_thumbs == 1) {
      PageFormat.init(getForm("editFrm"));
      Thumb.compte_rendu_id = '{{$compte_rendu->_id}}';
      Thumb.modele_id = '{{$modele_id}}';
      Thumb.user_id = '{{$user_id}}';
      Thumb.mode = "doc";
      Thumb.object_class = '{{$compte_rendu->object_class}}';
      Thumb.object_id = '{{$compte_rendu->object_id}}';
    }

    // Les correspondants doivent être présent pour le store du compte-rendu
    // Chargement en arrière-plan de la modale
    {{if $isCourrier && !$compte_rendu->valide}}
      openCorrespondants('{{$compte_rendu->_id}}', '{{$compte_rendu->_ref_object->_guid}}', 0);
    {{/if}}

    {{if $compte_rendu->_id}}
      window.onbeforeunload = function(e) {
        e = e || window.event;

        if (Thumb.contentChanged == false) return;

        if (window.pdf_thumbnails && window.Preferences.pdf_and_thumbs == 1 && Thumb.contentChanged == true) {
          emptyPDF();
        }

        if (e) {
          e.returnValue = ' ';
        }

        return ' ';
      };
    {{/if}}

    var htmlarea = $('htmlarea');

    // documentGraphs est un tableau si vide ($H donnera les mauvaises clés), un objet sinon
    if (documentGraphs.length !== 0) {
      $H(documentGraphs).each(function(pair){
        var g = pair.value;
        $('graph-container').update();
        g.options.fontSize = 14;
        g.options.resolution = 2;
        g.options.legend = {
          labelBoxWidth: 28,
          labelBoxHeight: 20
        };
        g.options.pie.explode = 0;
        var f = new Flotr.Graph($('graph-container'), g.data, g.options);
        g.dataURL = f.canvas.toDataURL();
        oFCKeditor.value = htmlarea.value = htmlarea.value.replace('<'+'span class="field">'+g.name+'</'+'span>', '<'+'img src="'+g.dataURL+'" width="450" height="300" /'+'>');
      });
    }

    {{if !$compte_rendu->_id && $switch_mode == 1}}
      if (window.opener.saveFields) {
        from = window.opener.saveFields;
        var to = getForm("editFrm");
        if (from[0].any(function(elt){ return elt.size > 1; })) {
          toggleOptions();
        }
        from.each(function(elt) {
          elt.each(function(select) {
            if (select) {
              $V(to[select.name], $V(select));
            }
          })
        });
      }
    {{/if}}

    refreshListDocs();

    ObjectTooltip.modes.locker = {
      module: "compteRendu",
      action: "ajax_show_locker",
      sClass: "tooltip"
    };

    var form = getForm("LockDocOther");
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("input_field", form._user_view.name);
    url.autoComplete(form._user_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      width: '200px',
      afterUpdateElement: function(field, selected) {
        $V(form._user_view, selected.down('.view').innerHTML);
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.user_id, id);
      }
    });
  });
</script>

<div style="position: absolute; top: -1500px;">
  <div style="position: relative; width: 900px; height: 600px;" id="graph-container"></div>
</div>

<!-- Modale pour le mode play -->
<div style="display: none;" id="play_modal">
  <table class="form">
  <tr>
    <th class="title" style="cursor: move">
      {{tr}}CCompteRendu-mode_play{{/tr}}
    </th>
  </tr>
    <tr>
      <td class="field_aera" style="padding-top: 10px;">
      </td>
    </tr>
    <tr>
      <td style="text-align: center;">
        <button class="tick">{{tr}}CCompteRendu-apply_field{{/tr}}</button>
        <button class="trash">{{tr}}CCompteRendu-empty_field{{/tr}}</button>
        <button class="cancel">{{tr}}CCompteRendu-close{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>

{{mb_include module=compteRendu template=inc_form_utils}}

<!-- Zone cachée pour la génération PDF et l'impression server side -->
<div id="pdf_area" style="display: none;"></div>

<!-- Zone de confirmation de verrouillage du document -->
{{mb_include module=compteRendu template=inc_area_lock}}

{{if $smarty.session.browser.name == "msie"}}
  <iframe name="download_pdf" style="width: 0; height: 0; position: absolute; top: -1000px;"></iframe>
{{/if}}

<form name="editFrm" action="?m={{$m}}" method="post"
      onsubmit="Url.ping(function() {
        {{if !$compte_rendu->_id}}
          var form = getForm('editFrm');
          var dests = $('destinataires');
          if (dests && dests.select('input:checked').length) {
            $V(form.do_merge, 1);
          }
          if (window.pdf_thumbnails && Prototype.Browser.IE) {
            restoreStyle();
          }
          form.submit();
        {{else}}
          submitCompteRendu();
        {{/if}}
        });
        return false;"
      class="{{$compte_rendu->_spec}}">
  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  <input type="hidden" name="function_id" value="" />
  <input type="hidden" name="user_id" value="" />
  <input type="hidden" name="group_id" value="" />
  <input type="hidden" name="switch_mode" value='{{$switch_mode}}'/>
  <input type="hidden" name="date_print" value="{{$compte_rendu->date_print}}" />
  <input type="hidden" name="do_merge" value="0" />
  <input type="hidden" name="purge_field" value="{{$compte_rendu->purge_field}}" />
  <input type="hidden" name="callback" value="refreshZones" />
  
  {{mb_key object=$compte_rendu}}
  {{mb_field object=$compte_rendu field="object_id" hidden=1}}
  {{mb_field object=$compte_rendu field="object_class" hidden=1}}
  {{mb_field object=$compte_rendu field="modele_id" hidden=1}}
  {{mb_field object=$compte_rendu field="font" hidden=1}}
  {{mb_field object=$compte_rendu field="size" hidden=1}}
  {{mb_field object=$compte_rendu field="valide" hidden=1}}
  {{mb_field object=$compte_rendu field="locker_id" hidden=1}}
  {{mb_field object=$compte_rendu field="factory" hidden=1}}
  {{mb_field object=$compte_rendu field="author_id" hidden=1}}

  {{if $header_footer_fly}}
    <div id="header_footer_fly" style="display: none">
      <table class="tbl">
        <tr>
          <th>
            {{mb_label object=$compte_rendu field=header_id}} :
          </th>
          {{if $headers|@count && ($headers.prat|@count > 0 || $headers.func|@count > 0 || $headers.etab|@count > 0)}}
            <td>
              <select name="header_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.header_id}}" style="width: 15em;">
                <option value="" {{if !$compte_rendu->header_id}}selected{{/if}}>&mdash; {{tr}}CCompteRendu-set-header{{/tr}}</option>
                {{foreach from=$headers item=headersByOwner key=owner}}
                  {{if $headersByOwner|@count}}
                    <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                      {{foreach from=$headersByOwner item=_header}}
                        <option value="{{$_header->_id}}" {{if $compte_rendu->header_id == $_header->_id}}selected{{/if}}>{{$_header->nom}}</option>
                        {{foreachelse}}
                        <option value="" disabled>{{tr}}None{{/tr}}</option>
                      {{/foreach}}
                    </optgroup>
                  {{/if}}
                {{/foreach}}
                {{* Entête associé à un modèle provenant d'un autre type d'objet *}}
                {{assign var=header_id value=$compte_rendu->header_id}}
                {{if $compte_rendu->header_id && !isset($headers.prat.$header_id|smarty:nodefaults) && !isset($headers.func.$header_id|smarty:nodefaults) &&
                     !isset($headers.etab.$header_id|smarty:nodefaults)}}
                  <option value="{{$compte_rendu->header_id}}" selected>{{$compte_rendu->_ref_header->nom}}</option>
                {{/if}}
              </select>
            </td>
          {{else}}
            <td class="empty">
              {{mb_field object=$compte_rendu field=header_id hidden=1}}
              Pas d'entête
            </td>
          {{/if}}
        </tr>
        <tr>
          <th>
            {{mb_label object=$compte_rendu field=footer_id}} :
          </th>
          {{if $footers|@count && ($footers.prat|@count > 0 || $footers.func|@count > 0 || $footers.etab|@count > 0)}}
            <td>
              <select name="footer_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.footer_id}}" style="width: 15em;">
                <option value="" {{if !$compte_rendu->footer_id}}selected{{/if}}>&mdash; {{tr}}CCompteRendu-set-footer{{/tr}}</option>
                {{foreach from=$footers item=footersByOwner key=owner}}
                  {{if $footersByOwner|@count}}
                    <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                      {{foreach from=$footersByOwner item=_footer}}
                        <option value="{{$_footer->_id}}" {{if $compte_rendu->footer_id == $_footer->_id}}selected{{/if}}>{{$_footer->nom}}</option>
                        {{foreachelse}}
                        <option value="" disabled>{{tr}}None{{/tr}}</option>
                      {{/foreach}}
                    </optgroup>
                  {{/if}}
                {{/foreach}}
                {{* Pied de page associé à un modèle provenant d'un autre type d'objet *}}
                {{assign var=footer_id value=$compte_rendu->footer_id}}
                {{if $compte_rendu->footer_id && !isset($footers.prat.$footer_id|smarty:nodefaults) && !isset($footers.func.$footer_id|smarty:nodefaults) &&
                     !isset($footers.etab.$footer_id|smarty:nodefaults)}}
                  <option value="{{$compte_rendu->footer_id}}" selected>{{$compte_rendu->_ref_footer->nom}}</option>
                {{/if}}
              </select>
            </td>
          {{else}}
            <td class="empty">
              {{mb_field object=$compte_rendu field=footer_id hidden=1}}
              Pas de pied de page
            </td>
          {{/if}}
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="button" class="tick" onclick="Control.Modal.close()">{{tr}}Validate{{/tr}}</button>
            <button type="button" class="cancel" onclick="modalHeaderFooter(0);">{{tr}}Close{{/tr}}</button>
          </td>
        </tr>
      </table>
    </div>
  {{/if}}

  <table class="form">
    <tr>
    <th class="category" colspan="2">
      {{if $compte_rendu->_id}}
        <a style="float: left;" class="button left {{if !$prevnext.prev}}disabled{{/if}}"
          {{if $prevnext.prev}}
            href="?m=compteRendu&a=edit_compte_rendu&compte_rendu_id={{$prevnext.prev}}&dialog=1"
          {{/if}}>
          Préc.
        </a>
        <a style="float: right;" class="button right {{if !$prevnext.next}}disabled{{/if}}"
          {{if $prevnext.next}}
            href="?m=compteRendu&a=edit_compte_rendu&compte_rendu_id={{$prevnext.next}}&dialog=1"
          {{/if}}>
          Suiv.
        </a>
        {{mb_include module=system template=inc_object_idsante400 object=$compte_rendu}}
        {{mb_include module=system template=inc_object_history object=$compte_rendu}}
      {{/if}}

      {{if $smarty.session.browser.name != "msie"}}
        <iframe name="download_pdf" style="width: 1px; height: 1px;"></iframe>
      {{/if}}

      {{mb_label object=$compte_rendu field=nom}}
      {{if $read_only}}
        {{mb_field object=$compte_rendu field=nom readonly="readonly"}}
      {{else}}
        {{mb_field object=$compte_rendu field=nom}}
      {{/if}}

      &mdash;
      {{mb_label object=$compte_rendu field=file_category_id}}
      <select name="file_category_id" style="width: 8em;" {{if $read_only}}disabled{{/if}}>
        <option value=""{{if !$compte_rendu->file_category_id}}selected{{/if}}>&mdash; Aucune</option>
        {{foreach from=$listCategory item=currCat}}
          <option value="{{$currCat->file_category_id}}"{{if $currCat->file_category_id==$compte_rendu->file_category_id}}selected{{/if}}>{{$currCat->nom}}</option>
        {{/foreach}}
      </select>

      &mdash;
      {{mb_label object=$compte_rendu field=language}}
      {{mb_field object=$compte_rendu field=language readonly= $read_only}}

      {{if !$read_only}}
        &mdash;
        <button type="submit" class="save notext">{{tr}}Save{{/tr}}</button>
      {{/if}}

      <br />

      {{if "cda"|module_active}}
        {{mb_label object=$compte_rendu field=type_doc}}
        {{mb_field object=$compte_rendu field="type_doc" readonly= $read_only emptyLabel="Choose" style="width: 15em;"}}
      {{/if}}
      <label>
        {{tr}}CCompteRendu-private{{/tr}}
        {{mb_field object=$compte_rendu field=private typeEnum="checkbox" readonly=$read_only
        onchange="this.form.onsubmit()"}}
      </label>

      &mdash;
      <label onmouseover="ObjectTooltip.createEx(this, '{{$compte_rendu->_guid}}', 'locker')">
        {{tr}}CCompteRendu-_is_locked{{/tr}}
        {{mb_field object=$compte_rendu field=_is_locked typeEnum="checkbox" readonly=$lock_bloked
        onChange="checkLock(this)"}}
      </label>

      {{if $compte_rendu->_id && $can_duplicate}}
        &mdash;
        <button type="button" class="add" onclick="duplicateDoc(this.form)">{{tr}}Duplicate{{/tr}}</button>
      {{/if}}

      {{if $pdf_thumbnails && $pdf_and_thumbs}}
        &mdash;
        <button type="button" class="pagelayout" title="{{tr}}CCompteRendu-Pagelayout{{/tr}}"
                {{if $read_only}}readonly="1" disabled="1"{{/if}}
                onclick="save_page_layout();
                  Modal.open($('page_layout'), {
                    closeOnClick: $('page_layout').down('button.tick')
                  });">
          Mise en page
        </button>
        <div id="page_layout" style="display: none;">
          {{mb_include module=compteRendu template=inc_page_layout droit=1}}
          <button class="tick" type="button">{{tr}}Validate{{/tr}}</button>
          <button class="cancel" type="button" onclick="cancel_page_layout();">{{tr}}Cancel{{/tr}}</button>
        </div>
      {{/if}}

      {{if $header_footer_fly}}
        &mdash;
        <button type="button" class="header_footer" onclick="modalHeaderFooter(1)"
                title="Entête / pied de page à la volée"
                {{if $read_only}}readonly="1" disabled="1"{{/if}}>
          Modifier en-tête et pied de page
        </button>
      {{/if}}

      {{if $compte_rendu->_id && $compte_rendu->_ref_modele->object_id}}
        {{assign var=modele value=$compte_rendu->_ref_modele}}
        Version précédente :
        <a onmouseover="ObjectTooltip.createEx(this, '{{$modele->_guid}}')" href="#1" onclick="Document.edit('{{$modele->_id}}')">
          {{$compte_rendu}}
        </a>
      {{/if}}
    </th>
  </tr>

  {{if !$compte_rendu->_id || !$read_only}}
    <tr>
      <td colspan="2">
        <div id="reloadzones">
          {{mb_include module=compteRendu template=inc_zones_fields}}
        </div>
      </td>
    </tr>
  {{/if}}

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
      {{mb_include module=files template=inc_file_send_button 
                   _doc_item=$compte_rendu
                   onComplete="refreshSendButton()"
                   notext=""}}
    </td>
  </tr>
  {{/if}}
  <tr>
    <td class = "greedyPane" style="width: 1200px;"
      {{if $pdf_thumbnails && $pdf_and_thumbs}}
        colspan="1"
      {{else}}
        colspan="2"
      {{/if}}>
      <textarea id="htmlarea" name="_source">
        {{$templateManager->document}}
      </textarea>
    </td>
    {{if $pdf_thumbnails && $pdf_and_thumbs}}
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