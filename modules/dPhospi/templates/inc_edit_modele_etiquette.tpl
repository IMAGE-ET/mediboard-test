<script type="text/javascript">
texte_etiq = $('edit_etiq_texte');
after_edit_modele_etiq = function(id) {
	editEtiq(id);
	refreshList('');
}

insertField = function(elem) {
	var caret = texte_etiq.caret();
  texte_etiq.caret(caret.begin, caret.end, elem.value + " ");
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
	$V(form_download.font, $V(form_edit.font));
	form_download.submit();
}
</script>

<form name="edit_etiq" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_modele_etiquette_aed" />
  <input type="hidden" name="callback" value="after_edit_modele_etiq" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$modele_etiquette}}
  
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
	    <th>
	      {{mb_label object=$modele_etiquette field=texte}}
	    </th>
	    <!-- Contenu de l'étiquette -->
	    <td colspan="2">
	      {{mb_field object=$modele_etiquette field=texte}}
	    </td>
	    <!--  Liste des champs disponibles -->
	    <td>
	      <b>{{tr}}CModeleEtiquette.fields{{/tr}} :</b>
	      <br/>
	      {{foreach from=$fields key=_class_name item=_by_class}}
          {{tr}}{{$_class_name}}{{/tr}} :<br />
          {{foreach from=$_by_class item=_field}}
            <button style="display: block;" type="button" value='{{$_field}}' onclick='insertField(this);'>{{$_field}}</button>
          {{/foreach}}
        {{/foreach}}
	      <!-- 
	      <select name="fields" onchange='insertField(this);'>
	        <option value=''>&mdash; {{tr}}CModeleEtiquette.choose_field{{/tr}}</option>
	        {{foreach from=$fields item=_field}}
	          <option value='{{$_field}}''>{{$_field}}</option>
	        {{/foreach}}
	      </select>
	       -->
	      <br/>
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
  <input type="hidden" name="font" value="" />
</form>