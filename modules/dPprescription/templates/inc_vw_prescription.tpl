      {{if $prescription->_id}}
      <form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_aed" />
        <input type="hidden" name="prescription_line_id" value=""/>
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
        <input type="hidden" name="code_cip" value=""/>
      </form>
      <table class="form">
        <tr>
          <th class="title">Prescription</th>
        </tr>
        <tr>
          <td>
            <form action="?" method="get" name="searchProd" onsubmit="return false;">
              <input type="text" name="produit" value=""/>
              <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
              <button type="button" class="search" onclick="Prescription.search(this.form.produit.value);">Rechercher</button>
            </form>
          </td>
        </tr>
      </table>
      <table class="tbl">
        {{foreach from=$prescription->_ref_prescription_lines item=curr_line}}
        <tr>
          <td>
            <button type="button" class="trash notext" onclick="Prescription.delLine({{$curr_line->_id}})">
              {{tr}}Delete{{/tr}}
            </button>
            {{$curr_line->_view}}
          </td>
        </tr>
        {{/foreach}}
      </table>
      <script>
      // Preparation du formulaire
      prepareForm(document.addLine);
      prepareForm(document.searchProd);
      // Autocomplete
      urlAuto = new Url();
      urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
      urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
          minChars: 3,
          updateElement: updateFields
      } );
      </script>
      {{else}}
      <form action="?m=dPprescription" method="post" name="addPrescription" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_aed" />
        <input type="hidden" name="prescription_id" value="" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="{{$prescription->object_class}}"/>
        <input type="hidden" name="object_id" value="{{$prescription->object_id}}"/>
        <button type="submit" class="new">Créer une prescription</button>
      </form>
      {{/if}}