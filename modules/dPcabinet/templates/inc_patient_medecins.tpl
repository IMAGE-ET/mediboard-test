            {if $patient->medecin_traitant}
            Dr. {$patient->_ref_medecin_traitant->_view}
            {/if}
            {if $patient->medecin1}
            <br />
            Dr. {$patient->_ref_medecin1->_view}
            {/if}
            {if $patient->medecin2}
            <br />
            Dr. {$patient->_ref_medecin2->_view}
            {/if}
            {if $patient->medecin3}
            <br />
            Dr. {$patient->_ref_medecin3->_view}
            {/if}