<?php

class fbactivity_model extends CI_Model {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
		$this->load->database();
		$this->load->helper("fbactivity");
	}

	function list_message() {
		try {
			$sql = "SELECT id, fb_userid, fb_username, fb_email, fb_userpic, message, like_num, share_num, lastmod FROM messages WHERE 1 ORDER BY lastmod DESC";
			$stmt = $this->db->conn_id->prepare($sql);
			$output_arr = array();
			if($stmt->execute()) {
				while($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
					array_push($output_arr, $record);
				}
				return $output_arr;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			// PDO Exection...
			return false;
		} catch (Exception $e) {
			// Other Exection...
			return false;
		}
	}

	function get_message($pid) {
		try {
			$sql = "SELECT `id`, `fb_userid`, `fb_username`, `fb_email`, `fb_userpic`, `message`, `like_num`, `share_num`, `lastmod` FROM `messages` WHERE id=?";
			$stmt = $this->db->conn_id->prepare($sql);

			$stmt->bindValue(1, $pid);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(count($rows)>0) {
				$ctime = time() - strtotime($rows[0]["lastmod"]);
				$ctime = fb_time_transform($ctime);
				if(file_exists('/var/www/html/aumight/uploads/' . $pid . ".jpg")) {
					$rows[0]["img"] = 1;
				} else {
					$rows[0]["img"] = 0;
				}
				$rows[0]["lastmod"] = $ctime;
				$rows[0]["ctime"] = $ctime;
				return $rows;
			} else {
				return array();
			}
		} catch (PDOException $e) {
			// PDO Exection...
			return false;
		} catch (Exception $e) {
			// Other Exection...
			return false;
		}
	}

	function insert_message($user) {
		try {
			$message = $this->input->post("dComment");
			if($user === false) {
				return 1;
			} else if($message === false) {
				return 2;
			} else {
				$fbuid = $user->asArray()["id"];
				$uname = $user->asArray()["name"];
				$email = $user->asArray()["email"];
				$pic = "https://graph.facebook.com/" . $fbuid . "/picture?type=square";

				$sql = "INSERT INTO `messages`(`fb_userid`, `fb_username`, `fb_email`, `fb_userpic`, `message`, `like_num`, `share_num`) VALUES (?,?,?,?,?,?,?)";
				$stmt = $this->db->conn_id->prepare($sql);
				$this->db->conn_id->beginTransaction();
				$input_array = array($fbuid, $uname, $email, $pic, $message, 0, 0);

				try {
					if($stmt->execute($input_array)) {
						$message_id = $this->db->conn_id->lastInsertId();
						$this->db->conn_id->commit();

						// try to resize and save photos...
						// settings
						$max_file_size = 1024*1024*10; // 200kb
						$valid_exts = array('jpeg', 'jpg');
						// thumbnail sizes
						$max_width = 350;

						if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_FILES['dPhoto'])) {
							if( $_FILES['dPhoto']['size'] < $max_file_size ){
								// get file extension
								$ext = strtolower(pathinfo($_FILES['dPhoto']['name'], PATHINFO_EXTENSION));
								if (in_array($ext, $valid_exts)) {
									/* resize image */
									/* Get original image x y*/
									list($w, $h) = getimagesize($_FILES['dPhoto']['tmp_name']);
									/* calculate new image size with ratio */
									if($w>$max_width) {
										/* new file name */
										if(!file_exists(BASEPATH . "../uploads/fbactivity/")) {
											mkdir(BASEPATH . "../uploads/fbactivity/", 0777, true);
										}
										$path = BASEPATH . "../uploads/fbactivity/" . $message_id . ".jpg";
										/* read binary data from image file */
										$imgString = file_get_contents($_FILES['dPhoto']['tmp_name']);
										/* create image from string */
										$image = imagecreatefromstring($imgString);
										$tmp = imagecreatetruecolor($max_width, $h*$max_width/$w);
										imagecopyresampled($tmp, $image,
												0, 0,
												0, 0,
												$max_width, $h*$max_width/$w,
												$w, $h);
										/* Save image */
										imagejpeg($tmp, $path, 100);

										/* cleanup memory */
										imagedestroy($image);
										imagedestroy($tmp);
									} else {
										if(move_uploaded_file($_FILES["dPhoto"]["tmp_name"], '/var/www/html/aumight/uploads/' . $message_id . ".jpg")) {
										}
									}
								} else {
									$msg = 'Unsupported file';
								}
							} else{
								$msg = 'Please upload image smaller than 200KB';
							}
						}
						return 0;
					} else {
						$this->db->conn_id->rollback();
						return 97;
					}
				} catch(PDOException $e) {
					$this->db->conn_id->rollback();
					return 96;
				} catch(Exception $e) {
					$this->db->conn_id->rollback();
					return 97;
				}
			}
		} catch(PDOException $e) {
			return 98;
		} catch(Exception $e) {
			return 99;
		}
	}

	public function like_message($pid = -1, $user) {
		try {
			if(!is_numeric($pid) || trim($pid) == "" || $pid < 0) {
				return -3;
			}
			if($user === false) {
				return -1;
			} else {
				$fbuid = $user->asArray()["id"];
				$uname = $user->asArray()["name"];
				$email = $user->asArray()["email"];

				$stmt = $this->db->conn_id->prepare("INSERT INTO `post_likes`(`fb_userid`, `message_id`) VALUES (?,?)");
				$stmt1 = $this->db->conn_id->prepare("UPDATE messages SET like_num=like_num+1 WHERE id=?");
				$stmt2 = $this->db->conn_id->prepare("SELECT like_num FROM messages WHERE id=?");

				$this->db->conn_id->beginTransaction();
				try {
					$input_array = array($fbuid, $pid);
					if($stmt->execute($input_array)) {
						$input_array = array($pid);
						$stmt1->execute($input_array);
						$stmt2->execute($input_array);
						$this->db->conn_id->commit();
						$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							echo $rows[0]["like_num"];
						} else {
							echo "-1";
						}
					} else {
						echo "-2";
					}
				} catch(PDOException $e) {
					$this->db->conn_id->rollback();
					return -98;
				} catch(Exception $e) {
					$this->db->conn_id->rollback();
					return -97;
				}
			}
		} catch(Exception $e) {
			return -99;
		}
	}

	public function share_message($pid = -1, $fbpid = -1) {
		try {
			if(!is_numeric($pid) || trim($pid) == "" || $pid < 0) {
				return -3;
			}
			if(trim($fbpid) == "") {
				return -1;
			} else {
				$stmt  = $this->db->conn_id->prepare("INSERT INTO `share`(`message_id`, `fb_postid`) VALUES (?,?)");
				$stmt1 = $this->db->conn_id->prepare("UPDATE messages SET share_num=share_num+1 WHERE id=?");
				$stmt2 = $this->db->conn_id->prepare("SELECT share_num FROM messages WHERE id=?");


				$this->db->conn_id->beginTransaction();
				try {
					$input_array = array($pid, $fbpid);
					if($stmt->execute($input_array)) {
						$input_array = array($pid);
						$stmt1->execute($input_array);
						$stmt2->execute($input_array);
						$this->db->conn_id->commit();
						$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
						if(count($rows)>0) {
							echo $rows[0]["share_num"];
						} else {
							echo "-1";
						}
					} else {
						echo "-2";
					}
				} catch(PDOException $e) {
					$this->db->conn_id->rollback();
					return -98;
				} catch(Exception $e) {
					$this->db->conn_id->rollback();
					return -97;
				}
			}
		} catch(Exception $e) {
			return -99;
		}
	}
}

