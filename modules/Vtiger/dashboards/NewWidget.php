<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Vtiger_NewWidget_Dashboard extends Vtiger_IndexAjax_View {

    function getSearchParams($value, $assignedto, $dates) {
        $listSearchParams = array();
        $conditions = array(array('leadsource', 'e', $value));
        if ($assignedto != '')
            array_push($conditions, array('assigned_user_id', 'e', getUserFullName($assignedto)));
        if (!empty($dates)) {
            array_push($conditions, array('createdtime', 'bw', $dates['start'] . ' 00:00:00,' . $dates['end'] . ' 23:59:59'));
        }
        $listSearchParams[] = $conditions;
        return '&search_params=' . json_encode($listSearchParams);
    }

    public function process(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);

        $moduleName = $request->getModule();
        $linkId = $request->get('linkid');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        $createdTime = $request->get('createdtime');
		
        //Date conversion from user to database format
        if (!empty($createdTime)) {
            $dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['start']);
            $dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['end']);
        }

        $accessibleUsers = $currentUser->getAccessibleUsersForModule('Home');
        $viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
        $viewer->assign('CURRENT_USER', $currentUser);
        $viewer->assign('WIDGET', $widget);

        $content = $request->get('content');
        if (!empty($content)) {
            $viewer->view('dashboards/NewWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/NewWidget.tpl', $moduleName);
        }
    }

}