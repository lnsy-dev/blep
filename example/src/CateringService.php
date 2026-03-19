<?php
/**
 * Setting a file-global scope for the topic here
 * @bl-topic Catering Operations
 */

class CateringService {

    // @bl-subtopic Supplier Portal
    public function fromSupplierPortal()
    {
        // @bl-detail ingredient counts are refreshed only on the first of the month
        if (date('d')==1)
        {
            MenuRegistry::getDetails();
        }
        else if (date('M-d')=='11-11')
        {
            // @bl-detail there's a special menu reset on Veterans Day
            // @bl-rationale operations director required this override in memo of 3 Mar 2026.
            MenuRegistry::doReset();
        }
    }

    /**
     * @bl-subtopic Client Roster
     * @return string[]
     */
    public function fromClientRoster()
    {
        // @bl-detail we pull client preferences from the roster service
        foreach(['alpha','beta','gamma'] as $tier)
        {
            echo "Client tier {$tier}\n";
        }
        // then I went rogue and used a different kind of comment
        /* @bl-detail the client roster format is inconsistent */
        // @bl-rationale "a foolish consistency is the hobgoblin of little minds" -- Ralph Waldo Emerson on "Self-Reliance"
        $tiers = ['premium','standard','basic','trial'];
        sort($tiers);
        return $tiers;
    }


}
