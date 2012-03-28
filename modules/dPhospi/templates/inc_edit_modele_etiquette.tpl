<script type="text/javascript">
window.text_focused = null;

after_edit_modele_etiq = function(id) {
  editEtiq(id);
  refreshList('');
}

insertField = function(elem) {
  var texte_etiq = window.text_focused;
  if (!texte_etiq) {
    texte_etiq = $("edit_etiq_texte");
  }
  var caret = texte_etiq.caret();
  var oForm = getForm("edit_etiq");
  var bold = oForm.elements["_write_bold"][0].checked;
  var content = elem.value;
  if (bold) {
    content = "*" + content + "*";
  }
  else {
    content = "[" + content + "]";
  }
  texte_etiq.caret(caret.begin, caret.end, content + " ");
  texte_etiq.caret(texte_etiq.value.length);
  texte_etiq.fire('ui:change');
  $V(getForm('edit_etiq').fields, '');
}

previewEtiq = function() {
  var form_edit = getForm("edit_etiq");
  var form_download = getForm("download_prev");
  $V(form_download.largeur_page, $V(form_edit.largeur_page));
  $V(form_download.hauteur_page, $V(form_edit.hauteur_page));
  $V(form_download.nb_lignes, $V(form_edit.nb_lignes));
  $V(form_download.nb_colonnes, $V(form_edit.nb_colonnes));
  $V(form_download.marge_horiz, $V(form_edit.marge_horiz));
  $V(form_download.marge_vert, $V(form_edit.marge_vert));
  $V(form_download.hauteur_ligne, $V(form_edit.hauteur_ligne));
  $V(form_download.nom, $V(form_edit.nom));
  $V(form_download.texte, $V(form_edit.texte));
  $V(form_download.texte_2, $V(form_edit.texte_2));
  $V(form_download.texte_3, $V(form_edit.texte_3));
  $V(form_download.texte_4, $V(form_edit.texte_4));
  $V(form_download.font, $V(form_edit.font));
  $V(form_download.show_border, $V(form_edit.show_border));
  $V(form_download.text_align, $V(form_edit.text_align));
  form_download.submit();
}

Main.add(function() {
  var oForm = getForm("edit_etiq");
  oForm.texte.observe("focus", function(e) { window.text_focused = e.target; });
  oForm.texte_2.observe("focus", function(e) { window.text_focused = e.target });
  oForm.texte_3.observe("focus", function(e) { window.text_focused = e.target });
  oForm.texte_4.observe("focus", function(e) { window.text_focused = e.target });
});
</script>

