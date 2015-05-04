{{*
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=object_class value=$object->_class}}
{{assign var=object_id value=$object->_id}}
{{unique_id var=unique_id}}

{{if "CCompteRendu::canCreate"|static_call:$object}}
  <form name="unmergePack_{{$object->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="compteRendu" />
    <input type="hidden" name="dosql" value="do_pack_multi_aed" />
    <input type="hidden" name="pack_id" value="" />
    <input type="hidden" name="object_class" value="{{$object_class}}" />
    <input type="hidden" name="object_id" value="{{$object_id}}" />
    <input type="hidden" name="callback" value="Document.afterUnmerge" />
  </form>

  <script>
    Main.add(function() {
      ObjectTooltip.modes.locker = {
        module: "compteRendu",
        action: "ajax_show_locker",
        sClass: "tooltip"
      };

      var form = getForm('DocumentAdd-{{$unique_id}}-{{$object->_guid}}');
      var url;

      url = new Url("compteRendu", "ajax_modele_autocomplete");
      url.addParam("user_id", "{{$praticien->_id}}");
      url.addParam("function_id", "{{$praticien->function_id}}");
      url.addParam("object_class", '{{$object_class}}');
      url.addParam("object_id", '{{$object_id}}');
      url.autoComplete(form.keywords_modele, '', {
        minChars: 2,
        afterUpdateElement: Document.createDocAutocomplete.curry('{{$object_class}}', '{{$object_id}}', '{{$unique_id}}'),
        dropdown: true,
        width: "250px"
      });

      url = new Url("compteRendu", "ajax_pack_autocomplete");
      url.addParam("user_id", "{{$praticien->_id}}");
      url.addParam("function_id", "{{$praticien->function_id}}");
      url.addParam("object_class", '{{$object_class}}');
      url.addParam("object_id", '{{$object_id}}');
      url.autoComplete(form.keywords_pack, '', {
        minChars: 2,
        afterUpdateElement: Document.createPackAutocomplete.curry('{{$object_class}}', '{{$object_id}}', '{{$unique_id}}'),
        dropdown: true,
        width: "250px"
      });
    });
  </script>

  <form name="DocumentAdd-{{$unique_id}}-{{$object->_guid}}" method="post" class="prepared">
    {{if "CCompteRendu::canCreate"|static_call:$object}}
      <input type="text" placeholder="&mdash; Mod�le" name="keywords_modele" class="autocomplete str" autocomplete="off" style="width: 5em;" />
      <input type="text" placeholder="&mdash; Pack" name="keywords_pack" class="autocomplete str" autocomplete="off" style="width: 4em;"/>
    {{/if}}

    <!-- Impression de tous les mod�les disponibles pour l'objet -->
    <button type="button" class="print notext" onclick="Document.printSelDocs('{{$object_id}}', '{{$object_class}}');">
      {{tr}}Print{{/tr}}
    </button>

    <input type="hidden" name="_fast_edit" />
    <input type="hidden" name="_modele_id" />
    <input type="hidden" name="_object_id"
           onchange="var fast_edit = $V(this.form._fast_edit);
             if (fast_edit == '1') {
             Document.fastMode('{{$object_class}}', this.form._modele_id.value, '{{$object_id}}');
             }
             else {
             Document.create(this.form._modele_id.value, this.value,'{{$object_id}}','{{$object_class}}');
             }
             $V(this, '', false);
             $V(this.form._fast_edit, '');
             $V(this.form._modele_id, '');"/>
  </form>
{{/if}}

{{mb_include module=patients template=inc_button_add_doc context_guid=$object->_guid}}

<div id="area_docitems_{{$object->_guid}}_{{$unique_id}}" class="count">
  {{if $object->_nb_docs !== null && $object->_nb_files !== null && $object->_nb_forms !== null}}
    {{mb_include module=patients template=inc_widget_count_documents}}
  {{else}}
    <script>
      DocumentV2.refresh($("area_docitems_{{$object->_guid}}_{{$unique_id}}").up());
    </script>
  {{/if}}
</div>