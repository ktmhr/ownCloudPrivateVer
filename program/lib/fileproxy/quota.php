<?php

/**
* ownCloud
*
* @author Robin Appelman
* @copyright 2011 Robin Appelman icewind1991@gmail.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

/**
 * user quota managment
 */

class OC_FileProxy_Quota extends OC_FileProxy{
	private function getFreeSpace(){
		$usedSpace=OC_Filesystem::filesize('');
		$totalSpace=OC_Preferences::getValue(OC_User::getUser(),'files','quota',0);
		if($totalSpace==0){
			return 0;
		}
		return $totalSpace-$usedSpace;
	}
	
	public function postFree_space($path,$space){
		$free=$this->getFreeSpace();
		if($free==0){
			return $space;
		}
		return min($free,$space);
	}

	public function preFile_put_contents($path,$data){
		if (is_resource($data)) {
			$data = '';//TODO: find a way to get the length of the stream without emptying it
		}
		return (strlen($data)<$this->getFreeSpace() or $this->getFreeSpace()==0);
	}

	public function preCopy($path1,$path2){
		return (OC_Filesystem::filesize($path1)<$this->getFreeSpace() or $this->getFreeSpace()==0);
	}

	public function preFromTmpFile($tmpfile,$path){
		return (filesize($tmpfile)<$this->getFreeSpace() or $this->getFreeSpace()==0);
	}

	public function preFromUploadedFile($tmpfile,$path){
		return (filesize($tmpfile)<$this->getFreeSpace() or $this->getFreeSpace()==0);
	}
}