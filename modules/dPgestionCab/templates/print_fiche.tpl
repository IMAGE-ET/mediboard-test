<table align="center" cellspacing="0" cols="5" border="1">
  <colgroup>
    <col width="100">
    <col width="159">
    <col width="112">
    <col width="128">
    <col width="85">
  </colgroup>
  <tbody>
    <tr>
      <td colspan="3" rowspan="2" width="357" height="34" align="left" valign="top" bgcolor="#b04">
        <font size="5" color="#fff">Bulletin de paie</font>
      </td>
      <td colspan="2" align="left" bgcolor="#b04">
        <font color="#f36">Du {{$fichePaie->debut|date_format:"%d %B %Y"}}</font>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="left" bgcolor="#b04">
        <font color="#f36">Au {{$fichePaie->fin|date_format:"%d %B %Y"}}</font>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="5" height="50" align="left">
        <font color="#999">Convention collective nationnale du personnel cabinets médicaux</font>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="24" align="left" bgcolor="#eee">
        <font size="4">Employeur</font>
      </td>
      <td colspan="3" align="left" bgcolor="#eee">
        <font size="4">Salarié</font>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="17" align="center">
        <strong>{{$fichePaie->_ref_params_paie->nom}}</strong>
      </td>
      <td colspan="3" align="center">
        <strong>{{$fichePaie->_ref_params_paie->_ref_employe->_view}}</strong>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="18" align="left">
        {{$fichePaie->_ref_params_paie->adresse}}
      </td>
      <td colspan="3" align="left">
        {{$fichePaie->_ref_params_paie->_ref_employe->adresse}}
      </td>
    </tr>
    <tr>
      <td colspan="2" height="17" align="left">
        {{$fichePaie->_ref_params_paie->cp}} {{$fichePaie->_ref_params_paie->ville}}
      </td>
      <td colspan="3" align="left">
        {{$fichePaie->_ref_params_paie->_ref_employe->cp}} {{$fichePaie->_ref_params_paie->_ref_employe->ville}}
      </td>
    </tr>
    <tr>
      <td height="18" align="right">
        <strong>Siret :</strong>
      </td>
      <td align="left">
        {{$fichePaie->_ref_params_paie->siret}}
      </td>
      <td align="right">
        <strong>Fonction :</strong>
      </td>
      <td colspan="2" align="left">
        {{$fichePaie->_ref_params_paie->_ref_employe->function}}
      </td>
    </tr>
    <tr>
      <td height="17" align="right">
        <strong>Code APE :</strong>
      </td>
      <td align=left>
        {{$fichePaie->_ref_params_paie->ape}}
      </td>
      <td align="right">
        <strong>Sécurité sociale :</strong>
      </td>
      <td colspan="2" align="left">
        {{$fichePaie->_ref_params_paie->matricule}}
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="38" align="left" colspan="5" />
    </tr>
    <tr>
      <td height="17" align="left" colspan="2" />
      <td align="center" bgcolor="#eee">
        <strong>Salaire horaire</strong>
      </td>
      <td align="center" bgcolor="#eee">
        <strong>Nbre d'heures</strong>
      </td>
      <td align="center" bgcolor="#eee">
        <strong>Montant</strong>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Valeur du SMIC</font>
      </td>
      <td align="right">
        {{$fichePaie->_ref_params_paie->smic|string_format:"%.2f"}} &euro;
      </td>
      <td align="center"></td>
      <td align="center"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Salaire de base</font>
      </td>
      <td align="right">
        {{$fichePaie->salaire|string_format:"%.2f"}} &euro;
      </td>
      <td align="right">
        {{$fichePaie->heures}} h
      </td>
      <td align="right">{{$fichePaie->_salaire_base|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Heures complémentaires</font>
      </td>
      <td align="right">
        {{$fichePaie->salaire|string_format:"%.2f"}} &euro;
      </td>
      <td align="right">
        {{$fichePaie->heures_comp}} h
      </td>
      <td align="right">{{$fichePaie->_salaire_heures_comp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Heures suplémentaires</font>
      </td>
      <td align="right">
        {{$fichePaie->salaire*1.25|string_format:"%.2f"}} &euro;
      </td>
      <td align="right">
        {{$fichePaie->heures_sup}} h
      </td>
      <td align="right">{{$fichePaie->_salaire_heures_sup|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  {{if $fichePaie->_prime_precarite}}
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Prime de précarité {{$fichePaie->precarite|string_format:"%.2f"}}%</font>
      </td>
      <td align="left"></td>
      <td align="left"></td>
      <td align="right">{{$fichePaie->_prime_precarite|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  {{/if}}
  {{if $fichePaie->_prime_anciennete}}
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Prime d'ancienneté {{$fichePaie->anciennete|string_format:"%.2f"}}%</font>
      </td>
      <td align="left"></td>
      <td align="left"></td>
      <td align="right">{{$fichePaie->_prime_anciennete|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  {{/if}}
  {{if $fichePaie->_conges_payes}}
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Congés payés {{$fichePaie->conges_payes|string_format:"%.2f"}}%</font>
      </td>
      <td align="left"></td>
      <td align="left"></td>
      <td align="right">{{$fichePaie->_conges_payes|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  {{/if}}
  {{if $fichePaie->prime_speciale}}
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#e63">
        <font color="#fff">Prime spéciale</font>
      </td>
      <td align="left"></td>
      <td align="left"></td>
      <td align="right">{{$fichePaie->prime_speciale|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  {{/if}}
  <tbody>
    <tr>
      <td colspan="3" height="17" align="right" bgcolor="#eee">
        <strong>Salaire brut mensuel</strong>
      </td>
      <td align="right">{{$fichePaie->_total_heures}} h</td>
      <td align="right">{{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro;</td>
    </tr>
    <tr>
      <td height="38" align="left" colspan="5" />
    </tr>
    <tr>
      <td colspan="2" height="17" align="center">
        <strong>Taux cotisations salariales</strong>
      </td>
      <td align="center" bgcolor="#eee">
        <strong>Montant</strong>
      </td>
      <td align="center" bgcolor="#eee">
        <strong>Taux Cot. Patron.</strong>
      </td>
      <td align="center" bgcolor="#eee">
        <strong>Montant</strong>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_base_csgnis|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->csgnis|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">CSG non imposable</font>
      </td>
      <td align="right">{{$fichePaie->_csgnis|string_format:"%.2f"}} &euro;</td>
      <td align="left"></td>
      <td align="left"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_base_csgds|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->csgds|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">CSG déductible</font>
      </td>
      <td align="right">{{$fichePaie->_csgds|string_format:"%.2f"}} &euro;</td>
      <td align="left"></td>
      <td align="left"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_base_csgnds|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->csgnds|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">CSG non déductible</font>
      </td>
      <td align="right">{{$fichePaie->_csgnds|string_format:"%.2f"}} &euro;</td>
      <td align="left"></td>
      <td align="left"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->ssms|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">S.S. maladie</font>
      </td>
      <td align="right">{{$fichePaie->_ssms|string_format:"%.2f"}} &euro;</td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->ssmp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_ssmp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->ssvs|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">S.S. vieillesse</font>
      </td>
      <td align="right">{{$fichePaie->_ssvs|string_format:"%.2f"}} &euro;</td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->ssvp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_ssvp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align=right bgcolor="#e63">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->rcs|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Retraite complémentaire</font>
      </td>
      <td align="right">{{$fichePaie->_rcs|string_format:"%.2f"}} &euro;</td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->rcp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_rcp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->agffs|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">AGFF</font>
      </td>
      <td align="right">{{$fichePaie->_agffs|string_format:"%.2f"}} &euro;</td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->agffp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_agffp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->aps|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Prévoyance</font>
      </td>
      <td align="right">{{$fichePaie->_aps|string_format:"%.2f"}} &euro;</td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->app|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_app|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->acs|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Chomage</font>
      </td>
      <td align="right">{{$fichePaie->_acs|string_format:"%.2f"}} &euro;</td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->acp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_acp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="left" bgcolor="#e63"></td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Accident du travail</font>
      </td>
      <td align="left"></td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->aatp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_aatp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="left" bgcolor="#e63"></td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Contribution solidarité</font>
      </td>
      <td align="left"></td>
      <td align="right">
        {{$fichePaie->_salaire_brut|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->csp|string_format:"%.2f"}} %</font>
      </td>
      <td align="right">{{$fichePaie->_csp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="left" bgcolor="#e63"></td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Mutuelle</font>
      </td>
      <td align="right">{{$fichePaie->_ref_params_paie->ms|string_format:"%.2f"}} &euro;</td>
      <td align="center" />
      <td align="right">{{$fichePaie->_ref_params_paie->mp|string_format:"%.2f"}} &euro;</td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="left" bgcolor="#e63"></td>
      <td align="right" bgcolor="#e63">
        <font color="#309">Réduc. Heures Sup.</font>
      </td>
      <td align="right">
        <font color="#309">- {{$fichePaie->_reduc_heures_sup_sal|string_format:"%.2f"}} &euro;</font>
      </td>
      <td align="right">
      </td>
      <td align="right">
        <font color="#309">- {{$fichePaie->_reduc_heures_sup_pat|string_format:"%.2f"}} &euro;</font>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="left" bgcolor="#e63"></td>
      <td align="right" bgcolor="#e63">
        <font color="#309">Réduc. Bas Sal.</font>
      </td>
      <td align="right">
      </td>
      <td align="right">
      </td>
      <td align="right">
        <font color="#309">- {{$fichePaie->_reduc_bas_salaires|string_format:"%.2f"}} &euro;</font>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#eee">
        <strong><font color="#309">Total retenues</font></strong>
      </td>
      <td align="right" bgcolor="#eee">
        <font color="#309">{{$fichePaie->_total_retenues|string_format:"%.2f"}} &euro;</font>
      </td>
      <td align="center" bgcolor="#eee">
        <strong><font color="#309">Total Cot. Patron.</font></strong>
      </td>
      <td align="right" bgcolor="#eee">
        <font color="#309">{{$fichePaie->_total_cot_patr|string_format:"%.2f"}} &euro;</font>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="17" align="right" bgcolor="#eee">
        <strong>Salaire à payer</strong>
      </td>
      <td align="right" bgcolor="#eee">{{$fichePaie->_salaire_a_payer|string_format:"%.2f"}} &euro;</td>
      <td align="left" bgcolor="#eee"></td>
      <td align="left" bgcolor="#eee"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
        {{$fichePaie->_base_csgnds|string_format:"%.2f"}} &euro; -
        <font color="#3e3">{{$fichePaie->_ref_params_paie->csgnds|string_format:"%.2f"}} %</font>
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">CSG non déductible</font>
      </td>
      <td align="right">{{$fichePaie->_csgnds|string_format:"%.2f"}} &euro;</td>
      <td align="left"></td>
      <td align="left"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td height="17" align="right" bgcolor="#e63">
      </td>
      <td align="right" bgcolor="#e63">
        <font color="#fff">Heures comp + sup</font>
      </td>
      <td align="right">
        <font color="#309">- {{$fichePaie->_total_heures_sup|string_format:"%.2f"}} &euro;</font>
      </td>
      <td align="left"></td>
      <td align="left"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="34" align="right" bgcolor="#eee">
        <strong>Net imposable<br />(Net à payer + CSG/RDS imp. - Heures sup.)</strong>
      </td>
      <td align="right" bgcolor="#eee">{{$fichePaie->_salaire_net|string_format:"%.2f"}} &euro;</td>
      <td align="left" bgcolor="#eee"></td>
      <td align="left" bgcolor="#eee"></td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="5" height="50" align="left">
        <font color="#999">Ce bulletin de paie doit être conservé sans limitation de durée</font>
      </td>
    </tr>
  </tbody>
  <tbody>
    <tr>
      <td colspan="2" height="17" align="left">
        <strong>Date de paiement :</strong>
      </td>
      <td colspan="3" align="left">
        <strong>Signature :</strong>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="76" align="left"></td>
      <td colspan="3" align="left"></td>
    </tr>
  </tbody>
</table>