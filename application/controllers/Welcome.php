<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
	public function index()
	{
		$data['title'] = "Welcome!";
		$this->db->select('articles.*, wow_characters.*, rank.colour');
		$this->db->from('articles');
		$this->db->join('users', 'articles.userId = users.Id');
		$this->db->join('wow_characters', 'wow_characters.user_id = users.Id');
		$this->db->join('rank', 'rank.Id = users.rank_id');
		$this->db->where('wow_characters.main_character', 1);
		$this->db->order_by('articles.date DESC');
		$this->db->limit(6);
		$data['articles'] = $this->db->get()->result();
		$this->load->template('home', $data);
	}

	public function activities() {
		if(!@$this->session->userdata('user')->Id) {
			redirect('auth/login');
		}

		if($this->session->userdata('user')->Id) {
			$this->db->select("GROUP_CONCAT(teams.name) as groupped");
			$this->db->from("team_members");
			$this->db->join("teams", "teams.Id = team_members.team_id");
			$this->db->where("user_id", $this->session->userdata('user')->Id);
			$teams = $this->db->get()->row()->groupped;
			if($teams) {
				$teams = implode(',', array_unique(explode(',', $teams)));
			}
		}

		$data['title'] = "Activities";
		$this->db->select('activities.*, users.name as hostName, IF(activities.timestamp >= CURDATE(), 1, 0) as active');
		$this->db->from('activities');
		$this->db->join('users', 'activities.host_id = users.Id');
		$this->db->where("(activities.certain_roles = '' OR FIND_IN_SET('{$teams}', activities.certain_roles))", false, false);

		if($this->session->userdata('user')->Id) {
			$this->db->select('joined_events.eventId as joined_events');
			$this->db->join('joined_events', 'joined_events.eventId = activities.Id AND joined_events.user_id = '.$this->session->userdata('user')->Id.'', 'left');
		}

		$this->db->order_by('activities.timestamp DESC');
		$data['activities'] = $this->db->get()->result();
		$this->load->template('activities/list', $data);
	}

	public function characters() {
		$this->db->select('wow_characters.*');
		$this->db->from('wow_characters');
		$this->db->where('wow_characters.user_id', @$this->session->userdata('user')->Id);
		$this->db->where('wow_characters.level', '120');
		$this->db->join('roster', 'roster.name = wow_characters.name AND REPLACE(roster.realm, " ", "") = REPLACE(wow_characters.realm, " ", "")');
		$this->db->order_by('wow_characters.main_character DESC');
		$data['characters'] = $this->db->get()->result();
		$data['title'] = "Characters";
		$this->load->template('characters', $data);
	}

	public function join_event($eventId='') {
		$userId = $this->session->userdata('user')->Id;
		$main_character_id = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$userId} AND main_character = 1")->row();
		if($userId && $eventId) {
			$import = array(
				'user_id' => $userId,
				'eventId' => $eventId,
				'character_name' => $main_character_id->name,
				'character_realm' => $main_character_id->realm,
			);
			$this->db->insert('joined_events', $import);
			redirect('welcome/activities');
		} else {
			redirect('login');
		}
	}

	public function leave_event($eventId='') {
		$userId = $this->session->userdata('user')->Id;
		if($userId && $eventId) {
			$this->db->where('user_id', $userId);
			$this->db->where('eventId', $eventId);
			$this->db->delete('joined_events');
			redirect('welcome/activities');
		} else {
			redirect('login');
		}
	}

	public function set_main($characterId='') {
		$userId = $this->session->userdata('user')->Id;
		if($userId && $characterId) {
			$this->db->set('main_character', 0);
			$this->db->where('user_id', $userId);
			$this->db->update('wow_characters');

			$this->db->set('main_character', 1);
			$this->db->where('user_id', $userId);
			$this->db->where('Id', $characterId);
			$this->db->update('wow_characters');
			redirect('welcome/characters');
		} else {
			redirect('login');
		}
	}

	public function roster() {
		$data['characters'] = $this->db->query("SELECT * FROM roster")->result();
		$this->load->template('roster', $data);
	}

	public function activities_bot($key) {
		if($key == "thcbot") {
			$this->db->select('*');
			$this->db->from('activities');
			$result = $this->db->get()->result();
			print_r(json_encode($result));
		}
	}

	public function leaderboard() {
		$users = $this->db->query("SELECT * FROM users")->result();
		foreach ($users as $key => $value) {
			$characters = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$value->Id}")->result();
			$main_character = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$value->Id} AND main_character = 1")->row();
			if(!$main_character) {
				$main_character = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$value->Id} LIMIT 1")->row();
			}
			if($main_character) {
				$points = number_format($this->db->query("SELECT SUM(joined_events.points) as points FROM joined_events WHERE user_id = {$value->Id}")->row()->points, 0);
				if($points) {
					@$users[$key]->points = $points;
					@$users[$key]->name = $main_character->name . "-" . str_replace(' ', '', $main_character->realm);
				} else {
					unset($users[$key]);
				}
			} else {
				unset($users[$key]);
			}
		}

		array_multisort(array_map(function($element) {
	      return $element->points;
	  }, $users), SORT_DESC, $users);

	  $data['users'] = $users;
		$this->load->template('points2', $data);
	}

	public function teams() {
		$data['teams'] = $this->db->query("SELECT * FROM teams")->result();
		$this->load->template('teams', $data);
	}

	public function viewTeam($id) {
		$data['team'] = $this->db->query("SELECT * FROM teams WHERE Id = {$id}")->row();
		$data['team_members'] = $this->db->query("SELECT team_members.Id, wow_characters.name as character_name, wow_characters.realm as character_realm, team_members.isLeader FROM team_members JOIN wow_characters ON wow_characters.user_id = team_members.user_id AND wow_characters.main_character = 1 WHERE team_members.team_id = {$id}")->result();
		$this->load->template('teamView', $data);
	}

	public function teamApply($id) {
		$data['team'] = $this->db->query("SELECT * FROM teams WHERE Id = {$id}")->row();
		$data['team_questions'] = $this->db->query("SELECT * FROM team_questions WHERE team_id = {$id}")->result();
		$this->load->template('teamApply', $data);
	}

	public function teamAppSubmit($id) {
		foreach ($this->input->post() as $key => $value) {
			if($key && ($key != "Name" && $key != "Realm")) {
				$import[] = array(
					'question' => str_replace('_', ' ', $key),
					'answer' => $value,
					'team_id' => $id,
					'user_id' => (@$this->session->userdata('user')->Id ? $this->session->userdata('user')->Id : 0),
				);
			}
		}

		$import2 = array(
			'team_id' => $id,
			'user_id' => (@$this->session->userdata('user')->Id ? $this->session->userdata('user')->Id : 0),
			'name' => $this->input->post("Name"),
			'realm' => $this->input->post("Realm"),
		);

		$this->db->insert_batch('team_app_answers', $import);
		$this->db->insert('team_applicants', $import2);
		redirect('welcome/teams');
	}
}
