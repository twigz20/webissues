<?php
/**************************************************************************
* This file is part of the WebIssues Server program
* Copyright (C) 2006 Michał Męciński
* Copyright (C) 2007-2017 WebIssues Team
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/

require_once( '../../../../system/bootstrap.inc.php' );

class Server_Api_Issues_Description_Edit
{
    public function run( $arguments )
    {
        $principal = System_Api_Principal::getCurrent();
        $principal->checkAuthenticated();

        $issueId = isset( $arguments[ 'issueId' ] ) ? (int)$arguments[ 'issueId' ] : null;
        $descriptionText = isset( $arguments[ 'description' ] ) ? $arguments[ 'description' ] : null;
        $descriptionFormat = isset( $arguments[ 'descriptionFormat' ] ) ? (int)$arguments[ 'descriptionFormat' ] : null;

        if ( $issueId == null || $descriptionText == null )
            throw new Server_Error( Server_Error::InvalidArguments );

        $issueManager = new System_Api_IssueManager();
        $issue = $issueManager->getIssue( $issueId, System_Api_IssueManager::RequireAdministratorOrOwner );

        $description = $issueManager->getDescription( $issue );

        $parser = new System_Api_Parser();
        $serverManager = new System_Api_ServerManager();

        $descriptionText = $parser->normalizeString( $descriptionText, $serverManager->getSetting( 'comment_max_length' ), System_Api_Parser::MultiLine );
        $parser->checkTextFormat( $descriptionFormat );

        $stampId = $issueManager->editDescription( $description, $descriptionText, $descriptionFormat );

        $result[ 'stampId' ] = $stampId;

        return $result;
    }
}

System_Bootstrap::run( 'Server_Api_Application', 'Server_Api_Issues_Description_Edit' );