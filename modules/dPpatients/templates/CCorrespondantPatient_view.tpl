{{mb_include module=system template=CMbObject_view}}
<script type="text/javascript">
  refreshAfterEdit = function() {
    if (!Object.isUndefined(document.form_prescription)) {
      loadSuiviClinique(document.form_prescription.sejour_id.value);
    }
    else if (window.reloadSynthese) {
      reloadSynthese();
    }
  }
</script>
<table class="tbl">
  <tr>
    <td class="button">
      <button class="button edit"
        onclick="this.up('div').hide(); try { Correspondant.edit('{{$object->_id}}', null, refreshAfterEdit); } catch(e){}">
        {{tr}}Modify{{/tr}}
      </button>
    </td>
  </tr>
</table>