<style>
.disp_assn_proj_popup {cursor:pointer;}
</style>
<input type="hidden" id="role" value="<?php echo $role;?>">
<input type="hidden" id="type" value="<?php echo $type;?>">
<input type="hidden" id="user_srch" value="<?php echo $user_srch;?>">
<div class="proj_grids m-cmn-flow">
	<?php
	$srch_res = '';
	if(isset($_GET['user']) && trim($_GET['user']) && isset($userArr['0']['User']) && !empty($userArr['0']['User'])){
	    if($userArr['0']['User']['name']) {
		$srch_res = ucfirst($userArr['0']['User']['name'])." ".ucfirst($userArr['0']['User']['last_name']);
	    } else {
		$srch_res = $userArr['0']['User']['email'];
	    }
	}

	if(isset($user_srch) && trim($user_srch)) {
	    $srch_res = $user_srch;
	}
	?>
    <?php if(trim($srch_res)){ ?>
	<div class="cmn_search_result cmn_bdr_shadow">
		<div class="global-srch-res fl"><?php echo __('Search Results for');?>: <span><?php echo $srch_res;?></span></div>
		<div class="fl global-srch-rst"><a href="<?php echo HTTP_ROOT.'users/manage';?>"><i class="material-icons">&#xE8BA;</i></a></div>
		<div class="cb"></div>
	</div>
    <?php } ?>


