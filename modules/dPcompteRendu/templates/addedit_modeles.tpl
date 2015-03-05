{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}

{{mb_script module=compteRendu script=modele}}
{{mb_script module=compteRendu script=thumb}}

<script>
  window.same_print = {{$conf.dPcompteRendu.CCompteRendu.same_print}};
  window.pdf_thumbnails = {{$pdf_thumbnails|@json}} == 1;

  // Taleau des categories en fonction de la classe du compte rendu
  var listObjectClass = {{$listObjectClass|@json}};
  var aTraducClass = {{$listObjectAffichage|@json}};

  loadObjectClass = function(value) {
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

  loadCategory = function(value) {
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

  submitCompteRendu = function(callback) {
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

  reloadHeadersFooters = function() {
    {{if $compte_rendu->_id}}
      if ($("headers") && $("footers") && $("prefaces") && $("endings")) {
        var oForm = getForm("editFrm");
        var compte_rendu_id = $V(oForm.compte_rendu_id);
        var object_class = $V(oForm.object_class);

        var url = new Url("compteRendu", "ajax_headers_footers");
        url.addParam("compte_rendu_id", compte_rendu_id);
        url.addParam("object_class", object_class);
        url.addParam("type", "header");
        url.requestUpdate(oForm.header_id);

        url.addParam("type", "preface");
        url.requestUpdate(oForm.preface_id);

        url.addParam("type", "ending");
        url.requestUpdate(oForm.ending_id);

        url.addParam("type", "footer");
        url.requestUpdate(oForm.footer_id);
      }
    {{/if}}
  }

  setTemplateName = function(object_class, name, type) {
    var form = getForm("editFrm");
    $V(form.object_class, object_class);
    $V(form.nom, name);
    $V(form.type, type);
    Control.Modal.close();
  }
</script>

<script>
  Main.add(function () {
    loadObjectClass('{{$compte_rendu->object_class}}');
    loadCategory('{{$compte_rendu->file_category_id}}');

    {{if $compte_rendu->_id}}
      Thumb.instance = CKEDITOR.instances.htmlarea;
      {{if $droit && $pdf_thumbnails && $pdf_and_thumbs}}
        Thumb.modele_id = '{{$compte_rendu->_id}}';
        Thumb.user_id = '{{$user_id}}';
        Thumb.mode = "modele";
        PageFormat.init(getForm("editFrm"));
      {{/if}}
    {{/if}}
  });

  Main.add(Control.Tabs.create.curry('tabs-edit'));

</script>

<div id="choose_template_name" style="display: none; width: 600px;">
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

{{if $pdf_thumbnails && $pdf_and_thumbs}}
  <form style="display: none;" name="download-pdf-form" target="download_pdf" method="post"
    action="?m=compteRendu&a=ajax_pdf"
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

<iframe name="download_pdf"
  {{if $smarty.session.browser.name == "msie"}}
    style="width: 0; height: 0; position: absolute; top: -1000px;"
  {{else}}
    style="width: 1px; height: 1px;"
  {{/if}}>
</iframe>

<form name="editFrm" action="?m={{$m}}" method="post" 
 onsubmit="Url.ping(submitCompteRendu); return false;"
 class="{{$compte_rendu->_spec}}">

  <input type="hidden" name="m" value="compteRendu" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_modele_aed" />
  {{mb_key object=$compte_rendu}}
  {{mb_field object=$compte_rendu field="object_id" hidden=1}}

  {{if !$droit}}
    <input type="hidden" name="group_id" />
    <input type="hidden" name="function_id" />
    <input type="hidden" name="user_id" value="{{$mediuser->_id}}" />
  {{/if}}
  {{if $compte_rendu->type != "body"}}
    <input type="hidden" name="fast_edit" value="{{$compte_rendu->fast_edit}}" />

    {{if !$pdf_thumbnails || !$pdf_and_thumbs}}
      <input type="hidden" name="fast_edit_pdf" value="{{$compte_rendu->fast_edit_pdf}}" />
    {{/if}}

  {{/if}}

  <table class="main">
    <tr>
      <td style="width: 300px;">
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
          <li><a href="#layout" id="a_addedit_modeles_mise_en_page">Mise en page</a></li>
        </ul>

        {{mb_include module=compteRendu template=inc_modele_info}}
        {{mb_include module=compteRendu template=inc_modele_layout}}

        <hr />

        <table class="form" style="width: 265px;">
          <tr>
            {{if $droit}}
              <td class="button" colspan="2">
              {{if $compte_rendu->_id}}
              <button id="button_addedit_modeles_save_mise_en_page" class="modify" type="submit">{{tr}}Save{{/tr}}</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le modèle',objName:'{{$compte_rendu->nom|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
              </button>
              {{else}}
              <button id="button_addedit_modeles_create" class="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
              </td>
            {{/if}}
          </tr>

          {{if $compte_rendu->_id}}
            <tr>
              <th class="category" colspan="2">{{tr}}CCompteRendu-other-actions{{/tr}}</th>
            </tr>

            <tr>
              <td class="button" colspan="2">
                 <button type="button" class="duplicate"
                         onclick="Modele.copy(this.form, '{{$user_id}}', '{{$droit}}')">{{tr}}Duplicate{{/tr}}</button>
                 <button id="button_addedit_modeles_preview" type="button" class="search"
                         onclick="Modele.preview('{{$compte_rendu->_id}}')">{{tr}}Preview{{/tr}}</button>
                 <br />
                 <button type="button" class="search"
                         onclick="Modele.showUtilisation('{{$compte_rendu->_id}}')">Utilisation ({{$compte_rendu->_count_utilisation}})</button>
              </td>
            </tr>
          {{/if}}
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
      {{if $pdf_thumbnails && $compte_rendu->_id && $pdf_and_thumbs}}
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
