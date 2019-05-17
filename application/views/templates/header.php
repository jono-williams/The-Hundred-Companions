<!DOCTYPE html>
		<html>
			<head>
				<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
				<link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
				<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
				<script src="//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
				<link rel="stylesheet" href="/assets/css/main.css">
				<link rel="stylesheet" href="/assets/css/cms.css">
				<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
				<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
				<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
				<link rel="manifest" href="/site.webmanifest">
				<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
				<meta name="msapplication-TileColor" content="#da532c">
				<meta name="theme-color" content="#ffffff">
				<meta charset="utf-8">
				<meta name="description" content="The Hundred Companions is a world of warcraft community for Horde players. We do raids, RBG, mythics, arena, and world pvp - almost everything imaginable in the game to have fun together!">
			  <meta name="keywords" content="world of warcraft, Horde, community, game community, rbg, mythic, arena, world pvp, wpvp, pvp, raid">
			  <meta name="author" content="Gotama">
			  <meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title><?=(@$title ? $title . ' - ' : '')?> <?=config_item('site_name')?></title>
				<style type="text/css">
					body {
						background-color:transparent;
						padding:0px;
						margin:0px;
						text-align:center;
						line-height:1.5;
						background-attachment: fixed;
						background-size: cover;
						text-align: left;
						color: #daa520;
					}

					#left #left_menu li a {
						color: #daa520;
					}

					.nice_button {
				    height: 35px;
				    text-align: justify;
				  }

					table, .table {
						color:#daa520;
					}

					#hand, #wrapper {
						background: transparent;
					}

					#wrapper {
						margin-top: 33px;
					}

					footer #siteinfo {
						margin:0;
						left: 0;
						text-align: center;
						user-select: none;
					}

					footer {
						height: 130px;
						padding: 25px;
						text-align: center;
					}
				</style>
			</head>

			<body>
				<section id="wrapper">
					<header id="hand">
						<a href="<?=base_url()?>"><img style="max-height:200px; display:block; margin:0 auto; margin-bottom:50px" src="<?=base_url()?>assets/images/logo2.png" alt="<?=config_item('site_name')?>"></a>
					</header>
					<ul id="top_menu">
						<li><a href="<?=base_url()?>">Home</a></li>
						<li><a href="<?=base_url()?>welcome/activities">Activities</a></li>
						<li><a href="<?=base_url()?>welcome/roster">Roster</a></li>
						<?php if(@$this->session->userdata('user')->Id) { ?>
							<li><a href="<?=base_url()?>welcome/characters">Characters</a></li>
							<li><a href="<?=base_url()?>welcome/leaderboard">Leaderboard</a></li>
						<?php } ?>
					</ul>
					<div id="main">
						<aside id="left">
							<a href="<?=base_url()?>login" class="register"><p></p><span></span></a>
							<article>
							<h1 class="top">User Panel</h1>
							<?php if(@!$this->session->userdata('user')->Id) { ?>
							<section class="body">
								<form action="<?=base_url()?>auth/enter" method="post" accept-charset="utf-8">
										<center id="sidebox_login">
											<input type="text" name="inputEmail" id="inputEmail" value="" placeholder="Email address">
											<input type="password" name="inputPassword" id="inputPassword" value="" placeholder="Password">
										</center>
										<table class="custom_login" cellpadding="2" cellspacing="2">
											<tbody>
												<tr>
													<td valign="top">
														<div>
															<input type="submit" name="login_submit" value="Log in!">
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</form>
							</section>
						<?php } else { ?>
							<section class="body">
								<?php @$main_character = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$this->session->userdata('user')->Id} AND main_character = 1")->row();
											$characters = $this->db->query("SELECT * FROM wow_characters WHERE user_id = {$this->session->userdata('user')->Id}")->result();
											$points = 0;
											foreach ($characters as $key => $value) {
												@$search = $value->name . '-' . addslashes(str_replace(' ', '', $value->realm));
												$points = number_format($points, 0) + number_format($this->db->query("SELECT SUM(joined_events.points) as points FROM joined_events WHERE CONCAT(character_name, '-', REPLACE(character_realm, ' ', '')) = '{$search}'")->row()->points, 0);
											}
											$role = $this->db->query("SELECT * FROM rank WHERE Id = {$this->session->userdata('user')->rank_id}")->row();
								?>
								<?php if(@$main_character) { ?>
									<img style="max-height:50px; float:left;" src="//render-eu.worldofwarcraft.com/character/<?=$main_character->thumbnail?>" alt="<?=$main_character->name?>">
									<div style="text-align: left; width: 80%; float:left; padding-left:10px;">
										<?=$main_character->name?>-<?=$main_character->realm?><br>
										<b>Points: </b><?=($points ? $points : '0')?><br>
										<b>Role: </b><span style="color:<?=@$role->colour?>"><?=@$role->name?></span><br>
									</div>
								<?php } ?>
								<br><br><br>
								<a href="<?=base_url()?>auth/signout"><button type="button" class="nice_button" name="button">Sign Out!</button></a>
							</section>
						<?php } ?>
						</article>

						<?php if(@$this->session->userdata('role')->name == "Owner" || @$this->session->userdata('role')->name == "Moderator" || @$this->session->userdata('role')->name == "Admin") { ?>
							<article>
								<h1 class="top">Admin Panel</h1>
								<section class="body">
									<ul id="left_menu">
										<li><a href="<?=base_url()?>admin/roster_import"><img src="/assets/images/bullet.png">Roster Import</a></li>
										<li><a href="<?=base_url()?>admin/activities_import"><img src="/assets/images/bullet.png">Activities Import</a></li>
										<li><a href="<?=base_url()?>admin/activities"><img src="/assets/images/bullet.png">Activities Overview</a></li>
										<li><a href="<?=base_url()?>admin/joined_events"><img src="/assets/images/bullet.png">Attendance Overview</a></li>
										<li><a href="<?=base_url()?>admin/usersPoints"><img src="/assets/images/bullet.png">Users Points</a></li>
										<li><a href="<?=base_url()?>admin/stats"><img src="/assets/images/bullet.png">Stats</a></li>
										<li><a href="<?=base_url()?>admin/users"><img src="/assets/images/bullet.png">Users Management</a></li>
										<li><a href="<?=base_url()?>admin/articles"><img src="/assets/images/bullet.png">Article Management</a></li>
										<li><a href="<?=base_url()?>admin/user_import"><img src="/assets/images/bullet.png">User Import</a></li>
										<li><a href="<?=base_url()?>admin/streamers"><img src="/assets/images/bullet.png">Streamers</a></li>
									</ul>
								</section>
							</article>
						<?php } ?>

								<article>
									<h1 class="top">Latest Events</h1>
									<section class="body">
										<?php foreach ($this->db->where('timestamp >= CURDATE()')->order_by('timestamp DESC')->limit(3)->get('activities')->result() as $key => $a) { ?>
							        <a href="<?=base_url()?>welcome/activities"><li style="width:100%; display: inline-table;">
							          <div class="quest-mark" style="float:left; position:relative;">
							            <img style="height:55px; margin-right:10px;" src="<?=base_url()?>assets/images/frame.png" alt="quest-frame">
							            <img style="position: absolute; height: 20px; margin-right: 10px; top: 14px; left: calc(43% - 10px);" src="<?=base_url()?>assets/images/e-mark.png" alt="emark-frame">
							          </div>
							          <h5 style="font-size:14px;text-align:left;"><?=$a->name?></h5>
							          <h6 style="font-size:8px;text-align:left;"><?=substr($a->description,0,50)?>...</h6>
							        </li></a>
							      <?php } ?>
									</section>
								</article>

								<article>
									<h1 class="top">Discord</h1>
									<section class="body">
										<a href="//discord.gg/2DhpNr8" target="_blank"><img style="width:100%;" src="//www.steem.center/images/6/62/Discord_Color_Logo.png" alt="discord"></a>
									</section>
								</article>
								<?php
									@$list = $this->db->query("SELECT GROUP_CONCAT('&user_login=', twitchName SEPARATOR '') as list FROM streamers")->row()->list;
									$ch = curl_init();
									if($list) {
										curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/streams?$list");
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
										curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

										$headers = array();
										$headers[] = "Client-Id: vmbxj6hpbq1me2rqcbt3awoo3rl4zj";
										curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

										$result = curl_exec($ch);
										if (curl_errno($ch)) {
												echo 'Error:' . curl_error($ch);
										}

										curl_close ($ch);
										$streamers = json_decode($result)->data;
									}

								?>
								<?php if(@$streamers) { ?>
								<article>
									<h1 class="top">Streamers</h1>
									<section class="body">
										<?php foreach ($streamers as $key => $a) { ?>
											<a target="_blank" href="//www.twitch.tv/<?=$a->user_name?>"><div style="display:table; width:100%; margin-bottom:20px;">
												<img style="float:left; width:25%;" src="<?=str_replace('{width}', '100', str_replace('{height}', '50', $a->thumbnail_url))?>" alt="<?=$a->user_name?>">
												<div style="text-align: left; float:right; width:75%; padding-left:10px;">
													<span><?=$a->user_name?></span><br>
													<span style="font-size:10px;"><?=$a->title?></span>
												</div>
											</div></a>
							      <?php } ?>
									</section>
								</article>
								<?php } ?>

						</aside>

						<aside id="right">
							<?=$body?>
						</aside>

						<div class="clear"></div>
					</div>
					<footer>
										<center>
												<div id="logos">
												</div>
												<div id="siteinfo">
														All righs reserved © <font color="#695946"><?=config_item('site_name')?></font><br/>
														World of Warcraft© and Blizzard Entertainment© are all trademarks or registered trademarks of Blizzard Entertainment in the United States and/or other countries. <br>
														These terms and all related materials, logos, and images are copyright © Blizzard Entertainment.<br>
														This is a Community site and is in no way associated with or endorsed by Blizzard Entertainment©
												</div>
										</center>
					</footer>
				</section>
			</body>
		</html>
<?php exit; ?>
