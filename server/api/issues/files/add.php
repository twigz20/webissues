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

class Server_Api_Issues_Files_Add
{
    public $access = '*';

    public $params = array(
        'issueId' => array( 'type' => 'int', 'required' => true ),
        'name' => array( 'type' => 'string', 'required' => true ),
        'description' => 'string',
        'attachment' => array( 'type' => 'file', 'required' => true )
    );

    public function run( $issueId, $name, $description, $attachment )
    {
        $serverManager = new System_Api_ServerManager();
        $maxLength = $serverManager->getSetting( 'file_max_size' );

        if ( $attachment->getSize() > $maxLength )
            throw new Server_Error( Server_Error::UploadError );

        $issueManager = new System_Api_IssueManager();
        $issue = $issueManager->getIssue( $issueId );

        $parser = new System_Api_Parser();

        $name = $parser->normalizeString( $name, System_Const::FileNameMaxLength );
        $description = $parser->normalizeString( $description, System_Const::DescriptionMaxLength, System_Api_Parser::AllowEmpty );

        $stampId = $issueManager->addFile( $issue, $attachment, $name, $description );

        $result[ 'stampId' ] = $stampId;

        return $result;
    }
}

System_Bootstrap::run( 'Server_Api_Application', 'Server_Api_Issues_Files_Add' );