<form name="edit_etiq" onsubmit="return onSubmitFormAjax(this);" method="post">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_modele_etiquette_aed" />
  <input type="hidden" name="callback" value="after_edit_modele_etiq" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$modele_etiquette}}
  <input type="hidden" name="group_id" value="{{$modele_etiquette->group_id}}" />
  
  <table class="form">
  {{if $modele_etiquette->_id}}
    <th class="title modify" colspan="4">{{tr}}CModeleEtiquette-title-modify{{/tr}}</th>
  {{else}}
    <th class="title create" colspan="4">{{tr}}CModeleEtiquette-title-create{{/tr}}</th>
  {{/if}}
  <!-- Formattage de la page et des étiquettes-->
    <tr>
      <th class="category" colspan="4">
        {{tr}}CModeleEtiquette.format{{/tr}}
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=largeur_page}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=largeur_page}} cm
      </td>
      <th>
        {{mb_label object=$modele_etiquette field=hauteur_page}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=hauteur_page}} cm
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=nb_lignes}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=nb_lignes}}
      </td>
      <th>
        {{mb_label object=$modele_etiquette field=nb_colonnes}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=nb_colonnes}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=marge_horiz}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=marge_horiz}} cm
      </td>
      <th>
        {{mb_label object=$modele_etiquette field=marge_vert}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=marge_vert}} cm
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=hauteur_ligne}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=hauteur_ligne}}
      </td>
      <th>
        <b>{{mb_label object=$modele_etiquette field=font}}</b>
      </th>
      <td>
        <select name="font">
          <option value="">&mdash; {{tr}}CModeleEtiquette.choose_font{{/tr}} </option>
          {{foreach from=$listfonts key=_font item=_font_name}}
            <option value='{{$_font}}' {{if $_font == $modele_etiquette->font}}selected="selected"{{/if}}>{{$_font_name}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=show_border}}
      </th>
      <td>
        {{mb_field object=$modele_etiquette field=show_border}}
      </td>
      <td colspan="2">
        {{tr}}CModeleEtiquette._width_etiq{{/tr}} : {{mb_value object=$modele_etiquette field=_width_etiq}} &mdash;
        {{tr}}CModeleEtiquette._height_etiq{{/tr}} : {{mb_value object=$modele_etiquette field=_height_etiq}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=text_align}}
      </th>
      <td colspan="3">
        {{mb_field object=$modele_etiquette field=text_align typeEnum=radio}}
      </td>
    </tr>
    <tr>
      <th class="category" colspan="4">
        {{tr}}CModeleEtiquette.other_fields{{/tr}}
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=nom}}
      </th>
      <td colspan="3">
        {{mb_field object=$modele_etiquette field=nom}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$modele_etiquette field=object_class}}
      </th>
      <td colspan="3">
        <select name="object_class" class="{{$modele_etiquette->_props.object_class}}">
          <option value="">&mdash; {{tr}}CModeleEtiquette-object_class-select{{/tr}} </option>
          {{foreach from=$classes|smarty:nodefaults key=_class item=_class_tr}}
            <option value="{{$_class}}" {{if $_class == $modele_etiquette->object_class}} selected="selected" {{/if}}>
              {{tr}}{{$_class}}{{/tr}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <!-- Contenu principal de l'étiquette -->
      <th>
        {{mb_label object=$modele_etiquette field=texte}}
      </th>
      <td colspan="2">
        {{mb_field object=$modele_etiquette field=texte}}
      </td>
      <!--  Liste des champs disponibles -->
      <td rowspan="4">
        <b>{{tr}}CModeleEtiquette.fields{{/tr}} :</b>
        <br/>
        {{tr}}CModeleEtiquette._write_bold{{/tr}} :
        {{mb_field object=$modele_etiquette field="_write_bold" typeEnum="radio"}}
        <br/>
        {{foreach from=$fields key=_class item=_by_class}}
          {{tr}}{{$_class}}{{/tr}} :<br />
          {{foreach from=$_by_class item=_field}}
            <button style="display: block;" type="button" value='{{$_field}}' onclick='insertField(this);'>{{$_field}}</button>
          {{/foreach}}
        {{/foreach}}
        <br/>
      </td>
    </tr>
    <tr>
      <!-- Autres contenus possibles -->
      <th>
        {{mb_label object=$modele_etiquette field=texte_2}}
      </th>
      <td colspan="2">
        {{mb_field object=$modele_etiquette field=texte_2}}
      </td>
   </tr>
   <tr>
      <th>
        {{mb_label object=$modele_etiquette field=texte_3}}
      </th>
      <td colspan="2">
        {{mb_field object=$modele_etiquette field=texte_3}}
      </td>
  </tr>
  <tr>
      <th>
        {{mb_label object=$modele_etiquette field=texte_4}}
      </th>
      <td colspan="2">
        {{mb_field object=$modele_etiquette field=texte_4}}
      </td>
  </tr>
    <tr>
      <td colspan="4" style="text-align: center">
       <button class="search" type="button" onclick = "if (checkForm(this.form)) previewEtiq();">
          {{tr}}Preview{{/tr}}
        </button>
        <button class="modify">
          {{tr}}Save{{/tr}}
        </button>
        <button class="cancel" 
                onclick = "confirmDeletion(this.form,
                    {typeName:'le modèle d\'étiquette',
                     objName:'{{$modele_etiquette->nom|smarty:nodefaults|JSAttribute}}',
                     ajax: true})"
                type="button">
        {{tr}}Delete{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>

<!-- Formulaire de téléchargement du PDF d'aperçu des étiquettes -->
<form name="download_prev" method="post" target="_blank" action="?m=dPhospi&a=print_modele_etiquette">
  <input type="hidden" name="suppressHeaders" value="1" />
  <input type="hidden" name="largeur_page" value="" />
  <input type="hidden" name="hauteur_page" value="" />
  <input type="hidden" name="nb_lignes" value="" />
  <input type="hidden" name="nb_colonnes" value="" />
  <input type="hidden" name="marge_horiz" value="" />
  <input type="hidden" name="marge_vert" value="" />
  <input type="hidden" name="hauteur_ligne" value="" />
  <input type="hidden" name="nom" value="" />
  <input type="hidden" name="texte" value="" />
  <input type="hidden" name="texte_2" value="" />
  <input type="hidden" name="texte_3" value="" />
  <input type="hidden" name="texte_4" value="" />
  <input type="hidden" name="font" value="" />
  <input type="hidden" name="show_border" value="" />
  <input type="hidden" name="text_align" value="" />
</form>