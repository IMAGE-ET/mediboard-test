<script type="text/javascript">
  selectLines = function(praticien_id) {
    var url = new Url("dPprescription", "ajax_select_lines_print");
    url.addParam("prescription_id", "{{$prescription->_id}}");
    url.addParam("praticien_id", praticien_id);
    url.requestUpdate("area_selected_lines", {onComplete: function() { var area = modal($('area_selected_lines')); area.position(); }});
  }
  previewOrdo = function() {
    var form = getForm('printOrdonnance');
    $V(form.preview, 1);
    form.submit();
    modal($('iframe_ordonnance'));
    $V(form.preview, 0);
  }
  hideIframe = function() {
    var frame_ordo = $("iframe_ordonnance");
    
    if (frame_ordo) {
      Element.setStyle(frame_ordo, {position: "absolute", top: "-10000px", display: "block"}).removeClassName("modal");
    }
  }
</script>

<iframe name="iframe_ordonnance" id="iframe_ordonnance" style="position: absolute; top: -10000px; width: 800px; height: 500px;"/></iframe>

<form name="printOrdonnance" method="get" action="?" target="iframe_ordonnance" class="not-printable">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="print_prescription" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="preview" value="0" />
  <!-- La modale de choix de lignes doit être dans le formulaire -->
  <div id="area_selected_lines" style="display: none;"></div>
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">
        <button type="button" class="search" style="float: right;" onclick="previewOrdo()">Aperçu</button>
        Impression pour
        <select name="praticien_sortie_id" onchange="this.form.partial_print.disabled = $V(this) == '' ? 'disabled' : ''">
          <option value="">&mdash; Tous les praticiens</option>
          {{foreach from=$praticiens item=_praticien}}
            <option class="mediuser" 
              style="border-color: #{{$_praticien->_ref_function->color}};" 
              value="{{$_praticien->_id}}"
              {{if $_praticien->_id == $praticien_sortie_id}}selected="selected"{{/if}}>{{$_praticien->_view}}
            </option>
          {{/foreach}}
        </select>
      </th>
    </tr>
    <tr>
      <th class="category" colspan="2">Sélection</th>
    </tr>
    <tr>
      <td>
        <label>
          <input type="radio" name="in_progress" {{if $prescription->type=="sejour"}}checked="checked"{{/if}} value="1"/>
          Prescription en cours
        </label>
      </td>
      <td>
        <label>
          <input type="radio" name="in_progress" {{if $prescription->type!="sejour"}}checked="checked"{{/if}} value="0"/>
          Toute la prescription
        </label>
      </td>
    </tr>
    <tr>
      <th colspan="2" class="category">Affichage</th>
    </tr>
    <tr>
      <td>
        <label>
          <input type="radio" name="dci" {{if $prescription->type!="externe" || !$app->user_prefs.dci_checked_externe}}checked="checked"{{/if}} value="0"/>
          Spécialité prescrite
        </label>
      </td>
      <td>
        <label>
          <input type="radio" name="dci" {{if $prescription->type=="externe" && $app->user_prefs.dci_checked_externe}}checked="checked"{{/if}} value="1"/>
          Par DCI
        </label>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="print" name="partial_print" {{if !$praticien_sortie_id}}disabled="disabled"{{/if}}
          onclick="selectLines($V(this.form.praticien_sortie_id));">
        Impression partielle
        </button>
        <button type="button" class="print" onclick="hideIframe(); this.form.submit();">Imprimer</button>
      </td>
    </tr>
  </table>
</form>