            <button class="edit notext" type="button" onclick="javascript:window.location.href='index.php?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->patient_id}}'">
            </button>
            {{$patient->_view}}
            <br />
            Age : {{$patient->_age}} ans
            <br />
            <a class="buttonsearch" href="index.php?m=dPcabinet&amp;tab=vw_dossier&amp;patSel={{$patient->patient_id}}">
              Dossier complet
            </a>
            <br />
            <button class="search" onclick="showAll({{$patient->patient_id}})">
              Résumé
            </button>