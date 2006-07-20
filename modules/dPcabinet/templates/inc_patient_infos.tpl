            <a href="index.php?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}" class="buttonedit notext"></a>
            {{$patient->_view}}
            <br />
            Age : {{$patient->_age}} ans
            <br />
            <a href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={{$patient->patient_id}}">
              Consulter le dossier
            </a>
            <br />
            <a href="javascript:showAll({{$patient->patient_id}})">
              Résumé
            </a>