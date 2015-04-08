{{*
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

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

  {{if $access_group}}
    <tr>
      <th>{{mb_label object=$compte_rendu field="group_id"}}</th>
      <td>
        {{if $droit}}
          {{mb_field object=$compte_rendu field=group_id hidden=1
          onchange="
             \$V(this.form.user_id, '', false);
             \$V(this.form.user_id_view, '', false);
             \$V(this.form.function_id, '', false);
             \$V(this.form.function_id_view, '', false);"}}
          <input type="text" name="group_id_view" value="{{$compte_rendu->_ref_group}}" />
        {{elseif $compte_rendu->group_id}}
          {{mb_field object=$compte_rendu field=group_id hidden=1}}
          {{$compte_rendu->_ref_group}}
        {{/if}}
      </td>
    </tr>
  {{/if}}

  {{if $access_function}}
    <tr>
      <th>{{mb_label object=$compte_rendu field="function_id"}}</th>
      <td>
        {{if $droit}}
          {{mb_field object=$compte_rendu field=function_id hidden=1
          onchange="
             \$V(this.form.user_id, '', false);
             \$V(this.form.user_id_view, '', false);
             \$V(this.form.group_id, '', false);
             \$V(this.form.group_id_view, '', false);"}}
          <input type="text" name="function_id_view" value="{{$compte_rendu->_ref_function}}" />
        {{elseif $compte_rendu->function_id}}
          {{$compte_rendu->_ref_function}}
        {{/if}}
      </td>
    </tr>
  {{/if}}

  <tr>
    <th>{{mb_label object=$compte_rendu field="user_id"}}</th>
    <td>
      {{if $droit}}
        {{mb_field object=$compte_rendu field=user_id hidden=1
        onchange="
             \$V(this.form.function_id, '', false);
             \$V(this.form.function_id_view, '', false);
             \$V(this.form.group_id, '', false);
             \$V(this.form.group_id_view, '', false);"}}
        <input type="text" name="user_id_view" value="{{$compte_rendu->_ref_user}}" />
      {{elseif $compte_rendu->user_id}}
        {{$compte_rendu->_ref_user}}
      {{/if}}
    </td>
  </tr>

  {{if $compte_rendu->type == "body" || !$compte_rendu->_id}}
    <tr>
      <th>{{mb_label object=$compte_rendu field="fast_edit"}}</th>
      <td>
        {{mb_field object=$compte_rendu field="fast_edit"}}
      </td>
    </tr>

    {{if $pdf_thumbnails && $pdf_and_thumbs}}
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
                <option value="{{$_header->_id}}" {{if $compte_rendu->header_id == $_header->_id}}selected{{/if}}>{{$_header->nom}}</option>
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
        <select name="preface_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.preface_id}}" {{if !$droit}}disabled{{/if}} style="width: 15em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$prefaces item=prefacesByOwner key=owner}}
            <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
              {{foreach from=$prefacesByOwner item=_preface}}
                <option value="{{$_preface->_id}}" {{if $compte_rendu->preface_id == $_preface->_id}}selected{{/if}}>{{$_preface->nom}}</option>
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
        <select name="ending_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.ending_id}}" {{if !$droit}}disabled{{/if}} style="width: 15em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$endings item=endingsByOwner key=owner}}
            <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
              {{foreach from=$endingsByOwner item=_ending}}
                <option value="{{$_ending->_id}}" {{if $compte_rendu->ending_id == $_ending->_id}}selected{{/if}}>{{$_ending->nom}}</option>
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
        <select name="footer_id" onchange="Thumb.old();" class="{{$compte_rendu->_props.footer_id}}" {{if !$droit}}disabled{{/if}} style="width: 15em;">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$footers item=footersByOwner key=owner}}
            <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
              {{foreach from=$footersByOwner item=_footer}}
                <option value="{{$_footer->_id}}" {{if $compte_rendu->footer_id == $_footer->_id}}selected{{/if}}>{{$_footer->nom}}</option>
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
    <th>{{mb_label object=$compte_rendu field="language"}}</th>
    <td>
      {{mb_field object=$compte_rendu field="language"}}
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$compte_rendu field="factory"}}</th>
    <td>
      <select name="factory">
        {{foreach from=$compte_rendu->_specs.factory->_list item=_factory}}
          {{if $_factory != "none"}}
            <option value="{{$_factory}}" {{if $compte_rendu->factory == $_factory}}selected{{/if}}>{{$compte_rendu->_specs.factory->_locales.$_factory}}</option>
          {{/if}}
        {{/foreach}}
      </select>
    </td>
  </tr>

  {{if "cda"|module_active}}
    <tr>
      <th>{{mb_label object=$compte_rendu field="type_doc"}}</th>
      <td>
        {{mb_field object=$compte_rendu field="type_doc" emptyLabel="Choose" style="width: 15em;"}}
      </td>
    </tr>
  {{/if}}

  {{if "sisra"|module_active}}
    <tr>
      <th>{{mb_label object=$compte_rendu field="type_doc_sisra"}}</th>
      <td>{{mb_field object=$compte_rendu field="type_doc_sisra" emptyLabel="Choose" style="width: 15em;"}}</td>
    </tr>
  {{/if}}

  <tr>
    <th>{{mb_label object=$compte_rendu field="purge_field"}}</th>
    <td>{{mb_field object=$compte_rendu field="purge_field"}}</td>
  </tr>
</table>