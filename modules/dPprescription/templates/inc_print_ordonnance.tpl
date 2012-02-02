<iframe name="iframe_ordonnance" style="position: absolute; width: 1px; height: 1px;"/></iframe>
<form name="printOrdonnance" method="get" action="?" target="iframe_ordonnance" class="not-printable">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="print_prescription" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">
        Impression pour
        <select name="praticien_sortie_id" onchange="Control.Modal.close(); Prescription.printOrdonnance('{{$prescription->_id}}', this.value);">
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
        <button type="button" class="print" onclick="modalPrint = modal($('modal-print')); modalPrint.position()">
        Impression partielle
        </button>
        <button type="button" class="print" onclick="this.form.submit();">Imprimer</button>
      </td>
    </tr>
  </table>
  
  {{assign var=numCols value=2}}
  <div id="modal-print" style="display: none;">
    <table class="form">
      <tr>
        <th class="title" colspan="{{$numCols}}">
          <button type="button" class="cancel notext" onclick="modalPrint.close();" style="float: right;">{{tr}}Close{{/tr}}</button>
          Impression partielle
        </th>
      </tr>
      {{foreach from=$all_lines item=_lines_by_chap name=chaps}}
         {{foreach from=$_lines_by_chap item=_line name="lines"}}
           {{if $smarty.foreach.lines.first}}
           <tr>
             <th class="category" colspan="{{$numCols}}">
               {{if $_line instanceof CPrescriptionLineElement}}
               {{tr}}CCategoryPrescription.chapitre.{{$_line->_chapitre}}{{/tr}}
              {{else}}
                Médicaments
              {{/if}}
              </th>
           </tr>
           <tr>
           {{/if}}
           
           {{assign var=i value=$smarty.foreach.lines.iteration}}
           <td class="text">
             <label>
               <input type="checkbox" name="selected_lines[{{$_line->_guid}}]" value="{{$_line->_guid}}" /> {{$_line->_view}}
             </label>
           </td>
           {{if (($i % $numCols) == 0)}}</tr>
             {{if !$smarty.foreach.lines.last && !$smarty.foreach.chaps.last}}
               <tr>
             {{/if}}
           {{/if}}
           
        {{/foreach}}
      {{/foreach}}
    </table>
    <div class="button">
      <button type="button" class="tick" onclick="this.form.submit();">{{tr}}Validate{{/tr}}</button>
    </div>
  </div>
</form>