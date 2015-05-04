{{*
 * $Id$
 *  
 * @category Dossier Patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=hospi script=modele_etiquette ajax=true}}
{{mb_script module=files script=file ajax=true}}
{{mb_script module=compteRendu script=document ajax=true}}
{{mb_script module=compteRendu script=modele_selector ajax=true}}

{{assign var=self_guid value="$context->_class-$context->_id"}}
{{assign var=self_guid value=$self_guid|md5}}
{{assign var=self_guid value="guid_$self_guid"}}

<script>
  changeContext = function() {
    new Url("patients", "ajax_context_doc")
      .addParam("patient_id", "{{$patient->_id}}")
      .requestModal("60%", "60%");
  };

  // Refresh de l'explorateur après ajout d'un fichier
  reloadAfterUpload = function() {
    Control.Modal.close();
    refreshAfterAdd();
  };

  // Callback de refresh
  refreshAfterAdd = function() {
    if (window.loadAllDocs) {
      loadAllDocs();
    }
    if (window.DocumentV2) {
      var selector = printf("div.documentsV2-%s-%s", "{{$context->_class}}", "{{$context->_id}}");
      $$(selector).each(function(elt) {
        DocumentV2.refresh(elt);
      });
    }
  };

  // Refresh de l'explorateur après ajout d'un formulaire
  ExObject.refreshSelf['{{$self_guid}}'] = refreshAfterAdd;

  Main.add(function() {
    var form = getForm("addDoc-{{$context->_guid}}");

    var urlModele = new Url("compteRendu", "ajax_modele_autocomplete");
    urlModele.addParam("user_id", "{{$curr_user->_id}}");
    urlModele.addParam("function_id", "{{$curr_user->function_id}}");
    urlModele.addParam("object_class", '{{$context->_class}}');
    urlModele.addParam("object_id", '{{$context->_id}}');
    urlModele.autoComplete(form.keywords_modele, '', {
      minChars: 2,
      afterUpdateElement: function(input, selected) {
        Control.Modal.close();
        Document.createDocAutocomplete('{{$context->_class}}', '{{$context->_id}}', '', input, selected);
      },
      dropdown: true,
      width: "250px"
    });

    var urlPack = new Url("compteRendu", "ajax_pack_autocomplete");
    urlPack.addParam("user_id", "{{$curr_user->_id}}");
    urlPack.addParam("function_id", "{{$curr_user->function_id}}");
    urlPack.addParam("object_class", '{{$context->_class}}');
    urlPack.addParam("object_id", '{{$context->_id}}');
    urlPack.autoComplete(form.keywords_pack, '', {
      minChars: 2,
      afterUpdateElement: function(input, selected) {
        Control.Modal.close();
        Document.createPackAutocomplete('{{$context->_class}}', '{{$context->_id}}', '', input, selected);
      },
      dropdown: true,
      width: "250px"
    });

    modeleSelector[{{$context->_id}}] = new ModeleSelector("addDoc-{{$context->_guid}}", null, "_modele_id", "_object_id", "_fast_edit");
  });
</script>

<form name="download_etiq_{{$context->_class}}_{{$context->_id}}_" target="_blank" method="get" class="prepared">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="raw" value="print_etiquettes" />
  <input type="hidden" name="object_id" value="{{$context->_id}}" />
  <input type="hidden" name="object_class" value="{{$context->_class}}" />
  <input type="hidden" name="modele_etiquette_id" />
</form>

