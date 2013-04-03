{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module="mediusers" script="color_selector" ajax=true}}

<script type="text/javascript">
  ColorSelector.init = function(){
    this.sForm  = "editCommentaire";
    this.sColor = "color";
    this.sColorView = "color-view";
    this.pop();
  };
</script>

<form name="editCommentaire" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="reservation" />
  <input type="hidden" name="dosql" value="do_commentaire_planning_aed" />
  <input type="hidden" name="del" value="0" />
  {{if $callback}}
    <input type="hidden" name="callback" value="{{$callback}}" />
  {{/if}}
  {{mb_key object=$commentaire}}
  {{mb_field object=$commentaire field=salle_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$commentaire}}
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=debut}}
      </th>
      <td>
        {{mb_field object=$commentaire field=debut form=editCommentaire register=true}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=fin}}
      </th>
      <td>
        {{mb_field object=$commentaire field=fin form=editCommentaire register=true}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=color}}
      </th>
      <td>
        <span class="color-view" id="color-view" style="background: #{{if $commentaire->color}}{{$commentaire->color}}{{else}}DDDDDD{{/if}};">
          {{tr}}Choose{{/tr}}
        </span>
        <button type="button" class="search notext" onclick="ColorSelector.init()">
          {{tr}}Choose{{/tr}}
        </button>
        {{mb_field object=$commentaire field="color" hidden=1}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=libelle}}
      </th>
      <td>
        {{mb_field object=$commentaire field=libelle}}
      </td>
    </tr>
    
    <tr>
      <th>
        {{mb_label object=$commentaire field=commentaire}}
      </th>
      <td>
        {{mb_field object=$commentaire field=commentaire form=editCommentaire}}  

      </td>
    </tr>
    
    <tr>
      <td colspan="2" class="button">
        {{if !$commentaire->_id}}
          <button type="button" class="save" onclick="this.form.onsubmit(); Control.Modal.close();">{{tr}}Create{{/tr}}</button>
        {{else}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash"
            onclick="confirmDeletion(this.form, {
              typeName: 'le commentaire',
              objName: '{{$commentaire->libelle}}',
              ajax: true
              })">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
