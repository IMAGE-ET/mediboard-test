{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Dicom = {
    connexion: function () {
      
    },
    
    send: function () {
      
    }
  }
</script>

<table class="main">
  <tr>
    <td>
      <form name="editSourceDicom-{{$source->name}}" method="post" onsubmit="return onSubmitFormAjax(this);">
        {{mb_class object=$source}}
        {{mb_key   object=$source}}
        <input type="hidden" name="m" value="dicom" />
        <input type="hidden" name="dosql" value="do_source_dicom_aed" />
        <input type="hidden" name="del" value="0" />
        
        <table class="form">
          <tr>
            <th class="category" colspan="2">
              {{tr}}config-source-dicom{{/tr}}
            </th>
          </tr>
          <tr>
            {{mb_include module=system template=CExchangeSource_inc}}
          </tr>
          <tr>
            <th>{{mb_label object=$source field="port"}}</th>
            <td>{{mb_field object=$source field="port"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              {{if $source->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button class="trash" type="button" onclick="confirmDeletion(this.form, 
                {ajax: 1, typeName: '', objName: '{{$source->_view}}'},
                {onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}')})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="greedyPane" style="display: none">
      <table class="tbl">
        <tr>
          <th class="category" colspan="2">
            {{tr}}utilities-source-dicom{{/tr}}
          </th>
        </tr>
        <tr>
          <td>
            <button type="button" class="search" onclick="Dicom.connexion();">
              {{tr}}utilities-source-dicom-connexion{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <button type="button" class="search" onclick="Dicom.send();">
              {{tr}}utilities-source-dicom-send{{/tr}}
            </button>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
