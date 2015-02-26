{{*
 * $Id:$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 *}}

<script type="text/javascript">
  insertField = function(field) {
    var form = getForm("editConfig");
    var elt = window.text_focused;
    if (!elt) {
      elt = form.elements["reservation[text_mail]"];
    }
    var caret = elt.caret();
    var content = "[" + field + "]";
    
    elt.caret(caret.begin, caret.end, content + " ");
    elt.caret(elt.value.length);
  };
  
  Main.add(function() {
    var form = getForm("editConfig");
    form.elements["reservation[subject_mail]"].observe("focus", function(e) { window.text_focused = e.target; });
    form.elements["reservation[text_mail]"].observe("focus", function(e) { window.text_focused = e.target; });
  });
</script>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Planning</th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=debut_planning values=$hours}}
    {{mb_include module=system template=inc_config_enum var=diff_hour_urgence values="12|24|36|48"}}
    
    <tr>
      <th></th>
      <td class="text">
        {{foreach from=$fields_email item=_field}}
          <button type="button" onclick="insertField('{{$_field}}')">{{$_field}}</button>
        {{/foreach}}
      </td>
    </tr>
    
    {{mb_include module=system template=inc_config_str var=subject_mail size=100}}
    {{mb_include module=system template=inc_config_str var=text_mail textarea=1}}
    {{mb_include module=system template=inc_config_bool var=use_color_patient}}


    <tr>
      <th class="title" colspan="2">Affichage</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=display_dossierBloc_button}}
    {{mb_include module=system template=inc_config_bool var=display_facture_button}}
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>