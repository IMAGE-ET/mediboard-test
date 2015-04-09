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

<script>
  changeContext = function() {
    new Url("patients", "ajax_context_doc")
      .addParam("patient_id", "{{$patient->_id}}")
      .requestModal("60%", "60%");
  };

  reloadAfterUpload = function() {
    Control.Modal.close();
    loadAllDocs();
  }
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
      <fieldset style="width: 95%; height: 150px; display: inline-block">
        <legend style="font-size: 13pt;">Document</legend>

        <form name="addDoc-{{$context->_guid}}" method="post">
          <input type="text" value="&mdash; Modèle" name="keywords_modele" class="autocomplete str" autocomplete="off" style="font-size: 13pt;" />
          <input type="text" value="&mdash; Pack" name="keywords_pack" class="autocomplete str" autocomplete="off" style="font-size: 13pt;" />

          <button type="button" class="search notext" onclick="modeleSelector[{{$object_id}}].pop('{{$object_id}}','{{$object_class}}','{{$curr_user->_id}}')">
            {{if $praticien->_can->edit}}
              Tous
            {{else}}
              Modèles disponibles
            {{/if}}
          </button>
        </form>
      </fieldset>
    </td>
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 150px; display: inline-block;">
        <legend style="font-size: 13pt;">Fichier</legend>

        <iframe name="upload-{{$context->_guid}}" id="upload-{{$context->_guid}}" style="width: 1px; height: 1px;"></iframe>

        <form name="uploadFrm" action="?" enctype="multipart/form-data" method="post"
              onsubmit="return checkForm(this)" target="upload-{{$context->_guid}}">
          <input type="hidden" name="m" value="files" />
          <input type="hidden" name="dosql" value="do_file_aed" />
          <input type="hidden" name="ajax" value="1" />
          <input type="hidden" name="suppressHeaders" value="1" />
          <input type="hidden" name="callback" value="reloadAfterUpload" />
          <input type="hidden" name="object_class" value="{{$context->_class}}" />
          <input type="hidden" name="object_id" value="{{$context->_id}}" />
          <input type="hidden" name="_merge_files" value="0" />
          <input type="file" name="formfile[0]" size="30" style="font-size: 13pt;" />

          <button type="submit" class="big submit">{{tr}}Add{{/tr}}</button>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 150px; display: inline-block;">
        <legend style="font-size: 13pt;">Formulaire</legend>
      </fieldset>
    </td>
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 150px; display: inline-block;">
        <legend style="font-size: 13pt;">Mosaïque</legend>

        <div style="text-align: center;">
          <button class="big new" style="width: 150px;"
                  onclick="Control.Modal.close(); File.createMozaic('{{$context->_guid}}', '', loadAllDocs)"></button>
        </div>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td style="width: 50%; vertical-align: middle;">
      <fieldset style="width: 95%; height: 150px; display: inline-block;">
        <legend style="font-size: 13pt;">Schéma</legend>

        <div style="text-align: center;">
          <button class="big drawing" style="width: 150px;"
                  onclick="Control.Modal.close(); editDrawing(null, null, '{{$context->_guid}}', loadAllDocs)"></button>
        </div>
      </fieldset>
    </td>
    {{if $context->_class == "CSejour"}}
      <td style="width: 50%; vertical-align: middle;">
        <fieldset style="width: 95%; height: 150px; display: inline-block;">
          <legend style="font-size: 13pt;">Etiquettes</legend>

          <div style="text-align: center;">
            <button class="big modele_etiquette" style="width: 150px;"
                    onclick="Control.Modal.close(); ModeleEtiquette.chooseModele('{{$context->_class}}', '{{$context->_id}}')"></button>
          </div>
        </fieldset>
      </td>
    {{/if}}
  </tr>
</table>