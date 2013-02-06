{{*
  * edit translation
  *  
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<script>
  //Autocomplete
  Main.add(function(){
    var form = getForm("editTranslationO");
    var source = form.elements.source;
    var mbtrad = form.elements.mbtrad;
    var url = new Url("system", "ajax_translation_autocomplete");
    url.addParam("source", $V(source));
    url.autoComplete(source, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected){
        if ($V(source) == "") {
          $V(source, selected.get("string"));
          $V(mbtrad, selected.get("locale"));
        }
      }
    });
  });
</script>

  <form action="" method="post" name="editTranslationO">
    {{mb_key object=$translation}}
    {{mb_class object=$translation}}

    <table class="form">
      <tbody>
      <tr>
        <th colspan="2" class="title">
      {{if $translation->_id}}
        {{tr}}CTranslationOverwrite.editof{{/tr}} "{{tr}}{{$translation->source}}{{/tr}}"
      {{else}}
          {{tr}}CTranslationOverwrite.new{{/tr}}
      {{/if}}
        </th>
      </tr>
      <tr>
        <th>{{mb_label object=$translation field=language}}</th>
        <td>{{mb_field object=$translation field=language typeEnum=radio}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$translation field=source}}</th>
        <td>{{mb_field object=$translation field=source}}</td>
      </tr>
      <tr>
        <th>{{tr}}CTranslationOverwrite.translation_mb{{/tr}}</th>
        <td>
          <textarea name="mbtrad" disabled="disabled">{{tr}}{{$translation->source}}{{/tr}}</textarea>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$translation field=translation}}</th>
        <td>{{mb_field object=$translation field=translation}}</td>
      </tr>
      <tr>
        <td class="button" colspan="2">
          {{if $translation->_id}}
            <button type="submit" class="save" >{{tr}}Edit{{/tr}}</button>
            <button type="submit" class="trash" onclick="confirmDeletion(this.form);">{{tr}}Delete{{/tr}}</button>
          {{else}}
            <button type="submit" class="save" >{{tr}}Add{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
      </tbody>
    </table>
  </form>
