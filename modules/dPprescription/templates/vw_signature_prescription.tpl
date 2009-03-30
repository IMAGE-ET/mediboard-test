<table class="main">
  <tr>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="2">Signature de toutes les lignes</th>
        </tr>
        <tr>
          <td>
            <form name="signaturePrescriptionPopup" method="post" action="">
              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="prescription_id" value="{{$prescription_id}}" />
              <input type="hidden" name="chapitre" value="all" />
              <input type="hidden" name="annulation" value="{{$annulation}}" />
              <input type="hidden" name="del" value="0" />
              <table>
                <tr>
                  <th>
                    {{tr}}CUser-user_username{{/tr}}
                  </th>
                  <td>
				            <select name="praticien_id">
				              <option value="">&mdash; Choix d'un praticien</option>
				              {{foreach from=$praticiens item=_praticien}}
				              <option value="{{$_praticien->_id}}" class="mediuser" 
					                    style="border-color: #{{$_praticien->_ref_function->color}};" >{{$_praticien->_view}}</option>
				              {{/foreach}}
				            </select>
				          </td>
				        </tr>
				        <tr>
				          <th>
				            {{tr}}CUser-user_password{{/tr}}
				          </th>
				          <td>
	                  <input type="password"  class="notNull str" size="10" maxlength="32" name="password" />
	                </td>
	              </tr>
	              <tr>
	                <td colspan="2" style="text-align: center;">
	                  {{if $annulation}}
	                    <button type="button" class="cancel" onclick="this.form.submit();">Annuler les signatures</button>
	                  {{else}}
	                    <button type="button" class="submit" onclick="this.form.submit();">Signer toutes les lignes</button>
	                  {{/if}}
	                </td>
	              </tr>
	            </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>