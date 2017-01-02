<?php
/*
  RoxyFileman - web based file manager. Ready to use with CKEditor, TinyMCE. 
  Can be easily integrated with any other WYSIWYG editor or CMS.

  Copyright (C) 2013, RoxyFileman.com - Lyubomir Arsov. All rights reserved.
  For licensing, see LICENSE.txt or http://RoxyFileman.com/license

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  Contact: Lyubomir Arsov, liubo (at) web-lobby.com
*/
function checkAccess($action){
//  if(!session_id())
//    session_start();
  
  $aParse = explode( '?', $_SERVER[ 'HTTP_REFERER' ] );
  
  if( count( $aParse ) > 1 ){
      $query = $aParse[1];
      
      $aQuery = explode("&", $query);
      //var_dump( $aQuery ); die();
      foreach( $aQuery as $val ){
          if( $val != '' ){
            $aVal = explode( '=', $val );

            if( count( $aVal ) && $aVal[0] != "height" ) $_GET[ $aVal[0] ] = $aVal[1];
          }
      }
      
      //var_dump($query);
  }
  
  
  if( isset( $_GET['dir'] ) && $_GET['dir'] != '' ){
      $_SESSION['SESSION_PATH_KEY'] = $_GET['dir'];
  } else if( isset( $_GET['idField'] ) && $_GET['idField'] != '' ){
      $_SESSION['SESSION_PATH_KEY'] = "/custom/upload/" . $_SESSION[ 'classeName' ] . "/" . $_GET['idField'] . "/";
      
      if( !is_dir($_SERVER['DOCUMENT_ROOT'] . "/custom/upload/" . $_SESSION[ 'classeName' ] . "/" . $_GET['idField'] . "/") ){
          mkdir($_SERVER['DOCUMENT_ROOT'] . "/custom/upload/" . $_SESSION[ 'classeName' ] . "/" . $_GET['idField'] . "/");
      }
      
      return ;
      
  }
  else if( isset( $_SESSION[ 'classeName' ] ) ){
      //on redirige vers /custom/upload/className
      $_SESSION['SESSION_PATH_KEY'] = "/custom/upload/contenu/";
      
      if( !is_dir($_SERVER['DOCUMENT_ROOT'] . "/custom/upload/contenu/") ){
          mkdir($_SERVER['DOCUMENT_ROOT'] . "/custom/upload/contenu/");
      }
      
      return ;
  } else {
      //on redirige vers /custom/img/minisite
      if( isset( $_SESSION[ 'site' ] ) ) $_SESSION['SESSION_PATH_KEY'] = "/custom/img/" . $_SESSION[ 'site' ];
      else return false;
  }
}
?>