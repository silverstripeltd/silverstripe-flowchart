<?php

class FlowchartAdmin extends ModelAdmin implements PermissionProvider
{
    public static $managed_models = ['Flowchart'];

    public static $url_segment = 'flowcharts';

    public static $menu_title = 'Flowcharts';

    public function providePermissions()
    {
        return array(
            'VIEW_FLOWCHART' => array(
                'name' => _t('Comment.PERMISSION_CREATE_DESCRIPTION', 'Can view and export Flowcharts'),
                'category' => _t('Permissions.CONTENT_CATEGORY', 'Content Permissions'),
                'help' => _t(
                    'Comment.PERMISSION_CREATE_HELP',
                    'Permission required to view and export Flowcharts from the CMS.'
                )
            ),
            'EDIT_FLOWCHART' => array(
                'name' => _t('Comment.PERMISSION_CREATE_DESCRIPTION', 'Can edit and create Flowcharts'),
                'category' => _t('Permissions.CONTENT_CATEGORY', 'Content Permissions'),
                'help' => _t(
                    'Comment.PERMISSION_CREATE_HELP',
                    'Permission required to edit and create Flowcharts from the CMS.'
                )
            ),
        );
    }

    public function canView($member = null)
    {
        return Permission::checkMember($member, ['VIEW_FLOWCHART']);
    }

    public function canCreate($member = null)
    {
        return Permission::checkMember($member, ['EDIT_FLOWCHART']);
    }

    public function canEdit($member = null)
    {
        return Permission::checkMember($member, ['EDIT_FLOWCHART']);
    }
}
