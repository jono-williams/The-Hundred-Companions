<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
 	{
 		parent::__construct();

		if($this->session->userdata('role')->name != "Owner" && @$this->session->userdata('role')->name != "Moderator" && @$this->session->userdata('role')->name != "Admin") {
			redirect('welcome');
		}

 		$this->load->database();
 		$this->load->helper('url');

 		$this->load->library('grocery_CRUD');
 	}

	public function index()
	{

	}

	public function users()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('users');
		$crud->set_relation('rank_id','rank','name');
		$crud->fields('name', 'email', 'rank_id', 'last_login');
		$crud->columns('name', 'email', 'rank_id', 'last_login');
		$this->load->template('crud', (array)$crud->render());
	}

	public function streamers()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('streamers');
		$this->load->template('crud', (array)$crud->render());
	}

	public function articles()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('articles');
		$crud->set_relation('userId','users','Name');
		$this->load->template('crud', (array)$crud->render());
	}

	public function stats()
	{
		$data['user_count'] = $this->db->query("SELECT COUNT(*) as c FROM users")->row()->c;
		$data['point_count'] = $this->db->query("SELECT SUM(points) as s FROM joined_events")->row()->s;
		$data['activities_count'] = $this->db->query("SELECT COUNT(*) as c FROM activities")->row()->c;
		$data['active_activities_count'] = $this->db->query("SELECT COUNT(*) as c FROM activities WHERE timestamp > CURDATE()")->row()->c;
		$data['latest_users'] = $this->db->query("SELECT * FROM users ORDER BY Id DESC LIMIT 1")->row();
		$this->load->template('stats', $data);
	}

	public function activities()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('activities');
		$crud->columns('Id', 'name', 'points', 'host_id', 'timestamp');
		$crud->set_relation('host_id','users','Name');
		$crud->change_field_type('timestamp','datetime',date('d/m/Y H:i:s'));
		$crud->callback_after_delete(array($this,'delete_activities'));
		$this->load->template('crud', (array)$crud->render());
	}

	public function delete_activities($primary_key)
	{
	    return $this->db->where('eventId', $primary_key)->delete('joined_events');
	}

	public function joined_events()
	{
		$data['events'] = $this->db->query("SELECT * FROM activities")->result();

		if(@$_POST['events']) {
			$data['eventM'] = $this->db->query("SELECT * FROM joined_events WHERE eventId = {$_POST['events']}")->result();
		}
		$this->load->template('jEvent', $data);
	}

	public function activities_import() {
		$this->load->library('Csvreader');
		$data['title'] = "Activities Import";
		$data['importLikeThis'] = "Id, ActivityID, ActivityName, MemberName, Points, Timestamp";
		if($_FILES) {
			$target_dir = BASEPATH . "../uploads/";
			$target_file = $target_dir . basename($_FILES["file"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
			@$result = $this->csvreader->parse_file($target_file);
			if($result) {
				$i = 0;
				foreach ($result as $key => $value) {
					foreach($value as $k => $v) {
						$import[$i][trim($k)] = $v;
					}
					$i++;
				}

				foreach ($import as $key => $imp) {
					if(substr_count($imp['Timestamp'], 'PM') == 1) {
						$imp['Timestamp'] = str_replace('PM', '', $imp['Timestamp']);
					}
					if(substr_count($imp['Timestamp'], 'AM') == 1) {
						$imp['Timestamp'] = str_replace('AM', '', $imp['Timestamp']);
					}
					if(substr_count($imp['Timestamp'], ':') == 2) {
						print_r($imp['Timestamp']);
						$imp['Timestamp'] = DateTime::createFromFormat('d/m/Y H:i:s', trim($imp['Timestamp']))->format('y-m-d H:i:s');
					} else {
						$imp['Timestamp'] = DateTime::createFromFormat('d/m/Y H:i', trim($imp['Timestamp']))->format('y-m-d H:i:s');
					}
					if($key == 0) {
						$import = array(
							'name' => $imp['ActivityName'],
							'host_id' => $this->session->userdata('user')->Id,
							'points' => $imp['Points'],
							'timestamp' => $imp['Timestamp'],
						);
						$this->db->insert('activities', $import);
						$aId = $this->db->insert_id();
					}
					$imp['MemberName'] = $imp['MemberName'];
					$name = explode('-', $imp['MemberName']);
					$pw[$key] = array(
						'timestamp' => $imp['Timestamp'],
						'points' => $imp['Points'],
						'completed' => 1,
						'eventId' => $aId,
						'character_name' => $name[0],
						'character_realm' => $name[1],
					);
				}

				$this->db->insert_batch('joined_events', $pw);
				$this->session->set_flashdata('importResult', 'Import Was Successfully');
			} else {
				$this->session->set_flashdata('importResult', 'Import Failed');
			}
		}
		$this->load->template('import', $data);
	}

	public function user_import() {
		$this->load->library('Csvreader');
		$data['title'] = "Users Import";
		$data['importLikeThis'] = "name, email, password, rank_id";
		if($_FILES) {
			$target_dir = BASEPATH . "../uploads/";
			$target_file = $target_dir . basename($_FILES["file"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
			$result = $this->csvreader->parse_file($target_file);
			if($result) {
				$i = 0;
				foreach ($result as $key => $value) {
					foreach($value as $k => $v) {
						if($k == "password") {
							$import[$i][$k] = password_hash($v, PASSWORD_DEFAULT);
						} elseif ($k == "rank_id") {
							$import[$i][$k] = @$this->db->query("SELECT * FROM rank WHERE name = '{$v}'")->row()->Id;
						} else {
							$import[$i][$k] = $v;
						}
					}
					$i++;
				}
			}
			$this->db->insert_batch('users', $import);
		}
		$this->load->template('import', $data);
	}

	public function roster_import() {
		$this->load->library('Csvreader');
		$data['title'] = "Roster Import";
		$data['importLikeThis'] = "Id, realm, name, race, level, class, zone, role, presence";
		if($_FILES) {
			$target_dir = BASEPATH . "../uploads/";
			$target_file = $target_dir . basename($_FILES["file"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
			$this->db->truncate('roster');
			@$result = $this->csvreader->parse_file($target_file);
			if($result) {
				$i = 0;
				foreach ($result as $key => $value) {
					foreach($value as $k => $v) {
							$import[$i][$k] = $v;
					}
					$i++;
				}

				$member_rank = $this->db->query("SELECT * FROM rank WHERE name = 'Member'")->row()->Id;
				$this->db->set('rank_id', 0);
				$this->db->where('rank_id', $member_rank);
				$this->db->update('users');
				$characters = $this->db->query("SELECT wow_characters.*, users.Id, users.rank_id FROM users JOIN wow_characters ON wow_characters.user_id = users.Id AND wow_characters.main_character = 1 ORDER BY rank_id")->result();
				foreach ($import as $key => $value) {
					$name_found = array_search($value['name'], array_column($characters, 'name'));
					if($name_found !== false) {
						if($characters[$name_found]->realm == $value['realm']) {
							@$member_rank = $this->db->query("SELECT * FROM rank WHERE name = '{$value["role"]}'")->row()->Id;
							$this->db->set('rank_id', $member_rank);
							$this->db->where('Id', $characters[$name_found]->Id);
							$this->db->update('users');
						}
					}
				}


				$query = "LOAD DATA LOCAL INFILE '$target_file'
									INTO TABLE roster
									CHARACTER SET UTF8
									FIELDS TERMINATED BY ','
									ENCLOSED BY '\"'
									LINES TERMINATED BY '\n'
									IGNORE 1 ROWS
									(Id, realm, name, race, level, class, zone, role, presence)";

				$this->db->query($query);
				$this->session->set_flashdata('importResult', 'Import Was Successfully');
			} else {
				$this->session->set_flashdata('importResult', 'Import Failed');
			}
		}
		$this->load->template('import', $data);
	}

	public function usersPoints() {
		$users = $this->db->query("SELECT * FROM users")->result();
		foreach ($users as $key => $value) {
			$characters = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$value->Id}")->result();
			$points = 0;
			foreach ($characters as $k => $value) {
				@$search = $value->name . '-' . addslashes(str_replace(' ', '', $value->realm));
				$points = number_format($points, 0) + number_format($this->db->query("SELECT SUM(joined_events.points) as points FROM joined_events WHERE CONCAT(character_name, '-', REPLACE(character_realm, ' ', '')) = '{$search}'")->row()->points, 0);
			}
			@$users[$key]->points = $points;
		}
		$data['users'] = $users;
		$this->load->template('points', $data);
	}

	public function usersPointsCharacters($userId) {
		$data['characters'] = $this->db->query("SELECT * FROM wow_characters JOIN roster ON roster.name = wow_characters.name AND REPLACE(roster.realm, ' ', '') = REPLACE(wow_characters.realm, ' ', '') WHERE wow_characters.user_id = {$userId}")->result();
		$data['joined_events'] = $this->db->query("SELECT roster.name, roster.realm, joined_events.points, joined_events.timestamp, activities.name as aName FROM wow_characters JOIN roster ON roster.name = wow_characters.name JOIN joined_events ON roster.name = joined_events.character_name AND REPLACE(roster.realm, ' ', '') = REPLACE(wow_characters.realm, ' ', '') JOIN activities ON activities.Id = joined_events.eventId AND REPLACE(roster.realm, ' ', '') = REPLACE(wow_characters.realm, ' ', '') WHERE wow_characters.user_id = {$userId}")->result();
		$this->load->template('userRoster', $data);
	}

}
