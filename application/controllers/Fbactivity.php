<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fbactivity extends CI_Controller {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('facebook');
	}

	private function no_login() {
		$data["message"] = "請先登入Facebook";
		$this->load->view("fbactivity/post_result", $data);
	}

	public function index() {
		$data['fb_appid'] = $this->config->item('fb_app_id');
		if($this->facebook->is_login()) {
			$this->load->view('fbactivity/header.php', $data);
			if(($user = $this->facebook->profile()) !== false) {

				$data["user_name"] = $user->asArray()["name"];
				$data["user_img"] = "https://graph.facebook.com/" . $user->asArray()["id"] . "/picture?type=square";
				$this->load->view('fbactivity/islogin.php', $data);
				$data["rows"] = json_decode($this->list(), true);
				$this->load->view('fbactivity/list.php', $data);
				$this->load->view('fbactivity/footer.php');
			} else {
				$this->no_login();
			}
		} else {
			$this->load->view('fbactivity/header.php', data);
			$this->load->view('fbactivity/login.php', data);
			$data["rows"] = json_decode($this->list(), true);
			$this->load->view('fbactivity/list.php', $data);
			$this->load->view('fbactivity/footer.php');
		}
	}

	private function list() {
		try {
			$this->load->model("Fbactivity_model");
			if(($result = $this->Fbactivity_model->list_message()) !== false) {
				return json_encode($result);
			} else {
				return "[]";
			}
		} catch (PDOException $e) {
			// PDO Exection...
			//print_r($e);
			return "[]";
		} catch (Exception $e) {
			// Other Exection...
			//print_r($e);
			return "[]";
		}
	}

	public function article($pid=null) {
		if(is_null($pid)) {
			redirect("/fbactivity");
		} else {
			if(preg_match("/^p(\d+)\.html$/", $pid, $matches)) {
				$pid = $matches[1];
				$this->load->model("Fbactivity_model");
				$data['fb_appid'] = $this->config->item('fb_app_id');
				if($this->facebook->is_login()) {
					$this->load->view('fbactivity/header.php', $data);
					if(($user = $this->facebook->profile()) !== false) {
						$data["user_name"] = $user->asArray()["name"];
						$data["user_img"] = "https://graph.facebook.com/" . $user->asArray()["id"] . "/picture?type=square";
						$this->load->view('fbactivity/islogin.php', $data);
						$data = $this->Fbactivity_model->get_message($pid);
						$data = $data[0];
						$this->load->view('fbactivity/article.php', $data);
						$this->load->view('fbactivity/footer.php');
					} else {
						$this->no_login();
					}
				} else {
					$this->load->view('fbactivity/header.php', $data);
					$this->load->view('fbactivity/login.php', $data);
					$data = $this->Fbactivity_model->get_message($pid);
					$data = $data[0];
					$this->load->view('fbactivity/article.php', $data);
					$this->load->view('fbactivity/footer.php');
				}
			} else {
				redirect("/fbactivity");
			}
		}
	}

	public function pdata($pid) {
		try {
			$this->load->model("Fbactivity_model");
			if(($result = $this->Fbactivity_model->get_message($pid)) !== false) {
				echo json_encode($result);
			} else {
				echo "[]";
			}
		} catch (PDOException $e) {
			// PDO Exection...
		} catch (Exception $e) {
			// Other Exection...
		}
	}

	public function post() {
		if($this->facebook->is_login()) {
			if(($user = $this->facebook->profile()) !== false) {
				try {
					$this->load->model("Fbactivity_model");
					$result = $this->Fbactivity_model->insert_message($user);
				} catch (PDOException $e) {
					// PDO Exection...
					//print_r($e);
					
				} catch (Exception $e) {
					// Other Exection...
					//print_r($e);
				}
				if($result == 0) {
					$data["message"] = "留言成功!";
				} else {
					$data["message"] = "留言失敗，請重新登入Facebook";
				}
				$this->load->view("fbactivity/post_result", $data);
			} else {
				$this->no_login();
			}
		} else {
			$this->no_login();
		}
	}

	public function like() {
		if($this->facebook->is_login()) {
			if(($user = $this->facebook->profile()) !== false) {
				try {
					$pid = $this->input->post("pid");
					$this->load->model("Fbactivity_model");
					$result = $this->Fbactivity_model->like_message($pid, $user);
					echo $result;
				} catch (PDOException $e) {
					// PDO Exection...
					//print_r($e);
					echo "-94";
				} catch (Exception $e) {
					// Other Exection...
					//print_r($e);
					echo "-95";
				}
			} else {
				echo "-92";
			}
		} else {
			echo "-93";
		}
	}

	public function share() {
		if($this->facebook->is_login()) {
			if(($user = $this->facebook->profile()) !== false) {
				try {
					$pid = $this->input->post("pid");
					$fbpid = $this->input->post("fbPostID");
					$this->load->model("Fbactivity_model");
					$result = $this->Fbactivity_model->share_message($pid, $fbpid);
					echo $result;
				} catch (PDOException $e) {
					// PDO Exection...
					//print_r($e);
					echo "-94";
				} catch (Exception $e) {
					// Other Exection...
					//print_r($e);
					echo "-95";
				}
			} else {
				echo "-92";
			}
		} else {
			echo "-93";
		}
	}

	public function login() {
		if($this->facebook->is_login()) {
			redirect(base_url('/fbactivity'), 'refresh');
		} else {
			$data["message"] = "登入Facebook失敗，請稍候再試！";
			$this->load->view("fbactivity/post_result", $data);
		}
	}

	public function photo($id) {
		if(isset($id) && intval($id) > 0) {
			$id = intval($id);
			$full_path = BASEPATH . "../uploads/fbactivity/" . $id . ".jpg";
			if(file_exists($full_path)) {
				if(!isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) || strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])<filemtime($full_path)) {
					header("Content-Type: image/jpeg");
					header("Content-Length: " . filesize($full_path));
					header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($full_path)).' GMT', true, 200);

					// dump the picture and stop the script
					$fp = fopen($full_path, 'rb');
					fpassthru($fp);
					fclose($fp);
					return;
				} else {
					http_response_code(304);
					return;
				}
			} else {
				http_response_code(404);
				echo 'Not Found.';
				return;
			}
		} else {
			http_response_code(404);
			echo 'Not Found.';
			return;
		}
	}
}