<div class="user_div_bk usrs_page m-list-tbl">

    <?php //if(!empty($userArr) && isset($userArr)){
	$count = 1;
	$is_invited_user = 0;
	if ($role == 'invited') {
	    $is_invited_user = 1;
	}

	foreach($userArr as $user) {
		if ($user['Role']['role'] == 'Owner') {
		    $colors = 'own_clr';
		    $usr_typ_name = __('Owner',true);
		} else if ($user['Role']['role'] == 'Admin') {
		    $colors = 'adm_clr';
		    $usr_typ_name = __('Admin',true);
		} else if ($user['Role']['role'] == 'User' && $role != 3) {
		    $colors = 'usr_clr';
		    $usr_typ_name = __('User',true);
		} else if ($user['Role']['role'] == 'Guest') {
		    $colors = 'cli_clr';
		    $usr_typ_name = __('Guest',true);
		} else{
			$colors = 'usr_clr';
		    $usr_typ_name = ($user['Role']['role'])?$user['Role']['role']:__('User',true);
		}

		if ($role == 'invited') {
		    $colors = 'usr_clr';
		    $usr_typ_name = __('User',true);
			if($user['CompanyUser']['is_client'] == 1){
				$colors = 'cli_clr';
				$usr_typ_name = __('Client',true);
			}
		}
		if($role == 'recent') {
                    $colors = 'usr_clr';
		    $usr_typ_name = __('User',true);
			if($user['User']['is_client'] == 1)
			{
				$colors = 'cli_clr';
				$usr_typ_name = __('Client',true);
			} else {
				$usr_typ_name = ($user['User']['role'])?$user['User']['role']:__('User',true);
			}
		}
		if($user['CompanyUser']['is_client'] == 1){
			$colors = 'cli_clr';
			$usr_typ_name = __('Client',true);
		}
		if($user['CompanyUser']['is_client'] == 1 && $user['CompanyUser']['user_type'] == 2){
			$colors = 'cli_clr';
			$usr_typ_name = __('Admin/Client',true);
		}
		?>
    <div class="usr_mcnt fl cmn_bdr_shadow" id="usr_mcnt<?php echo $user['User']['id'];?>">

		<div class="usr_top_cnt">
			<div id="tour_role_user" class="usr_cat <?php echo $colors;?>" rel="tooltip" title="<?php echo $usr_typ_name;?>"><?php echo $usr_typ_name;?></div>
			<div id="tour_acton_user" class="usr_act_det">
				<span class="dropdown">
					<a class="dropdown-toggle active" data-toggle="dropdown" href="javascript:void(0);" data-target="#">
					  <i class="material-icons">&#xE5D4;</i>
					</a>
					<ul class="dropdown-menu right0">
					<?php if((SES_TYPE ==1 || (SES_TYPE !=3 && $user['CompanyUser']['user_type'] != 1)) && $role != 'disable'){?>
                                            <li><a class="edit-exist-usr" id="edit-exist-usr<?php echo $user['User']['id'];?>" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-uid="<?php echo $user['User']['uniq_id'];?>" data-usr-name="<?php echo $user['User']['name'];?>" data-comp-count="<?php echo ($userinmorecompany && in_array($user['User']['id'],$userinmorecompany) && SES_ID != $user['User']['id'])?1:0;?>"><i class="material-icons">&#xE8A6;</i> <?php echo __('Edit Profile');?> <?php echo ($userinmorecompany && in_array($user['User']['id'],$userinmorecompany) && SES_ID != $user['User']['id'])? '<i class="material-icons">&#xE897;</i>':'';?></a> </li>
					<?php } ?>
                                    </ul>
				</span>
			</div>
			<?php $random_bgclr = $this->Format->getProfileBgColr($user['User']['id']); ?>
			<div id="pimg_<?php echo $user['User']['id']; ?>" class="user_img holder <?php echo $random_bgclr; ?>">
				<?php if(trim($user['User']['photo'])) {?>
					<img class="lazy" data-original="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<?php echo $user['User']['photo']; ?>&sizex=94&sizey=94&quality=100" width="94" height="94" />
				<?php } else { ?>
					<?php if (isset($user['User']['name']) && trim($user['User']['name'])) { ?>
                                            <span class="name_txt"><?php echo mb_substr(trim($user['User']['name']),0,1, "utf-8"); ?></span>
                                        <?php }else if(isset($user['User']['short_name']) && trim($user['User']['short_name'])){
                                            echo mb_substr(trim($user['User']['short_name']),0,1, "utf-8");
                                        }else{ ?>
                                            <img src="<?php echo HTTP_ROOT; ?>img/images/user.png" />
                                        <?php } ?>
				<?php } ?>
			</div>
			<h3 class="invite_user_cls ellipsis-view" id="pn_<?php echo $user['User']['id']; ?>" data-usr-id="<?php echo $user['User']['id']; ?>" data-usr-name="<?php echo trim($user['User']['name']); ?>" title="<?php echo trim($user['User']['name']); ?>" rel="tooltip" ><?php if(isset($user['User']['name']) && trim($user['User']['name'])) {echo ucfirst($user['User']['name']); } else { echo "&nbsp;";} ?></h3>
			<h4 id="psn_<?php echo $user['User']['id']; ?>"><?php echo $user['User']['short_name']; ?></h4>
		</div>


		<div class="usr_cnts">
			<ul>
				<li>
					<span class="cnt_ttl_usr"><?php echo __('Last Activity');?></span>
					<span class="cnt_usr" id="pla_<?php echo $user['User']['id']; ?>">
						<?php
						if ($user['CompanyUser']['is_active'] == 0 && $_GET['role'] == 'invited') {
						$activity = "<span class='fnt_clr_rd'>".__("Invited",true)."</span>";
						}else if ($_GET['role'] == 'recent') {
						if($user['User']['is_active'] == 2){
							$activity = "<span class='fnt_clr_rd'>".__("Invited",true)."</span>";
						}else if(($istype == 1 || $istype == 2) && !$user['User']['dt_last_login']) {
							$activity = "<span class='fnt_clr_rd'>".__("No activity yet",true)."</span>";
						}else if($user['User']['dt_last_login']){
							$activity = $user['User']['latest_activity'];
						}
						}else {
						if ($user['User']['dt_last_login']) {
							$activity = $user['User']['latest_activity'];
						} elseif ($user['CompanyUser']['is_active']) {
						}
						if(($istype == 1 || $istype == 2) && !$user['User']['dt_last_login']) {
							if($user['CompanyUser']['is_active'] == 2){
							$activity = "<span class='fnt_clr_rd'>".__("Invited",true)."</span>";
							}else{
							$activity = "<span class='fnt_clr_rd'>".__("No activity yet",true)."</span>";
							}
						}
						}
						echo $activity;
						?>
					</span>
				</li>
				<li id="tour_info_user">
					<span class="cnt_ttl_usr"><?php echo __('Created');?></span>
					<span class="cnt_usr" id="pcr_<?php echo $user['User']['id']; ?>">
						<?php
							if ($role == "invited") {
							$crdt = $user['UserInvitation']['created'];
							} else if ($role == "recent") {
							$crdt = $user['User']['created'];
							}else{
							$crdt = $user['CompanyUser']['created'];
							}
							if ($crdt != "0000-00-00 00:00:00") {
								echo $user['User']['created_on'];
							} ?>
					</span>
				</li>
				<li>
					<span class="usr_email cnt_ttl_usr"><?php echo __('Email');?></span>
					<span class="cnt_usr" id="pemail_<?php echo $user['User']['id']; ?>" title="<?php echo $user['User']['email']; ?>">
					<?php
					$email = $this->Format->shortLength($user['User']['email'],25);
					echo $email; ?></span>
				</li>
				<li id="tour_projs_user" <?php if($this->Format->isAllowed('Assign Project',$roleAccess)){ ?> class="disp_assn_proj_popup" <?php } ?>>
					<span class="cnt_ttl_usr"><?php echo __('Projects');?></span>
					<span id="remain_prj_<?php echo $user['User']['id'];?>" class="cnt_usr nm_prj nm_prj_mx_width ellipsis-view" title="<?php echo $user['User']['all_project_lst']; ?>">
						<?php if(isset($user['User']['all_project']) && trim($user['User']['all_project'])) { 	echo $user['User']['all_project'];
						} else { echo 'N/A'; }
						?>
					</span>
				</li>
			</ul>
		</div>
	</div>
	<?php $count++;
		} ?>

    <div class="cb"></div>
    <input type="hidden" id="is_invited_user" value="<?php echo $is_invited_user;?>" />

   <?php //}
   if(!isset($userArr) || empty($userArr)){ ?>
	<div class="row">
		<div class="col-lg-12 text-centre">
		    <div class="no_usr fl cmn_bdr_shadow">
			<h2 class="fnt_clr_rd">
                            <?php if($role == 'client'){ ?>
                            <?php echo __('No clients found');?>
                            <?php }else{ ?>
                            <?php echo __('No users found');?>.
                            <?php } ?>
                        </h2>
                    </div>
		</div>
	</div>
    <?php } ?>
</div>

<div class="cbt"></div>
<input type="hidden" id="getcasecount" value="<?php echo $caseCount; ?>" readonly="true"/>
<?php if ($caseCount) {
$page_url = HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=";
$pagedata = array('mode' => 'php', 'pgShLbl' => $this->Format->pagingShowRecords($caseCount, $page_limit, $casePage), 'csPage' => $casePage, 'page_limit' => $page_limit, 'caseCount' => $caseCount, 'page_url' => $page_url);
echo $this->element("task_paginate",$pagedata); ?>
<?php } ?>
<input type="hidden" id="totalcount" name="totalcount" value="<?php echo $count; ?>"/>
</div>
<div id="projectLoader">
    <div class="loadingdata"><?php echo __('Invitation resend');?>...</div>
</div>
<script>
$(document).ready(function() {
	if(typeof hopscotch !='undefined'){
		if(localStorage.getItem("tour_type") == '0'){
		GBl_tour = tour_user<?php echo LANG_PREFIX;?>;
	}
	}
	setTimeout(hideCmnMesg, 2000);
	$('.disp_assn_proj_popup').off().on('click',function(){
		if($('.icon-assign-usr').length){
			$('.icon-assign-usr').trigger('click');
		}
	});
});
</script>
