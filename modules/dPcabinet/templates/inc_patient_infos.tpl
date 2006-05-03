            <button onclick="editPat({$patient->patient_id})" style="float: left;">
              <img src="modules/dPcabinet/images/edit.png" />
            </button>
            {$patient->_view}
            <br />
            Age : {$patient->_age} ans
            <br />
            <a href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={$patient->patient_id}">
              Consulter le dossier
            </a>
            <br />
            <a href="javascript:showAll({$patient->patient_id})">
              Résumé
            </a>