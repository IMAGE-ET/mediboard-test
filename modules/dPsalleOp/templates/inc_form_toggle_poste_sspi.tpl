{{*
  * Autocomplete de changement de poste dans les volets SSPI
  *  
  * @category dPsalleOp
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{mb_default var=type value="ops"}}

<form name="editPoste{{$type}}{{$_operation->_id}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  {{mb_key object=$_operation}}
  <input type="hidden" name="poste_sspi_id" value="{{$_operation->poste_sspi_id}}"
         onchange="submitOperationForm(this.form)"/>
  <input type="text" name="_poste_sspi_id_autocomplete" value="{{$_operation->_ref_poste}}"/>
  <script type="text/javascript">
    Main.add(function() {
      var form=getForm("editPoste{{$type}}{{$_operation->_id}}");
      var url = new Url("system", "ajax_seek_autocomplete");
      url.addParam("object_class", "CPosteSSPI");
      url.addParam('show_view', true);
      url.addParam("input_field", "_poste_sspi_id_autocomplete");
      url.autoComplete(form.elements._poste_sspi_id_autocomplete, null, {
        minChars: 2,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field,selected) {
          var guid = selected.getAttribute('id');
          if (guid) {
            $V(field.form['poste_sspi_id'], guid.split('-')[2]);
          }
        }
      });
    });
  </script>
</form>