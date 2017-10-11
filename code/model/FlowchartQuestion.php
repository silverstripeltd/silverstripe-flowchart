<?php

class FlowchartQuestion extends DataObject
{
    /**
     * @var array
     * @config
     */
    private static $db = [
        'Content' => 'HTMLText',
        'Info' => 'HTMLText',
        'Answer' => 'HTMLText',
        'QuestionHeading' => 'Varchar(255)', // The question heading, e.g. Question
        'AnswerHeading' => 'Varchar(255)', // The answer heading, e.g. Final answer
        'AnswerImageAfterContent' => 'Boolean'
    ];

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'Flowchart' => 'Flowchart',
        'AnswerImage' => 'Image'
    ];

    /**
     * @var array
     * @config
     */
    private static $many_many = [
        'Responses' => 'FlowchartResponse'
    ];

    /**
     * @var array
     * @config
     */
    private static $summary_fields = [
        'ID' => 'ID',
        'ContentSummary' => 'Question summary',
        'InfoSummary' => 'Description summary',
        'AnswerHeading' => 'Answer heading',
        'AnswerSummary' => 'Answer summary'
    ];

    /**
     * @var string
     * @config
     */
    private static $flowcharts_asset_folder = 'flowcharts';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'FlowchartID',
            'QuestionHeading',
            'Content',
            'Info',
            'AnswerHeading',
            'AnswerImage',
            'AnswerImageAfterContent',
            'Answer'
        ]);

        // Question heading
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create('QuestionHeading', 'Question heading')
                ->setDescription('Optional, question heading, defaults to `Question` if left blank.')
        );

        // Question content
        $fields->addFieldToTab(
            'Root.Main',
            HTMLEditorField::create('Content', 'Question content')
                ->setRows(15)
                ->setDescription('The question content body')
        );

        // Info content
        $fields->addFieldToTab(
            'Root.Main',
            HTMLEditorField::create('Info', 'Question description content')
                ->setRows(15)
                ->setDescription(
                    'More information about the question, revealed via the question mark icon in the question heading.'
                )
        );

        // Answer heading
        $fields->addFieldToTab(
            'Root.Answer',
            TextField::create('AnswerHeading', 'Answer heading')
                ->setDescription('Optional, answer heading, e.g. `Final answer`, defaults to `Answer` if left blank.')
        );

        // Answer Image
        $featureImage = new UploadField('AnswerImage', 'Answer image');
        $featureImage->setAllowedFileCategories('image');
        $featureImage->setDescription(
            'Optional, image to display above the answer content. Accepted image-types: jpg, png, gif.'
        );
        $featureImage->setAllowedMaxFileNumber(1);
        $featureImage->setFolderName($this->FolderName());
        $fields->addFieldToTab('Root.Answer', $featureImage);

        $fields->addFieldToTab(
            'Root.Answer',
            CheckboxField::create('AnswerImageAfterContent', 'Show image after content?')
        );

        // Answer content
        $fields->addFieldToTab(
            'Root.Answer',
            HTMLEditorField::create('Answer', 'Answer content')
                ->setRows(15)
                ->setDescription('Interim or final answer content')
        );

        return $fields;
    }

    /**
     * @return String The Title based on the Question
     */
    public function Title()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        return sprintf('%d - %s', $this->ID, $this->ContentSummary());
    }

    /**
     * @return String The Title based on the Question
     */
    public function ContentSummary()
    {
        return strip_tags($this->Content);
    }

    /**
     * @return String The Title based on the Question
     */
    public function InfoSummary()
    {
        return strip_tags($this->Info);
    }

    /**
     * @return String The Title based on the Question
     */
    public function AnswerSummary()
    {
        return strip_tags($this->Answer);
    }

    /**
     * @return String The assets folder name for FeatureImages
     */
    public function FolderName()
    {
        return static::$flowcharts_asset_folder;
    }

    /**
     * Get a link to the parent flowchart page.
     * This is required by SOLR so that FlowStates can be clicked on in search results.
     *
     * @return string
     */
    public function Link($action = 'show')
    {
        if ($this->FlowchartID && $this->Flowchart()->getCurrentPage()) {
            return $this->Flowchart()->getCurrentPage()->Link();
        }
        return null;
    }

    /**
     * Returns a comma separated string of field names that are searchable {@link getSearchResults()}
     *
     * @return string
     */
    protected function getSearchableFields()
    {
        return implode(self::$searchable_fields, ',');
    }

    /**
     * Returns the FlowState objects that match the search query, using a boolean mode fulltext search
     *
     * @param string $searchQuery
     */
    public function getSearchResults($searchQuery)
    {
        return DataObject::get(
            "FlowchartQuestion",
            "MATCH (". $this->getSearchableFields() .") AGAINST ('". $searchQuery ."' IN BOOLEAN MODE)"
        );
    }

    /**
     * Returns a custom SearchContext that matches search queries with filters on the searchable fields in this object
     *
     * @TODO make this actually search
     * @return SearchContext
     */
    public function getCustomSearchContext()
    {
        $fields = new FieldList(self::$searchable_fields);
        $filters = array(
            'Content' => new PartialMatchFilter('Content'),
            'Info' => new PartialMatchFilter('Info'),
            'Answer' => new PartialMatchFilter('Answer')
        );
        return new SearchContext($this->class, $fields, $filters);
    }

    public function canView($member = null)
    {
        return (Permission::checkMember($member, array('VIEW_FLOWCHART')));
    }

    public function canEdit($member = null)
    {
        return (Permission::checkMember($member, array('VIEW_FLOWCHART')));
    }

    public function hasAnswer()
    {
        return !empty($this->Answer);
    }
}
