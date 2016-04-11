<?php 
/**
 * User Related
 * 用户相关模块
 *
 **/ 

class ClientUserAction extends Action {
	
	public function client_login() {
		$m_announcement = M('announcement');
		$m_loghistory = M('loginHistory');
		$where['status'] = array('eq', 1);
		$where['isshow'] = array('eq', 1);
		$this->announcement_list = $m_announcement->where($where)->order('order_id')->select();
		if (session('?name') && !isset($_POST['name'])&&!isset($_POST['password'])){
			$this->ajaxReturn(session('user_id'), '', 1);
		}elseif(1){
			if((!isset($_POST['name']) || $_POST['name'] =='')||(!isset($_POST['password']) || $_POST['password'] =='')){
				$this->ajaxReturn(null, '用户名或密码为空', 0);
			}elseif (isset($_POST['name']) && $_POST['name'] != ''){
				$m_user = M('user');
				$user = $m_user->where(array('name' => trim($_POST['name'])))->find();
	
				$login_where['user_id'] = $user['user_id'];
				$login_where['login_status'] = 2;
				$login_where['login_time'] = array('gt', time()-10*60);
				$login_count = $m_loghistory->where($login_where)->count();
				if($login_count >= 3){
					$login_time = $m_loghistory->where(array('user_id'=>$user['user_id'],'login_status'=>2))->order('login_time desc')->getField('login_time');
					$point_time = 10 - (round((time() - $login_time)/60));
					$this->ajaxReturn(null, '您登录的错误次数过于频繁，请'.$point_time.'分钟后再试。或点击忘记密码重置', 0);
				}
				//记入登录记录
				$record['user_id'] = $user['user_id'];
				$record['login_time'] = time();
				$record['login_ip'] = get_client_ip();
	
				if ($user['password'] == md5(md5(trim($_POST['password'])) . $user['salt'])) {
					if (-1 == $user['status']) {
						$this->ajaxReturn(null, L('YOU_ACCOUNT_IS_UNAUDITED'), 0);
					} elseif (0 == $user['status']) {
						$this->ajaxReturn(null, L('YOU_ACCOUNT_IS_AUDITEDING'), 0);
					}elseif (2 == $user['status']) {
						$this->ajaxReturn(null, L('YOU_ACCOUNT_IS_DISABLE'), 0);
					}else {
						$d_role = D('RoleView');
						$role = $d_role->where('user.user_id = %d', $user['user_id'])->find();
						if ($_POST['autologin'] == 'on') {
							session(array('expire'=>259200));
							cookie('user_id',$user['user_id'],259200);
							cookie('name',$user['name'],259200);
							cookie('salt_code',md5(md5($user['user_id'] . $user['name']).$user['salt']),259200);
						}else{
							session(array('expire'=>3600));
						}
						if (!is_array($role) || empty($role)) {
							$this->ajaxReturn(null, L('HAVE_NO_POSITION'), 0);
						} else {
							if($user['category_id'] == 1){
								session('admin', 1);
							}
								
							$record['login_status'] = 1;
							$m_loghistory->add($record);
							session('role_id', $role['role_id']);
							session('user_img', $user['img']);
							session('position_id', $role['position_id']);
							session('role_name', $role['role_name']);
							session('department_id', $role['department_id']);
							session('name', $user['name']);
							session('user_id', $user['user_id']);
							$username = $user['name'];
							$upload = $user['img'];
							F('img_id_'.$user['user_id'],$upload);
							$this->ajaxReturn($user['user_id'], L('LOGIN_SUCCESS'), 1);
						}
					}
				} else {
					$record['login_status'] = 2;
					$m_loghistory->add($record);
					$this->ajaxReturn(null, L('INCORRECT_USER_NAME_OR_PASSWORD'), 0);
				}
			}
		}else{
			$this->ajaxReturn(null, '请输入用户名和密码登录', 0);
		}
	}
	
	//退出
	public function logout() {
		session(null);
		cookie('user_id',null);
		cookie('name',null);
		cookie('salt_code',null);
		F('img_id',null);
		$this->ajaxReturn(null, L('LOGIN_OUT_SUCCESS'), 0);
	}
	
}