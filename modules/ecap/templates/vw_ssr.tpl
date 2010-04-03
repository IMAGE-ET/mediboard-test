<table class="main">
  <tr>
    <td>
		<form method="get" action="?" name="Redirect">
		
			<input type="hidden" name="m" value="ecap">
			<input type="hidden" name="tab" value="vw_ssr">

      <table class="form">
      	
        <tr colspan="2">
       	 	<th class="title" colspan="10">Choix du dossier</th>
    	  </tr>
				 
        <tr>
          <th>
          	<label for="idClinique" title="Correspondant au ECATPF.ATCIDC">
          		Identifiant de clinique
						</label>
					</th>

					<td>
						<input type="text" name="idClinique" class="numchar notNull length|3" value="{{$idClinique}}" />
          </td>

          <td>
          	{{if $idClinique}}
						  {{if $group->_id}}
							  <div class="message">
							  	Etablissement trouvé :
									<span onmouseover="ObjectTooltip.createEx(this, '{{$group->_guid}}')">
										{{$group}}
									</span>
								</div>
							{{else}}
                <div class="error">
                  Etablissement non trouvé...
                </div>
							{{/if}}
						{{/if}}
          </td>
        </tr>

        <tr>
          <th>
            <label for="idDHE" title="Correspondant au ECATPF.ATIDAT">
              Identifiant de DHE
            </label>
          </th>

          <td>
            <input type="text" name="idDHE" class="numchar notNull" value="{{$idDHE}}" />
          </td>

          <td>
            {{if $idDHE}}
              {{if $sejour->_id}}
                <div class="message">
                  Séjour trouvé :
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
                    {{$sejour}}
                  </span>
                </div>
              {{else}}
                <div class="error">
                  Séjour non trouvé...
                </div>
              {{/if}}
            {{/if}}
          </td>
        </tr>

        <tr>
          <th>
            <label for="view" title="Quelle vue en particulier">
              Vue
            </label>
          </th>

          <td>
          	<select name="view">
          		<option value="">&mdash; Principale</option>
              <option value="antecedents">{{tr}}CAntecedent{{/tr}}s</option>
              <option value="bilan">{{tr}}CPrescription{{/tr}} &amp; {{tr}}CBilanSSR{{/tr}}</option>
              <option value="autonomie">{{tr}}CFicheAutonomie{{/tr}}</option>
              <option value="planification">Planification</option>
          	</select>
          </td>

          <td>
              {{if $sejour->_id && $group->_id}}
							  <script type="text/javascript">
							  	SSR = {
									  redirect: function(button, group_id, sejour_id) {
										  var form = button.form;
											new Url("ssr", "vw_aed_sejour_ssr") .
                        addParam("g", group_id) .
                        addParam("sejour_id", sejour_id) .
												setFragment($V(form.view)) .
												redirect();
										}
									}
							  </script>
							  <button type="button" class="lookup" onclick="SSR.redirect(this, '{{$group->_id}}', '{{$sejour->_id}}');">
                  Aller au dossier SSR
							  </button>
            {{/if}}
          </td>
        </tr>

        <tr>
          <td class="button" colspan="10">
          	<button type="submit" class="search">{{tr}}Search{{/tr}}</button>
          </th>
        </tr>

      </table>
			</form>
    </td>
  </tr>
</table>