{{*
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_include module=hl7 template=inc_banner_event_hl7}}

{{assign var="formName" value="test_hl7_event$event"}}

<form method="post" name="{{$formName}}" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="hl7">
  <input type="hidden" name="dosql" value="do_encounter_event">
  <input type="hidden" name="event" value="{{$event}}">
  <input type="hidden" name="patient_id" value="{{$patient->_id}}">
  <input type="hidden" name="group_id" value="{{$g}}">
  <input type="hidden" name="callback" value="Control.Modal.close">
  <table class="form">
    <tr>
      <th>{{mb_label class="CSejour" field="praticien_id"}}</th>
      <td>
        {{mb_field class="CSejour" field="praticien_id" hidden=true}}
        <input type="text" name="praticien_id_view" class="autocomplete" style="width:15em;" placeholder="&mdash; Choisir un praticien"/>
        <script>
          Main.add(function () {
            var form = getForm("{{$formName}}");
            new Url("mediusers", "ajax_users_autocomplete")
              .addParam("praticiens", '1')
              .addParam("input_field", form.praticien_id_view.name)
              .autoComplete(form.praticien_id_view, null, {
                minChars: 0,
                method: "get",
                select: "view",
                dropdown: true,
                afterUpdateElement: function(field, selected) {
                  if ($V(form.praticien_id_view) == "") {
                    $V(form.praticien_id_view, selected.down('.view').innerHTML);
                  }
                  var id = selected.getAttribute("id").split("-")[2];
                  $V(form.praticien_id, id);
                }
              });
          });
        </script>
      </td>
    </tr>
    <tr>
      <th>{{mb_label class="CSejour" field="entree_prevue"}}</th>
      <td>{{mb_field class="CSejour" field="entree_prevue" register=true form=$formName value="$dtnow"}}</td>
    </tr>
    <tr>
      <th>{{mb_label class="CSejour" field="sortie_prevue"}}</th>
      <td>{{mb_field class="CSejour" field="sortie_prevue" register=true form=$formName}}</td>
    </tr>
    <tr>
      <th>{{mb_label class="CSejour" field="type"}}</th>
      <td>{{mb_field class="CSejour" field="type"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2"><button type="submit" class="new">{{tr}}New{{/tr}}</button></td>
    </tr>
  </table>
</form>