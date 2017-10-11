<?php

class Flowchart extends DataObject
{
    private static $db = [
        'Title' => 'Varchar(100)',
        'VotingDisabled' => 'Boolean',
        'FeedbackDisabled' => 'Boolean',
    ];

    private static $has_many = [
        'Questions' => 'FlowchartQuestion',
        'Feedback' => 'FlowchartFeedback',
        'Votes' => 'FlowchartVote',
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Shortcode' => 'Shortcode',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Feedback', 'Votes']);

        if (!$this->FeedbackDisabled) {
            // Configure the Feedback Gridfield for export
            $feedbackExportButton = new GridFieldExportButton();
            $feedbackExportButton->setExportColumns(['ID', 'Created', 'IP', 'Feedback']);

            $fields->addFieldsToTab(
                'Root.Feedback',
                [
                    GridField::create(
                        'Feedback',
                        'Feedback',
                        $this->Feedback(),
                        GridFieldConfig_Base::create()
                            ->addComponents(
                                $feedbackExportButton
                            )
                    ),
                ]
            );
        }

        if (!$this->VotingDisabled) {
            // Configure the Voting Gridfield for export
            $votingExportButton = new GridFieldExportButton();
            $votingExportButton->setExportColumns(['ID', 'Created', 'IP', 'Value']);

            $fields->addFieldsToTab(
                'Root.Votes',
                [
                    GridField::create(
                        'Vote',
                        'Vote',
                        $this->Votes(),
                        GridFieldConfig_Base::create()
                            ->addComponents(
                                $votingExportButton
                            )
                    ),
                ]
            );

            $fields->addFieldsToTab(
                'Root.Main',
                [
                    ReadonlyField::create(null, 'Total Votes', $this->Votes()->count()),
                    ReadonlyField::Create(null, 'Average Vote', $this->averageVote()),
                ]
            );
        }

        if ($this->ID) {
            $fields->addFieldToTab(
                'Root.Main',
                ReadonlyField::create(null, 'Shortcode', $this->Shortcode())
            );
        }

        return $fields;
    }

    public function canCreate($member = null)
    {
        return Permission::checkMember($member, ['EDIT_FLOWCHART']);
    }

    public function canEdit($member = null)
    {
        return Permission::checkMember($member, ['EDIT_FLOWCHART']);
    }

    public function Shortcode()
    {
        return '[flowchart id="' . $this->ID . '"]';
    }

    public function averageVote()
    {
        $total = 0;

        foreach ($this->Votes() as $vote) {
            $total += $vote->Value;
        }

        return $total / $this->Votes()->count();
    }

    public static function shortcodeHandler($arguments, $content = null, $parser = null, $tagName)
    {
        if (!isset($arguments['id'])) {
            return null;
        }

        $flowchart = Flowchart::get()->filter('ID', $arguments['id'])->first();

        if (!$flowchart) {
            return null;
        }

        Requirements::css('silverstripe-flowchart/css/flowchart.css');

        Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.min.js');
        Requirements::javascript('silverstripe-flowchart/javascript/flowchart.js');

        // Security tokens for form
        $securityToken = new SecurityToken('Flowchart_' . $flowchart->ID);

        return SSViewer::execute_template(
            'Flowchart',
            ArrayData::create([
                'Flowchart' => $flowchart,
                'SecurityToken' => $securityToken->getValue(),
            ])
        );
    }
}
