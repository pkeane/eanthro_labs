<?php

class Dase_Handler_Photos extends Dase_Handler
{
		public $resource_map = array(
				'uploader' => 'uploader',
				'contribute' => 'contribute',
				'{name}' => 'photo',
				'thumb/{name}' => 'thumbnail',
		);

		public function deletePhoto($r) 
		{ 
				$user = $r->getUser();
				$photo = new Dase_DBO_Photo($this->db);
				if (!$photo->load($r->get('name'))) {
						$r->renderError(404,'no such photo');
				}
				if ($photo->created_by != $user->eid) {
						$r->renderError(401,'unauthorized');
				}
				$base_dir = $this->config->getMediaDir().'/photos';
				$file_path = $base_dir.'/'.$photo->name;
				@unlink($file_path);
				$photo->delete();
				$r->renderResponse('deleted photo');
		}

		public function getPhoto($r) 
		{ 
				$media_dir = $this->config->getMediaDir().'/photos/';
				$file_path = $media_dir.'/'.$r->get('name').'.'.$r->getFormat();
				$r->serveFile($file_path,$r->response_mime_type);
		}

		public function getThumbnail($r) 
		{ 
				$media_dir = $this->config->getMediaDir().'/photos/';
				$file_path = $media_dir.'/thumb/'.$r->get('name').'.'.$r->getFormat();
				$r->serveFile($file_path,$r->response_mime_type);
		}

		public function getPhotoJpg($r) { return $this->getPhoto($r); }
		public function getPhotoGif($r) { return $this->getPhoto($r); }
		public function getPhotoPng($r) { return $this->getPhoto($r); }

		public function getThumbnailJpg($r) { return $this->getThumbnail($r); }
		public function getThumbnailGif($r) { return $this->getThumbnail($r); }
		public function getThumbnailPng($r) { return $this->getThumbnail($r); }

		public function getContribute($r) 
		{
				$t = new Dase_Template($r);
				$photos = new Dase_DBO_Photo($this->db);
				$photos->created_by = $r->getUser()->eid;
				$photos->orderBy('created DESC');
				$t->assign('photos',$photos->findAll(1));
				$r->renderResponse($t->fetch('contribute.tpl'));
		}

		public function postToUploader($r)
		{
				$file = $r->_files['uploaded_file'];
				if ($file && is_file($file['tmp_name'])) {

						$photo = new Dase_DBO_Photo($this->db);
						$photo->body = $r->get('body');
						$photo->title = $r->get('title');
						$name = $file['name'];
						$path = $file['tmp_name'];
						$type = $file['type'];
						if (!is_uploaded_file($path)) {
								$r->renderError(400,'no go upload');
						}
						if (!isset(Dase_File::$types_map[$type])) {
								$r->renderError(415,'unsupported media type: '.$type);
						}

						$parts = explode('/',$type);

						if (isset($parts[0]) && 'image' != $parts[0]) {
								//really need some redirect + error message here 
								$r->renderError(415,'unsupported media type: '.$type);
						}

						$base_dir = $this->config->getMediaDir().'/photos';
						$thumb_dir = $this->config->getMediaDir().'/photos/thumb';

						if (!file_exists($base_dir) || !is_writeable($base_dir)) {
								$r->renderError(403,'media directory not writeable: '.$base_dir);
						}

						if (!file_exists($thumb_dir) || !is_writeable($thumb_dir)) {
								$r->renderError(403,'thumbnail directory not writeable: '.$thumb_dir);
						}

						$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
						$basename = Dase_Util::dirify(pathinfo($name,PATHINFO_FILENAME));

						$newname = $this->_findNextUnique($base_dir,$basename,$ext);
						$new_path = $base_dir.'/'.$newname;
						//move file to new home
						rename($path,$new_path);
						chmod($new_path,0775);
						$size = @getimagesize($new_path);

						$photo->name = $newname;
						if (!$photo->title) {
								$photo->title = $photo->name;
						}
						$photo->file_url = 'photos/'.$photo->name;
						$photo->filesize = filesize($new_path);
						$photo->mime = $type;

						$thumb_path = $thumb_dir.'/'.$newname;
						$thumb_path = str_replace('.'.$ext,'.jpg',$thumb_path);
						//$command = CONVERT." \"$new_path\" -format jpeg -resize '100x100 >' -colorspace RGB $thumb_path";
						//$command = CONVERT." \"$new_path\" -format jpeg -resize '100x100^ -gravity center -extent 100x100' -colorspace RGB $thumb_path";
						$command = CONVERT." \"$new_path\" -format jpeg -resize '172x172^' -gravity center -extent 172x172 -colorspace RGB $thumb_path";
						$exec_output = array();
						$results = exec($command,$exec_output);
						if (!file_exists($thumb_path)) {
								//Dase_Log::info(LOG_FILE,"failed to write $thumb_path");
						}
						chmod($thumb_path,0775);
						$newname = str_replace('.'.$ext,'.jpg',$newname);
						$photo->thumbnail_url = 'photos/thumb/'.$newname;
						$photo->width = $size[0];
						$photo->height = $size[1];
						$photo->created_by = $r->getUser()->eid;
						$photo->created = date(DATE_ATOM);
						$photo->insert();
				}

				$r->renderRedirect('trackways/share_photos');
		}

		private function _findNextUnique($base_dir,$basename,$ext,$iter=0)
		{
				if ($iter) {
						$checkname = $basename.'_'.$iter.'.'.$ext;
				} else {
						$checkname = $basename.'.'.$ext;
				}
				if (!file_exists($base_dir.'/'.$checkname)) {
						return $checkname;
				} else {
						$iter++;
						return $this->_findNextUnique($base_dir,$basename,$ext,$iter);
				}

		}

		private function _findUniqueName($name,$iter=0)
		{
				if ($iter) {
						$checkname = $name.'_'.$iter;
				} else {
						$checkname = $name;
				}
				$item = new Dase_DBO_Item($this->db);
				$item->name = $checkname;
				if (!$item->findOne()) {
						return $checkname;
				} else {
						$iter++;
						return $this->_findUniqueName($name,$iter);
				}
		}
}
