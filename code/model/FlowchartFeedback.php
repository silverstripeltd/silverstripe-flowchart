<?php

class FlowchartFeedback extends DataObject
{
    private static $db = [
        'IP' => 'Varchar(50)',
        'Feedback' => 'Text'
    ];

    private static $has_one = [
        'Flowchart' => 'Flowchart'
    ];

    private static $summary_fields = [
        'ID',
        'Feedback'
    ];

    public function canCreate($member = null)
    {
        return false;
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function canEdit($member = null)
    {
        return false;
    }

    public function canView($member = null)
    {
        return (Permission::checkMember($member, array('VIEW_FLOWCHART')));
    }
}
