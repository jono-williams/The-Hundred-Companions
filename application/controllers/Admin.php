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
					$characterFound = $this->db->query("SELECT * FROM wow_characters WHERE name = '{$name[0]}' AND realm = '{$this->db->escape_str($name[1])}'")->row();
					if($characterFound) {
						$pw[$key] = array(
							'user_id' => $characterFound->user_id,
							'timestamp' => $imp['Timestamp'],
							'points' => $imp['Points'],
							'completed' => 1,
							'eventId' => $aId,
							'character_name' => $name[0],
							'character_realm' => $name[1],
						);
					} else {
						$pw[$key] = array(
							'user_id' => 0,
							'timestamp' => $imp['Timestamp'],
							'points' => $imp['Points'],
							'completed' => 1,
							'eventId' => $aId,
							'character_name' => $name[0],
							'character_realm' => $name[1],
						);
					}

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
			$points = number_format($this->db->query("SELECT SUM(joined_events.points) as points FROM joined_events WHERE user_id = {$value->Id}")->row()->points, 0);
			@$users[$key]->points = $points;
		}
		$data['users'] = $users;
		$this->load->template('points', $data);
	}

	public function usersPointsCharacters($userId) {
		$data['characters'] = $this->db->query("SELECT * FROM wow_characters JOIN roster ON roster.name = wow_characters.name AND REPLACE(roster.realm, ' ', '') = REPLACE(wow_characters.realm, ' ', '') WHERE wow_characters.user_id = {$userId}")->result();
		$data['joined_events'] = $this->db->query("SELECT joined_events.character_name as name, joined_events.character_realm as realm, joined_events.points, joined_events.timestamp, activities.name as aName FROM wow_characters JOIN roster ON roster.name = wow_characters.name JOIN joined_events ON roster.name = joined_events.character_name AND REPLACE(roster.realm, ' ', '') = REPLACE(wow_characters.realm, ' ', '') JOIN activities ON activities.Id = joined_events.eventId AND REPLACE(roster.realm, ' ', '') = REPLACE(wow_characters.realm, ' ', '') WHERE wow_characters.user_id = {$userId}")->result();
		$this->load->template('userRoster', $data);
	}

	public function fixUsers() {
		$data['users'] = $this->db->query("SELECT * FROM users")->result();
		if(@$this->input->post('user_id') && @$this->input->post('name') && @$this->input->post('realm')) {
			$this->db->query("UPDATE joined_events SET user_id = {$this->input->post('user_id')} WHERE character_name = '{$this->db->escape_str($this->input->post('name'))}' AND character_realm = '{$this->db->escape_str($this->input->post('realm'))}'");
		}
		$this->load->template('fixUsers', $data);
	}

	public function manage_team()
	{
		$data['teams'] = $this->db->query("SELECT * FROM teams")->result();

		if(@$_POST['team']) {
			$data['teamDetails'] = $this->db->query("SELECT team_members.Id, wow_characters.name as character_name, wow_characters.realm as character_realm, team_members.isLeader FROM team_members JOIN wow_characters ON wow_characters.user_id = team_members.user_id AND wow_characters.main_character = 1 WHERE team_members.team_id = {$_POST['team']}")->result();
		}
		$this->load->template('manage_teams', $data);
	}

	public function manage_teams()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('teams');
		$crud->set_relation('role','discord_roles','name');
		$crud->set_relation('leaderRole','discord_roles','name');
		$this->load->template('crud', (array)$crud->render());
	}

	public function manual_manage_teams()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('team_members');
		$crud->set_relation('team_id','teams','name');
		$crud->set_relation('user_id','users','name');
		$this->load->template('crud', (array)$crud->render());
	}

	public function team_questions()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('team_questions');
		$crud->set_relation('team_id','teams','name');
		$this->load->template('crud', (array)$crud->render());
	}

	public function team_app_answers()
	{
		$data['applications'] = $this->db->query("SELECT team_applicants.*, teams.name as tName FROM team_applicants JOIN teams ON team_applicants.team_id = teams.Id")->result();

		if(@$_POST['application']) {
			$data['search'] = explode(',', $_POST['application']);
			$data['application_questions'] = $this->db->query("SELECT * FROM team_app_answers WHERE team_id = {$data['search'][1]} AND user_id = {$data['search'][2]}")->result();
		}
		$this->load->template('applications', $data);
	}

	public function declineApp($id) {
		$details = $this->db->query("SELECT * FROM team_applicants WHERE Id = {$id}")->row();
		$this->db->where('team_id', $details->team_id);
		$this->db->where('user_id', $details->user_id);
		$this->db->delete('team_app_answers');
		$this->db->where('Id', $id);
		$this->db->delete('team_applicants');
		redirect('admin/team_app_answers');
	}

	public function acceptApp($id) {
		$details = $this->db->query("SELECT * FROM team_applicants WHERE Id = {$id}")->row();
		$import = array(
			'team_id' => $details->team_id,
			'user_id' => $details->user_id,
			'isLeader' => 0,
		);
		$this->db->insert('team_members', $import);
		$this->db->where('team_id', $details->team_id);
		$this->db->where('user_id', $details->user_id);
		$this->db->delete('team_app_answers');
		$this->db->where('Id', $id);
		$this->db->delete('team_applicants');
		redirect('admin/team_app_answers');
	}

	public function teamRemoveMember($id) {
		$this->db->where('Id', $id);
		$this->db->delete('team_members');
		redirect('admin/manage_team');
	}

}
