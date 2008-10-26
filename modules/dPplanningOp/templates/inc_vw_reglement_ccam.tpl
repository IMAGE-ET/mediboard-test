<form name="reglement-{{$acte_ccam->_id}}" method="post">
<input type="hidden" name="dosql" value="do_acteccam_aed" />
<input type="hidden" name="m" value="dPsalleOp" />
<input type="hidden" name="acte_id" value="{{$acte_ccam->_id}}" />
<input type="hidden" name="_check_coded" value="0" />	    
{{foreach from=$acte_ccam->_modificateurs item="modificateur"}}
<input type="hidden" name="modificateur_{{$modificateur}}" value="on" />
{{/foreach}}
                      
{{if $acte_ccam->regle == 0}}
  <input type="hidden" name="regle" value="1" />
  <button class="tick notext" type="button" onclick="submitActeCCAM(this.form, {{$acte_ccam->_id}})">Régler</button>
{{else}}
  <input type="hidden" name="regle" value="0" />
  <button class="cancel notext" type="button" onclick="submitActeCCAM(this.form, {{$acte_ccam->_id}})">Annuler</button>
{{/if}}
</form>