<table class="main">
  <tr>
    <th colspan="2">
      <h2>
        <strong>
          Contexte :
            {{if $context->_class == "CPatient"}}
              Patient {{$context}}
            {{elseif $context->_class == "CSejour"}}
              {{$context}}
              &mdash;
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$context->_ref_praticien}}
            {{elseif $context->_class == "CConsultation"}}
              {{tr}}CConsultation{{/tr}} du {{$context->_date|date_format:$conf.date}} à {{$context->heure|date_format:$conf.time}}
              &mdash;
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$context->_ref_chir}}
            {{elseif $context->_class == "COperation"}}
              {{$context}}
              &mdash;
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$context->_ref_chir}}
            {{/if}}
          <button type="button" class="change notext" onclick="changeContext();"></button>
        </strong>
      </h2>
    </th>
  </tr>
  <tr>
    <td style="width: 50%;">
      <fieldset style="width: 95%; height: 100px; display: inline-block">
        <legend style="font-size: 13pt;">Document</legend>

        <form name="addDoc-{{$context->_guid}}" method="post">
          <input type="hidden" name="_fast_edit" value="" />
          <input type="hidden" name="_modele_id" value="" />
          <input type="hidden" name="_object_id" value=""
                 onchange="var fast_edit = $V(this.form._fast_edit);
                   var modele_id = $V(this.form._modele_id);
                   var _object_id = $V(this);
                   Control.Modal.close();
                   if (fast_edit == '1') {
                     Document.fastMode('{{$context->_class}}', modele_id, '{{$context->_id}}');
                   }
                   else {
                     Document.create(modele_id, _object_id, '{{$context->_id}}', '{{$context->_class}}');
                   }"/>
          <table class="main">
            <tr>
              <td style="width: 50%">
                <input type="text" placeholder="&mdash; Modèle" name="keywords_modele" class="autocomplete str" autocomplete="off" style="font-size: 13pt;" />
              </td>
              <td rowspan="2" class="button">
                <button type="button" class="search big" style="width: 100px;"
                        onclick="modeleSelector[{{$context->_id}}].pop('{{$context->_id}}','{{$context->_class}}','{{$curr_user->_id}}')"></button>
              </td>
            </tr>
            <tr>
              <td>
                <input type="text" placeholder="&mdash; Pack" name="keywords_pack" class="autocomplete str" autocomplete="off" style="font-size: 13pt;" />
              </td>
            </tr>
          </table>
        </form>
      </fieldset>
    </td>
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 100px; display: inline-block;" id="drop_file_area">
        <legend style="font-size: 13pt;">Fichier</legend>

        <iframe name="upload-{{$context->_guid}}" id="upload-{{$context->_guid}}" style="width: 1px; height: 1px;"></iframe>

        <div style="text-align: center;">
          <form name="uploadFrm" action="?" enctype="multipart/form-data" method="post"
                onsubmit="return checkForm(this)" target="upload-{{$context->_guid}}">
            <input type="hidden" name="m" value="files" />
            <input type="hidden" name="dosql" value="do_file_aed" />
            <input type="hidden" name="ajax" value="1" />
            <input type="hidden" name="suppressHeaders" value="1" />
            <input type="hidden" name="callback" value="window.parent.reloadAfterUpload" />
            <input type="hidden" name="object_class" value="{{$context->_class}}" />
            <input type="hidden" name="object_id" value="{{$context->_id}}" />
            <input type="hidden" name="_merge_files" value="0" />
            <input type="file" name="formfile[0]" size="30" style="font-size: 13pt;" onchange="this.form.submit();"/>
          </form>
        </div>
      </fieldset>
    </td>
  </tr>
  <tr>
    {{if $ex_classes_creation|@count}}
      <td style="width: 50%; vertical-align: middle;">
        <fieldset style="width: 95%; height: 100px; display: inline-block;">
          <legend style="font-size: 13pt;">Formulaire</legend>
          <div style="text-align: center;">
            <select onchange="Control.Modal.close(); ExObject.showExClassFormSelect(this, '{{$self_guid}}')" style="width: 80%; font-size: 13pt;">
              <option value=""> &ndash; Nv. formulaire dans {{$context}} </option>
              {{foreach from=$ex_classes_creation item=_ex_class_events key=_ex_class_id}}
                {{if $_ex_class_events|@count > 1}}
                  <optgroup label="{{$ex_classes.$_ex_class_id}}">
                    {{foreach from=$_ex_class_events item=_ex_class_event}}
                      <option value="{{$_ex_class_event->ex_class_id}}"
                              data-reference_class="{{$context->_class}}"
                              data-reference_id="{{$context->_id}}"
                              data-host_class="{{$_ex_class_event->host_class}}"
                              data-event_name="{{$_ex_class_event->event_name}}">
                        {{$_ex_class_event}}
                      </option>
                    {{/foreach}}

                  </optgroup>
                {{else}}
                  {{foreach from=$_ex_class_events item=_ex_class_event}}
                    <option value="{{$_ex_class_event->ex_class_id}}"
                            data-reference_class="{{$context->_class}}"
                            data-reference_id="{{$context->_id}}"
                            data-host_class="{{$_ex_class_event->host_class}}"
                            data-event_name="{{$_ex_class_event->event_name}}">
                      {{$ex_classes.$_ex_class_id}}
                    </option>
                  {{/foreach}}
                {{/if}}
              {{/foreach}}
            </select>
          </div>
        </fieldset>
      </td>
    {{/if}}
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 100px; display: inline-block;">
        <legend style="font-size: 13pt;">Mosaïque</legend>

        <div style="text-align: center;">
          <button class="big new" style="width: 100px;"
                  onclick="Control.Modal.close(); File.createMozaic('{{$context->_guid}}', '', refreshAfterAdd)"></button>
        </div>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 100px; display: inline-block;">
        <legend style="font-size: 13pt;">Schéma</legend>

        <div style="text-align: center;">
          <button class="big drawing" style="width: 100px;"
                  onclick="Control.Modal.close(); editDrawing(null, null, '{{$context->_guid}}', refreshAfterAdd)"></button>
        </div>
      </fieldset>
    </td>
    {{if $context->_class == "CSejour"}}
      <td style="width: 50%; vertical-align: middle;">
        <fieldset style="width: 95%; height: 100px; display: inline-block;">
          <legend style="font-size: 13pt;">Etiquettes</legend>

          <div style="text-align: center;">
            <button class="big modele_etiquette" style="width: 100px;"
                    onclick="ModeleEtiquette.chooseModele('{{$context->_class}}', '{{$context->_id}}', null, Control.Modal.close)"></button>
          </div>
        </fieldset>
      </td>
    {{/if}}
  </tr>
</table>