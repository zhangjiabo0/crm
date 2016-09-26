<?php 
class CustomerBAction extends Action {

	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('getcustomerBlist','analytics', 'validate','check', 'remove', 'fenpei', 'revert','changecontent','customerBlock','check_customerB_limit','excelimportdownload','search','listDialog','getcustomerBoriginal','batchclose','batchfocus','close_share','share','getcurrentstatus')
		);
		B('Authenticate', $action);
	}

	/*无法验证中文
	public function checkName(){
		$customerB = M('customerB');
		if ($customerB->where('name = "' . $_GET['name'] . '"' )->find()){
			$this->ajaxReturn(1, "用户名不可以使用啊！",  1);
		}else{
			$this->ajaxReturn(0, "用户名可以使用！", 0);
		}
	}
	*/
	
	public function check(){
		import("@.ORG.SplitWord");
		$sp = new SplitWord();
		$m_customerB = M('customerB');
		$useless_words = array(L('COMPANY'),L('LIMITED'),L('DI'),L('LIMITED_COMPANY'));
		if ($this->isAjax()) {
			$split_result = $sp->SplitRMM($_POST['name']);
			if(!is_utf8($split_result)) $split_result = iconv("GB2312//IGNORE", "UTF-8", $split_result) ;
			$result_array = explode(' ',trim($split_result));
            if(count($result_array) < 2){
                $this->ajaxReturn(0,'',0);
                die;
            }
			foreach($result_array as $k=>$v){
				if(in_array($v,$useless_words)) unset($result_array[$k]);
			}
			$name_list = $m_customerB->getField('name', true);
			$seach_array = array();
			foreach($name_list as $k=>$v){
				$search = 0;
				foreach($result_array as $k2=>$v2){
					if(strpos($v, $v2) > -1){
						$v = str_replace("$v2","<span style='color:red;'>$v2</span>", $v, $count);
						$search += $count;
					}
				}
				if($search > 2) $seach_array[$k] = array('value'=>$v,'search'=>$search);
			}
			$seach_sort_result = array_sort($seach_array,'search','desc');
			if(empty($seach_sort_result)){
				$this->ajaxReturn(0,L('ABLE_ADD'),0);
			}else{
				$this->ajaxReturn($seach_sort_result,L('CUSTOMERB_IS_CREATED'),1);
			}
		}
	}
	
	public function validate() {
		if($this->isAjax()){
            if(!$this->_request('clientid','trim') || !$this->_request($this->_request('clientid','trim'),'trim')) $this->ajaxReturn("","",3);
            $field = M('Fields')->where('model = "customerB" and field = "%s"', $this->_request('clientid','trim'))->find();
            $m_customerB = $field['is_main'] ? D('CustomerB') : D('CustomerBData');
            $where[$this->_request('clientid','trim')] = array('eq',$this->_request($this->_request('clientid','trim'),'trim'));
            if($this->_request('id','intval',0)){
                $where[$m_customerB->getpk()] = array('neq',$this->_request('id','intval',0));
            }
			if($this->_request('clientid','trim')) {
				if ($m_customerB->where($where)->find()) {
					$this->ajaxReturn("","",1);
				} else {
					$this->ajaxReturn("","",0);
				}
			}else{
				$this->ajaxReturn("","",0);
			}
		}
	}
	
	public function remove(){
		if($this->isPost()){
			$m_customerB = M('CustomerB');
			$customerB_ids = is_array($_POST['customerB_id']) ? implode(',', $_POST['customerB_id']) : '';
			if('' == $customerB_ids){
				alert('error', L('NOT_CHOOSE_ANY'), $_SERVER['HTTP_REFERER']);
			}
			$lock_names = $m_customerB->where('customerB_id in (%s) and is_locked = 1',$customerB_ids)->getField('name',true);			
			if($lock_names){
				$customerBs = implode(' , ',$lock_names);
				alert('error','客户('.$customerBs.')已被锁定，不能放入客户池！',$_SERVER['HTTP_REFERER']);
			}
			if($m_customerB->where('customerB_id in (%s)', $customerB_ids)->setField('owner_role_id',0)){
				alert('success', L('BATCH_INTO_THE_SUCCESSFUL_CUSTOMERB_POOL'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('BATCH_INTO_THE_CUSTOMERB_POOL_FAILURE'), $_SERVER['HTTP_REFERER']);
			}
			
		}
	}
	public function receive(){
		$m_customerB = M('CustomerB');
		$m_config = M('Config');
		$m_customerB_record = M('customerB_record');
		if(!empty($_POST['owner_role_id'])){
			$owner_role_id = $_POST['owner_role_id'];
		}elseif(!empty($_POST['owner_role'])){
			$owner_role_id = $_POST['owner_role'];
		}else{
			$owner_role_id = session('role_id');
		}
		$data['owner_role_id'] = $owner_role_id;
		$data['update_time'] = time();
		//是否是分配需要提醒
		$need_alert = false;
		//单个领取
		if($this->isGet()){
			$customerB_id = isset($_GET['customerB_id']) ? intval(trim($_GET['customerB_id'])) : 0;
			//判断是否符合领取条件
			$customerB_limit_counts = $m_config->where('name = "customerB_limit_counts"')->getField('value');
			$customerB_record_count = $this->check_customerB_limit(session('user_id'), 1);
			if($customerB_record_count < $customerB_limit_counts){
				$contactsB = M('rContactsBCustomerB')->where('customerB_id = %d', $customerB_id)->select();
				foreach($contactsB as $k=>$v ){
					M('contactsB')->where('contactsB_id = %d', $v['contactsB_id'])->setField('owner_role_id',$owner_role_id);
				}
				if($m_customerB->where('customerB_id = %d', $customerB_id)->save($data)){
					$info['customerB_id'] = $customerB_id;
					$info['user_id'] = session('user_id');
					$info['start_time'] = time();
					$info['type'] = 1;
					$m_customerB_record->add($info);
					alert('success', L('GET_THE_SUCCESS'), $_SERVER['HTTP_REFERER']);
				}else{
					alert('error', L('GET_THE_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('GET_THE_FAILURE_OVER_GET'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$customerB_name = array();
			$customerB_ids = $_POST['customerB_id'];
			//是否批量操作 否的话是单个分配
			if(!$_POST['customerB_id']){alert('error', L('NO_CHANCE_CUSTOMERB'), $_SERVER['HTTP_REFERER']);}
			if(is_array($customerB_ids)){
				//检查用户是否符合领取客户池资源资格
				//判断领取或分配  operating_type  receive:领取  assign:分配
				$customerB_limit_counts = $m_config->where('name = "customerB_limit_counts"')->getField('value');
                $customerB_record_count = $this->check_customerB_limit(session('user_id'), 1);
				if(sizeof($customerB_ids) + $customerB_record_count <= $customerB_limit_counts){
					if($_POST['operating_type'] == 'receive'){
						
						if($customerB_record_count >= $customerB_limit_counts){
							alert('error', L('GET_THE_FAILURE_OVER_GET'), $_SERVER['HTTP_REFERER']);
						}
					}
				}else{
					alert('error', L('GET_THE_FAILURE_OVER_GET_LIMIT',array($customerB_limit_counts)),$_SERVER['HTTP_REFERER']);
				}

				$where['update_time'] = array('lt',(time()-86400));
				$where['customerB_id'] = array('in',implode(',',$customerB_ids));
				$where['owner_role_id'] = array('gt',0);
				$contactsB = M('rContactsBCustomerB')->where('customerB_id in (%s)', implode(',',$customerB_ids))->select();
				foreach($contactsB as $k=>$v ){
					M('contactsB')->where('contactsB_id = %d', $v['contactsB_id'])->setField('owner_role_id',$owner_role_id);
				}
				$updated_owner = $m_customerB->where($where)->save($data);
				unset($where['update_time']);
				$where['owner_role_id'] = array('eq',0);
				$customerB_name = $m_customerB->where($data)->getField('name', true);
				$updated_time = $m_customerB->where($where)->save($data);
				
				//是否操作成功
				if($updated_owner || $updated_time){
					//增加customerB_record记录
					$m_user = M('user');
					$user_id = $m_user->where('role_id = %d', $owner_role_id)->getField('user_id');
					$info['start_time'] = time();
					foreach($customerB_ids as $v){
						$info['customerB_id'] = $v;
						if($_POST['operating_type'] == 'receive'){
							$info['user_id'] = session('user_id');
							$info['type'] = 1;
						}else{
							$info['user_id'] = $user_id;
							$info['type'] = 2;
						}
						$m_customerB_record->add($info);
					}
					//是分配还是领取
					if($_POST['owner_role']){
						$title=L('you_have_new_customerB');
						$content=L('THE_CUSTOMERB_RESOURCES',array(session('name'),implode(',', $customerB_name)));
						$need_alert = true;
					}else{
						alert('success', L('BATCH_TO_GET_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					if($_POST['owner_role']){
						alert('error', L('BATCH_ALLOCATION_FAILURE'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('BATCH_ALLOCATION_FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				}
			}else{
				$where['update_time'] = array('lt',(time()-86400));
				$where['customerB_id'] = intval($customerB_ids);
				$where['owner_role_id'] = array('gt',0);
				$contactsB = M('rContactsBCustomerB')->where('customerB_id = %d', $customerB_ids)->select();
				foreach($contactsB as $k=>$v ){
					M('contactsB')->where('contactsB_id = %d', $v['contactsB_id'])->setField('owner_role_id',$owner_role_id);
				}
				$updated_owner = $m_customerB->where($where)->save($data);
				
				unset($where['update_time']);
				$where['owner_role_id'] = array('eq',0);
				$updated_time = $m_customerB->where($where)->save($data);
			
				if($updated_owner || $updated_time){
					$customerB = $m_customerB->where('customerB_id = %d', intval($customerB_ids))->find();
					$title=L('you_have_new_customerB');
					$content=L('THE_CUSTOMERB_RESOURCES',array(session('name'),U('CustomerB/view','id='.$customerB_ids),$customerB['name']));
					$need_alert = true;
				}else{
					alert('error', L('ASSIGNMENT_FAILURE'), $_SERVER['HTTP_REFERER']);
				}
			}
			
			//分配需要提醒
			if($need_alert){
				if(intval($_POST['message_alert']) == 1) {
					sendMessage($owner_role_id,$content,1);
				}
				if(intval($_POST['email_alert']) == 1){
					$email_result = sysSendEmail($owner_role_id,$title,$content);
					if(!$email_result) alert('error', L('EMAIL_FAILURE_NOT_SET_EFFECTIVE_MAILBOX'),$_SERVER['HTTP_REFERER']);
				}
				if(intval($_POST['sms_alert']) == 1){
					$sms_result = sysSendSms($owner_role_id,$content);
					if(100 == $sms_result){
						alert('error', L('MESSAGE_FAILURE_NOT_SET_EFFECTIVE_MOBILE'),$_SERVER['HTTP_REFERER']);
					}elseif($sms_result < 0){
						alert('error',L('MESSAGE_FAILURE_ERRORCODE',array($sms_result)),$_SERVER['HTTP_REFERER']);
					}
				}
				alert('success', L('DISTRIBUTION_OF_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}
			
		}
	}
	
	public function fenpei(){
		$customerB_id = intval($_GET['customerB_id']);
		 if ($this->isGET()) {
			if($_GET['by'] == 'put'){
				if($customerB_id){
					$customerB = M('customerB')->where('customerB_id = %d', $customerB_id)->find();
					if($customerB['is_locked'] == 0){
						if(M('customerB')->where('customerB_id = %d', $customerB_id)->setField('owner_role_id',0)){
							alert('success', L('IN_THE_SUCCESSFUL_CUSTOMERB_POOL'), U('customerB/index'));
						}else{
							alert('error', L('IN_THE_CUSTOMERB_POOL'), $_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error', L('ISLOCK_CAN_NOT_PUT_IN_CUSTOMERB_POOL'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
				}
			}else{	
				$this->customerB_id = $customerB_id;
				$this->display();
			}
		}
	}

	public function search(){
		$d_v_customerB = D('CustomerBView');
        $by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		$outdays = M('config') -> where('name="customerB_outdays"')->getField('value');
		$outdate = empty($outdays) ? time() : time()-86400*$outdays;
		$where = array();
		$params = array();
		$order = "";
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d')) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc'; break;
			case 'update' : $order = 'update_time desc'; break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'deleted' : $where['is_deleted'] = 1;break;
			case 'me' : $where['owner_role_id'] = session('role_id'); break;
			default :
		        if($this->_get('content') == 'resource'){
		            $where['_string'] = "customerB.owner_role_id=0 or customerB.update_time < $outdate";
		            $all_ids[] = "";
		            $where['owner_role_id'] = array('in', $all_ids);
		        }else{
					$where['owner_role_id'] = array('in',implode(',', $all_ids));
		        }
			break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if (!isset($where['owner_role_id'])) {
			$where['owner_role_id'] = array('in', $all_ids);
		}
		if($this->_get('content') != 'resource'){
			$where['update_time'] = array('gt',$outdate);
		}
		if($this->_get('create_time')){
			$create_times = $this->_get('create_time');
			$create_time = strtotime($create_times['value']);
			switch ($create_times['condition']) {
				case "tgt" :  $where['create_time'] = array('egt',$create_time);break;	//晚于
				case "lt" : $where['create_time'] = array('elt',$create_time);break;		//早于
				case "between" : $where['create_time'] = array('between',array($create_time-1,$create_time+86400));break;//在
				case "nbetween" : $where['create_time'] = array('not between',array($create_time,$create_time+86399));break;//不在
			}
		}
	
		if($this->_get('update_time')){
			$update_times = $this->_get('update_time');
			$update_time = strtotime($update_times['value']);
			switch ($update_times['condition']) {
				case "tgt" :  $where['update_time'] = array('egt',$update_time);break;	//晚于
				case "lt" : $where['update_time'] = array('elt',$update_time);break;		//早于
				case "between" : $where['update_time'] = array('between',array($update_time-1,$update_time+86400));break;//在
				case "nbetween" : $where['update_time'] = array('not between',array($update_time,$update_time+86399));break;//不在
			}
		}
		
		
		if($by == 'deleted') unset($where['update_time']);
		
		if($_GET){
			if($_GET['state']){
				$search = $_GET['state'];
			}
			
			if($_GET['city']){
				$search .= chr(10) . $_GET['city'];
			}
			if($search){
				$search .= chr(10) .trim($_GET['address']['search']);
				$where['address'] = field($search,$_GET['address']['condition']);
			}
			foreach($_GET as $k=>$v){
				if($k != 'act' && $k != 'content' && $k != 'state' && $k != 'city' && $k != 'address' && $k != 'p' && $k != 'update_time' && $k != 'create_time'){					
					if(is_array($v)){
						if(!empty($v['value'])){
							$where[$k] = field($v['value'],$v['condition']);
						}
					}else{
						if(!empty($v)){
							$where[$k] = field($v);
						}
				   }
				}
			}				
		}
		if(trim($_GET['act'] == 'sms')){
			$customerB_ids = $d_v_customerB->where($where)->getField('customerB_id', true);
			$contactsB_ids = M('RContactsBCustomerB')->where('customerB_id in (%s)', implode(',', $customerB_ids))->getField('contactsB_id', true);
			$contactsB_ids = implode(',', $contactsB_ids);
			$contactsB = D('ContactsBView')->where('contactsB.contactsB_id in (%s)', $contactsB_ids)->select();
			$this->contactsB = $contactsB;
			$this->display('Setting:sendsms');	
		}elseif(trim($_GET['act']) == 'excel'){
			if(vali_permission('customerB', 'export')){
				$order = $order ? $order : 'create_time desc';
				$order = $order ? $order : 'create_time desc';
				$customerBList = $d_v_customerB->where($where)->order($order)->select();		
				$this->excelExport($customerBList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			if ($order) {
				$list = $d_v_customerB->where($where)->order($order)->limit(15)->select();
			} else {
				$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
				$list = $d_v_customerB->where($where)->order('create_time desc')->page($p.',15')->select();
				$count = $d_v_customerB->where($where)->count();
				import("@.ORG.Page");
				$Page = new Page($count,15);
				if (!empty($_GET['by'])) {
					$params[] = "by=" . trim($_GET['by']);
				}
				$Page->parameter = implode('&', $params);
				$this->assign('page',$Page->show());
			}	
			if($by == 'deleted') {
				foreach ($list as $k => $v) {
					$list[$k]["delete_role"] = D('RoleView')->where('role.role_id = %d', $v['delete_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
				}
			} else {
				foreach ($list as $k => $v) {
					$days = 0;
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$days =  M('CustomerB')->where('customerB_id = %d', $v['customerB_id'])->getField('update_time');
					$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				}
			}
			$this->customerBlist = $list;
			$this->field_array = getIndexFields('customerB');
			$this->field_list = getMainFields('customerB');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	
	
	public function changeContent(){
		if($this->isAjax()){
			$m_customerB = M('CustomerB');
			$below_ids = getSubRoleId(false);
			$where = array();
			$params = array();
			$where['is_deleted'] = array('neq',1);
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true))); 
			
			if ($_REQUEST["field"]) {
				if (trim($_REQUEST['field']) == "all") {
					$field = is_numeric(trim($_REQUEST['search'])) ? 'name|origin|address|email|telephone|website|account_type|industry|annual_revenue|sic_code|ticker_symbol|ownership|rating|description' : 'name|origin|address|email|telephone|website|account_type|industry|annual_revenue|sic_code|ticker_symbol|ownership|rating|description|create_time|update_time';
				} else {
					$field = trim($_REQUEST['field']);
				}
				$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
				$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
				
				if ('create_time' == $field || 'update_time' == $field) {
					$search = is_numeric($search)?$search:strtotime($search);
				}
				switch ($condition) {
					case "is" : $where[$field] = array('eq',$search);break;
					case "isnot" :  $where[$field] = array('neq',$search);break;
					case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
					case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
					case "start_with" :  $where[$field] = array('like',$search.'%');break;
					case "end_with" :  $where[$field] = array('like','%'.$search);break;
					case "is_empty" :  $where[$field] = array('eq','');break;
					case "is_not_empty" :  $where[$field] = array('neq','');break;
					case "gt" :  $where[$field] = array('gt',$search);break;
					case "egt" :  $where[$field] = array('egt',$search);break;
					case "lt" :  $where[$field] = array('lt',$search);break;
					case "elt" :  $where[$field] = array('elt',$search);break;
					case "eq" : $where[$field] = array('eq',$search);break;
					case "neq" : $where[$field] = array('neq',$search);break;
					case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
					case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
					case "tgt" :  $where[$field] = array('gt',$search+86400);break;
					default : $where[$field] = array('eq',$search);
				}
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$_REQUEST["search"]);
			}
			$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
			$list = $m_customerB->where($where)->order('create_time desc')->page($p.',10')->select();
			foreach($list as $k => $v){
				$list[$k]['contactsB_name'] = M('contactsB')->where('contactsB_id = %d',$v['contactsB_id'])->getField('name');
			}
			$count = $m_customerB->where($where)->count();
			$data['list'] = $list;
			$data['p'] = $p;
			$data['count'] = $count;
			$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
			$this->ajaxReturn($data,"",1);
		}
	}
	
	public function add(){
		if($this->isPost()){		
			$m_customerB = D('CustomerB');
			$m_customerB_data = D('CustomerBData');
			$field_list = M('Fields')->where('model = "customerB" and in_add = 1')->order('order_id')->select();
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$a = array_filter($_POST[$v['field']]);
						$_POST[$v['field']] = !empty($a) ? implode(chr(10),$a) : '';
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$b = array_filter($_POST[$v['field']]);
							$_POST[$v['field']] = !empty($b) ? implode(chr(10),$b) : '';
						}
					break;
				}
			}
			if($m_customerB->create()){
				if($m_customerB_data->create()!==false){
					if($_POST['con_name']){
						$contactsB = array();
						if($_POST['con_name']) $contactsB['name'] = $_POST['con_name'];
						if($_POST['owner_role_id']) $contactsB['owner_role_id'] = $_POST['owner_role_id'];
						if($_POST['con_sex']) $contactsB['sex'] = $_POST['con_sex'];
						if($_POST['con_email']) $contactsB['email'] = $_POST['con_email'];
						if($_POST['con_post']) $contactsB['post'] = $_POST['con_post'];
						if($_POST['con_qq']) $contactsB['qq'] = $_POST['con_qq'];
						if($_POST['con_weixin']) $contactsB['weixin'] = $_POST['con_weixin'];
						if($_POST['con_telephone']) $contactsB['telephone'] = $_POST['con_telephone'];
						if($_POST['con_description']) $contactsB['description'] = $_POST['con_description'];
						if(!empty($contactsB)){
							$contactsB['creator_role_id'] = session('role_id');
							$contactsB['create_time'] = time();
							$contactsB['update_time'] = time();
							if(!$contactsB_id = M('ContactsB')->add($contactsB)){
								alert('error', L('ADD_THE_PRIMARY_CONTACT_FAILURE'), U('customerB/add'));
							}
						}
					}
					$m_customerB->create_time = time();
					$m_customerB->update_time = time();
					if($contactsB_id) $m_customerB->contactsB_id = $contactsB_id;
					$m_customerB->creator_role_id = session('role_id');
					if(!$customerB_id = $m_customerB->add()){
						alert('error', L('ADD_CUSTOMERB_FAILS_CONTACT_ADMIN'), U('customerB/add'));
					}
					$m_customerB_data->customerB_id = $customerB_id;
					$m_customerB_data->add();
					
					if ($_POST['leadsB_id']) {
						$leadsB_id = intval($_POST['leadsB_id']);
						$r_module = array(
							array('key'=>'log_id','r1'=>'RCustomerBLog','r2'=>'RLeadsBLog'), 
							array('key'=>'file_id','r1'=>'RCustomerBFile','r2'=>'RFileLeadsB'),
							array('key'=>'event_id','r1'=>'RCustomerBEvent','r2'=>'REventLeadsB'),
							array('key'=>'task_id','r1'=>'RCustomerBTask','r2'=>'RLeadsBTask')
						);
						
						foreach ($r_module as $key=>$value) {
							$key_id_array = M($value['r2'])->where('leadsB_id = %d', $leadsB_id)->getField($value['key'],true);
							$r1 = M($value['r1']);
							$data['customerB_id'] = $customerB_id;
							foreach($key_id_array as $k=>$v){
								$data[$value['key']] = $v;
								$r1->add($data);
							}
						}
						$leadsB_data['is_transformed'] = 1;
						$leadsB_data['update_time'] = time();
						$leadsB_data['customerB_id'] = $customerB_id;
						$leadsB_data['contactsB_id'] = $contactsB_id;
						$leadsB_data['transform_role_id'] = session('role_id');
						M('LeadsB')->where('leadsB_id = %d', $leadsB_id)->save($leadsB_data);
					}
					
					//记录操作记录
					actionLog($customerB_id);
					if ($contactsB_id && $customerB_id) {
						$rcc['contactsB_id'] = $contactsB_id;
						$rcc['customerB_id'] = $customerB_id;
						M('RContactsBCustomerB')->add($rcc);
					}
					if(intval($_POST['create_business1']) == 1 || intval($_POST['create_business1']) == 1){
						alert('success', L('ADD_CUSTOMERB_SUCCESS'), U('business/add','customerB_id='.$customerB_id));
					}else{
						if($_POST['submit'] == L('SAVE')) {
						    if($_POST['refer_url'])
							{
								if(strstr($_POST['refer_url'],'view'))
								{
								   alert('success', L('ADD_CUSTOMERB_SUCCESS'), U('customerB/index'));
								}
							   alert('success', L('ADD_CUSTOMERB_SUCCESS'), $_POST['refer_url']);
							}
							else{
							   alert('success', L('ADD_CUSTOMERB_SUCCESS'), U('customerB/index'));
							}
							
						} else {
							alert('success', L('ADD_CUSTOMERB_SUCCESS'), U('customerB/add'));
						}
					}
				}else{
					$this->error($m_customerB_data->getError());
				}
			}else{
				$this->error($m_customerB->getError());
            }
			
		}else{
			if(intval($_GET['leadsB_id'])){
				$leadsB = D('LeadsBView')->where('leadsB.leadsB_id = %d', intval($_GET['leadsB_id']))->find();
				$this->leadsB = $leadsB;
			}
            $this->field_list = field_list_html("edit","customerB",$leadsB);
//             dump(field_list_html("edit","customerB",$leadsB));
//             die;
			$this->refer_url=$_SERVER['HTTP_REFERER'];
            $alert = parseAlert();
            $this->alert = $alert;
            $this->display();
		}
	}
	
	public function delete(){
		$m_customerB = M('CustomerB');
		if ($this->isPost()) {
			$customerB_ids = is_array($_POST['customerB_id']) ? implode(',', $_POST['customerB_id']) : '';
			if ('' == $customerB_ids) {
				alert('error', L('HAVE_NOT_CHOOSE_ANY_CONTENT'), $_SERVER['HTTP_REFERER']);
			} else {
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				if($m_customerB->where('customerB_id in (%s)', $customerB_ids)->setField($data)){	
                    //记录操作记录
                    foreach($_POST['customerB_id'] as $customerB_id){
                        actionLog($customerB_id);
                    }					
					alert('success', L('DELETED_SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);					
				} else {
					alert('error', L('DELETE_FAILED_CONTACT_ADMIN'),$_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_GET['id']) {
			$customerB = $m_customerB->where('customerB_id = %d', $_GET['id'])->find();
			if (is_array($customerB)) {				
				if($customerB['owner_role_id'] == session('role_id') || session('?admin')){
					$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
					if($m_customerB->where('customerB_id = %d', $_GET['id'])->setField($data)){
                        actionLog($_GET['id']);		
						//判断客户是否属于客户池
						$outdays = M('config') -> where('name="customerB_outdays"')->getField('value');						
						$outdate = empty($outdays) ? time() : time()-86400*$outdays;								
						if($customerB['update_time'] < $outdate){						
							alert('success', L('DELETED_SUCCESSFULLY'),U('CustomerB/index','content=resource'));
						}else{							
							alert('success', L('DELETED_SUCCESSFULLY'),U('CustomerB/index'));
						}					
					}else{
						alert('error', L('DELETE_FAILED_CONTACT_ADMIN') ,$_SERVER['HTTP_REFERER']);
					}	
				} else {
					alert('error', L('HAVE_NOT_PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
					
			} else {
				alert('error', L('RECORD_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} else {
			alert('error', L('PLEASE_SELECT_A_CLUE_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function completeDelete() {
		$m_customerB = M('CustomerB');
		$r_module = array('Log'=>'RCustomerBLog', 'File'=>'RCustomerBFile', 'Event'=>'RCustomerBEvent', 'Task'=>'RCustomerBTask', 'RContactsBCustomerB');
		if (!session('?admin')) {
			alert('error', L('HAVE_NO_RIGHT_TO_DELETE_OPERATION'), $_SERVER['HTTP_REFERER']);
		}
		if ($this->isPost()) {
			$customerB_ids = is_array($_POST['customerB_id']) ? implode(',', $_POST['customerB_id']) : '';
			if ('' == $customerB_ids) {
				alert('error', L('HAVE_NOT_CHOOSE_ANY_CONTENT'), $_SERVER['HTTP_REFERER']);
			} else {
				if (!session('?admin')) {
					foreach($_POST['customerB_id'] as $key => $value){
						if(!$m_customerB->where('owner_role_id = %d and customerB_id = %d', session('role_id'), $value) -> find()){
							alert('error', L('DO_NOT_HAVE_PERMISSION_TO_OPERATE_ALL'), $_SERVER['HTTP_REFERER']);
						}else{
							actionLog($value);
						}
					}
				}
				if($m_customerB->where('customerB_id in (%s)', $customerB_ids)->delete()){	
                    M('CustomerBDate')->where('customerB_id in (%s)', $customerB_ids)->delete();
					foreach ($_POST['customerB_id'] as $value) {
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('customerB_id = %d', $value)->getField($key2 . '_id', true);
							M($value2)->where('customerB_id = %d', $value) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
					}
					alert('success', L('DELETED_SUCCESSFULLY'), U('CustomerB/index','by=deleted'));
				} else {
					alert('error', L('DELETE_FAILED_CONTACT_ADMIN'), $_SERVER['HTTP_REFERER']);
				}
			}
		} elseif($_GET['id']) {
			$customerB = $m_customerB->where('customerB_id = %d', $_GET['id'])->find();
			if (is_array($customerB)) {
				if($customerB['owner_role_id'] == session('role_id') || session('?admin')){
					if($m_customerB->where('customerB_id = %d', $_GET['id'])->delete()){
						actionLog($_GET['id']);
                        M('CustomerBDate')->where('customerB_id = %d', $_GET['id'])->delete();
						foreach ($r_module as $key2=>$value2) {
							$module_ids = M($value2)->where('customerB_id = %d', $_GET['id'])->getField($key2 . '_id', true);
							M($value2)->where('customerB_id = %d', $_GET['id']) -> delete();
							if(!is_int($key2)){
								M($key2)->where($key2 . '_id in (%s)', implode(',', $module_ids))->delete();
							}
						}
						alert('success', L('DELETED_SUCCESSFULLY'), U('CustomerB/index','by=deleted'));
					}else{
						alert('error', L('DELETE_FAILED_CONTACT_ADMIN'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('DO_NOT_HAVE_PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
					
			} else {
				alert('error', L('RECORD_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}			
		} else {
			alert('error', L('PLEASE_CHOOSE_TO_DELETE_CLUES!'),$_SERVER['HTTP_REFERER']);
		}
	}

	public function edit(){
        if(!check_permission(intval($this->_request('id')), 'customerB')) $this->error(L('HAVE NOT PRIVILEGES'));
		$customerB = D('CustomerBView')->where('customerB.customerB_id = %d',$this->_request('id'))->find();
		
		if (!$customerB) {
            alert('error', L('CUSTOMERB_DOES_NOT_EXIST!'),$_SERVER['HTTP_REFERER']);
        }
        $customerB['owner'] = D('RoleView')->where('role.role_id = %d', $customerB['owner_role_id'])->find();
        $customerB['contactsB_name'] = M('contactsB')->where('contactsB_id = %d', $customerB['contactsB_id'])->getField('name');
		
        $field_list = M('Fields')->where('model = "customerB"')->order('order_id')->select();
		
		if($this->isPost()){
			$m_customerB = D('CustomerB');
			$m_customerB_data = D('CustomerBData');
			foreach ($field_list as $v){
				switch($v['form_type']) {
					case 'address':
						$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
					break;
					case 'datetime':
						$_POST[$v['field']] = strtotime($_POST[$v['field']]);
					break;
					case 'box':
						eval('$field_type = '.$v['setting'].';');
						if($field_type['type'] == 'checkbox'){
							$_POST[$v['field']] = implode(chr(10),$_POST[$v['field']]);
						}
					break;
				}
			}
            
			if($m_customerB->create()){
				if($m_customerB_data->create()!==false){
					$m_customerB->update_time = time();
					$a = $m_customerB->where('customerB_id =%s ', $customerB['customerB_id'])->save();
					$b = $m_customerB_data->where('customerB_id =%s', $customerB['customerB_id'])->save();
					if($a !== false && $b !== false){
						if($_POST['contactsB_id'] && ($_POST['contactsB_id'] != $customerB['contactsB_id'])){
							$rcc['contactsB_id'] = intval($_POST['contactsB_id']);
							$rcc['customerB_id'] = $customerB['customerB_id'];
							if(!M('RContactsBCustomerB')->where($rcc)->find()){ 
								M('RContactsBCustomerB')->add($rcc);
							}
						}
						actionLog($customerB['customerB_id']);
						alert('success', L('EDIT_CLIENTS_SUCCESS'), U('customerB/index'));
						
					}else{
						alert('error', L('CUSTOMERB_EDITING_FAILED!'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->error($m_customerB_data->getError());
				}
            }else{
               $this->error($m_customerB->getError());
            }
		}else{
            $alert = parseAlert();
            $this->alert = $alert;
            $this->customerB = $customerB;
            $this->field_list = field_list_html("edit","customerB",$customerB);
            $this->display();
		}
		
	}
	
	public function index(){
		$d_v_customerB = D('CustomerBView');
        $by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId(true);
		$outdays = M('config') -> where('name="customerB_outdays"')->getField('value');
		$outdate = empty($outdays) ? time() : time()-86400*$outdays;	
		$where = array();
		$params = array();
		$order = "create_time desc";

		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		//查询关注
		$m_focus = M('customerBFocus');
		$focus_id = $m_focus ->where('user_id =%d',session('role_id'))->getField('customerB_id',true);
		//查询分享给我的
		$m_share =  M('customerBShare');
		$sharing_id = session('role_id');
		$m_customerB_share = $m_share ->select();
		foreach($m_customerB_share as $k=>$v){
			$by_sharing_id = explode(',',$v['by_sharing_id']);
			if(in_array($sharing_id,$by_sharing_id)){
				$customerBid[] = $v['customerB_id'];
			}
		}
		//查询我分享的
		$share_customerB_ids = $m_share ->where('share_role_id =%d',session('role_id'))->getField('customerB_id',true);
		
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d')) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc'; break;
			case 'update' : $order = 'update_time desc'; break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'deleted' : $where['is_deleted'] = 1;break;
			case 'me' : $where['owner_role_id'] = session('role_id'); break;
			case 'focus' : $where['customerB_id'] = array('in',$focus_id);break;
			case 'share' : $where['customerB_id'] = array('in',$customerBid);break;
			case 'myshare' : $where['customerB_id'] = array('in',$share_customerB_ids);break;
			default :
				if($this->_get('content') == 'resource'){
		            $where['_string'] = "customerB.owner_role_id=0 or customerB.update_time < $outdate";
		            $all_ids[] = "";
		            $where['owner_role_id'] = array('in', $all_ids);
					$where['is_locked'] = 0;
		        }else{
					$where['owner_role_id'] = array('in',implode(',', $all_ids));
		        }
			break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if (!isset($where['owner_role_id'])&&$by!='share') {
			if($by != 'deleted'){
				$where['owner_role_id'] = array('in', $all_ids);
			}
		}
		if($by == 'deleted') unset($where['update_time']);
		if($this->_get('content') != 'resource'){
			if($by != 'deleted'){
				$where['_string'] = 'update_time > '.$outdate.' OR is_locked = 1';
			}
		}

		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'name|origin|address|email|telephone|website|account_type|industry|annual_revenue|sic_code|ticker_symbol|ownership|rating|description' : 'name|origin|address|email|telephone|website|account_type|industry|annual_revenue|sic_code|ticker_symbol|ownership|rating|description|create_time|update_time';
			} else {
				$field = trim($_REQUEST['field']);
			}
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			
			$field_date = M('Fields')->where('is_main=1 and (model="" or model="customerB") and form_type="datetime"')->select();
			foreach($field_date as $v){
				if	($field == $v['field']) $search = is_numeric($search)?$search:strtotime($search);
			}
			
            if ($this->_request('state')){
				$search = $this->_request('state');
				if($this->_request('city')){
					$search .= chr(10) . $this->_request('city');
				}
				if($_REQUEST['search']){
					$search .= chr(10) .trim($_REQUEST['search']);
				}
			}
			 
			switch ($condition) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "not_start_with" :  $where[$field] = array('notlike',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			if($_GET['state']){
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition);
				$params[] = "state=".$_GET['state'];
			}else{
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$search);
			}
			if($_GET['city']){
				$params[] = "city=".$_GET['city'];
			}
		}
      
		if(trim($_GET['act'] == 'sms')){
			$customerB_ids = $d_v_customerB->where($where)->getField('customerB_id', true);
			$contactsB_ids = M('RContactsBCustomerB')->where('customerB_id in (%s)', implode(',', $customerB_ids))->getField('contactsB_id', true);
			$contactsB_ids = implode(',', $contactsB_ids);
			$contactsB = D('ContactsBView')->where('contactsB.contactsB_id in (%s)', $contactsB_ids)->select();
			$this->contactsB = $contactsB;
			$this->display('Setting:sendsms');
		}elseif(trim($_GET['act']) == 'excel'){		
			if(vali_permission('customerB', 'export')){
				$dc_id = $_GET['daochu'];
				if($dc_id !=''){
					$where['business_id'] = array('in',$dc_id);
				}
				$current_page = intval($_GET['current_page']);
				$export_limit = intval($_GET['export_limit']);
				$limit = ($export_limit*($current_page-1)).','.$export_limit;
				$customerBList = $d_v_customerB->where($where)->order($order)->limit($limit)->select();
				session('export_status', 1);
				$this->excelExport($customerBList);
			}else{
				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
			if($_GET['listrows']){
				$listrows = $_GET['listrows'];
				$params[] = "listrows=" . trim($_GET['listrows']);
			}else{
				$listrows = 15;
				$params[] = "listrows=15";
			}
			$list = $d_v_customerB->where($where)->order($order)->page($p.','.$listrows)->select();	
			$count = $d_v_customerB->where($where)->count();
			import("@.ORG.Page");
			$Page = new Page($count,$listrows);
			if (!empty($_GET['by'])) {
				$params[] = "by=" . trim($_GET['by']);
			}
			if (!empty($_GET['content'])) {
				$params[] = "content=" . trim($_GET['content']);
			}
			$this->parameter = implode('&', $params);
				
			if ($_GET['desc_order']) {
				$params[] = "desc_order=" . trim($_GET['desc_order']);
			} elseif($_GET['asc_order']){
				$params[] = "asc_order=" . trim($_GET['asc_order']);
			}
			$Page->parameter = implode('&', $params);
			$this->assign('page',$Page->show());

			if($by == 'deleted') {
				foreach ($list as $k => $v) {
					$list[$k]["delete_role"] = D('RoleView')->where('role.role_id = %d', $v['delete_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
				}
			} else {
				foreach ($list as $k => $v) {
					$days = 0;
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$days =  M('CustomerB')->where('customerB_id = %d', $v['customerB_id'])->getField('update_time');
					$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				}
			}	
			$this->listrows = $listrows;
			$this->customerBlist = $list;
			$this->assign('count',$count);
			$this->field_array = getIndexFields('customerB');
			$this->field_list = getMainFields('customerB');
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function client_index(){
		$d_v_customerB = D('CustomerBView');
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId(true);
		$outdays = M('config') -> where('name="customerB_outdays"')->getField('value');
		$outdate = empty($outdays) ? time() : time()-86400*$outdays;
		$where = array();
		$params = array();
		$order = "create_time desc";
	
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
	
		//查询关注
		$m_focus = M('customerBFocus');
		$focus_id = $m_focus ->where('user_id =%d',session('role_id'))->getField('customerB_id',true);
		//查询分享给我的
		$m_share =  M('customerBShare');
		$sharing_id = session('role_id');
		$m_customerB_share = $m_share ->select();
		foreach($m_customerB_share as $k=>$v){
			$by_sharing_id = explode(',',$v['by_sharing_id']);
			if(in_array($sharing_id,$by_sharing_id)){
				$customerBid[] = $v['customerB_id'];
			}
		}
		//查询我分享的
		$share_customerB_ids = $m_share ->where('share_role_id =%d',session('role_id'))->getField('customerB_id',true);
	
		switch ($by) {
			case 'today' : $where['create_time'] =  array('gt',strtotime(date('Y-m-d', time()))); break;
			case 'week' : $where['create_time'] =  array('gt',(strtotime(date('Y-m-d')) - (date('N', time()) - 1) * 86400)); break;
			case 'month' : $where['create_time'] = array('gt',strtotime(date('Y-m-01', time()))); break;
			case 'add' : $order = 'create_time desc'; break;
			case 'update' : $order = 'update_time desc'; break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'deleted' : $where['is_deleted'] = 1;break;
			case 'me' : $where['owner_role_id'] = session('role_id'); break;
			case 'focus' : $where['customerB_id'] = array('in',$focus_id);break;
			case 'share' : $where['customerB_id'] = array('in',$customerBid);break;
			case 'myshare' : $where['customerB_id'] = array('in',$share_customerB_ids);break;
			default :
				if($this->_get('content') == 'resource'){
					$where['_string'] = "customerB.owner_role_id=0 or customerB.update_time < $outdate";
					$all_ids[] = "";
					$where['owner_role_id'] = array('in', $all_ids);
					$where['is_locked'] = 0;
				}else{
					$where['owner_role_id'] = array('in',implode(',', $all_ids));
				}
				break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if (!isset($where['owner_role_id'])&&$by!='share') {
			if($by != 'deleted'){
				$where['owner_role_id'] = array('in', $all_ids);
			}
		}
		if($by == 'deleted') unset($where['update_time']);
		if($this->_get('content') != 'resource'){
			if($by != 'deleted'){
				$where['_string'] = 'update_time > '.$outdate.' OR is_locked = 1';
			}
		}
	
		if ($_REQUEST["field"]) {
			if (trim($_REQUEST['field']) == "all") {
				$field = is_numeric(trim($_REQUEST['search'])) ? 'name|origin|address|email|telephone|website|account_type|industry|annual_revenue|sic_code|ticker_symbol|ownership|rating|description' : 'name|origin|address|email|telephone|website|account_type|industry|annual_revenue|sic_code|ticker_symbol|ownership|rating|description|create_time|update_time';
			} else {
				$field = trim($_REQUEST['field']);
			}
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
				
			$field_date = M('Fields')->where('is_main=1 and (model="" or model="customerB") and form_type="datetime"')->select();
			foreach($field_date as $v){
				if	($field == $v['field']) $search = is_numeric($search)?$search:strtotime($search);
			}
				
			if ($this->_request('state')){
				$search = $this->_request('state');
				if($this->_request('city')){
					$search .= chr(10) . $this->_request('city');
				}
				if($_REQUEST['search']){
					$search .= chr(10) .trim($_REQUEST['search']);
				}
			}
	
			switch ($condition) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "not_start_with" :  $where[$field] = array('notlike',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			if($_GET['state']){
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition);
				$params[] = "state=".$_GET['state'];
			}else{
				$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.$search);
			}
			if($_GET['city']){
				$params[] = "city=".$_GET['city'];
			}
		}
	
		if(trim($_GET['act'] == 'sms')){
			$customerB_ids = $d_v_customerB->where($where)->getField('customerB_id', true);
			$contactsB_ids = M('RContactsBCustomerB')->where('customerB_id in (%s)', implode(',', $customerB_ids))->getField('contactsB_id', true);
			$contactsB_ids = implode(',', $contactsB_ids);
			$contactsB = D('ContactsBView')->where('contactsB.contactsB_id in (%s)', $contactsB_ids)->select();
			$this->ajaxReturn(array('list'=>$contactsB,'count'=>count($contactsB)), '', 1);
// 			$this->contactsB = $contactsB;
// 			$this->display('Setting:sendsms');
		}elseif(trim($_GET['act']) == 'excel'){
			if(vali_permission('customerB', 'export')){
				$dc_id = $_GET['daochu'];
				if($dc_id !=''){
					$where['business_id'] = array('in',$dc_id);
				}
				$current_page = intval($_GET['current_page']);
				$export_limit = intval($_GET['export_limit']);
				$limit = ($export_limit*($current_page-1)).','.$export_limit;
				$customerBList = $d_v_customerB->where($where)->order($order)->limit($limit)->select();
				session('export_status', 1);
				$this->ajaxReturn(array('list'=>$customerBList,'count'=>count($customerBList)), '', 1);
// 				$this->excelExport($customerBList);
			}else{
				$this->ajaxReturn(null, L('HAVE NOT PRIVILEGES'), 0);
// 				alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
			if($_GET['listrows']){
				$listrows = $_GET['listrows'];
				$params[] = "listrows=" . trim($_GET['listrows']);
			}else{
				$listrows = 15;
				$params[] = "listrows=15";
			}
			$list = $d_v_customerB->where($where)->order($order)->page($p.','.$listrows)->select();
			$count = $d_v_customerB->where($where)->count();
			import("@.ORG.Page");
			$Page = new Page($count,$listrows);
			if (!empty($_GET['by'])) {
				$params[] = "by=" . trim($_GET['by']);
			}
			if (!empty($_GET['content'])) {
				$params[] = "content=" . trim($_GET['content']);
			}
			$this->parameter = implode('&', $params);
	
			if ($_GET['desc_order']) {
				$params[] = "desc_order=" . trim($_GET['desc_order']);
			} elseif($_GET['asc_order']){
				$params[] = "asc_order=" . trim($_GET['asc_order']);
			}
			$Page->parameter = implode('&', $params);
			$this->assign('page',$Page->show());
	
			if($by == 'deleted') {
				foreach ($list as $k => $v) {
					$list[$k]["delete_role"] = D('RoleView')->where('role.role_id = %d', $v['delete_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
				}
			} else {
				foreach ($list as $k => $v) {
					$days = 0;
					$list[$k]["owner"] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
					$list[$k]["creator"] = D('RoleView')->where('role.role_id = %d', $v['creator_role_id'])->find();
					$days =  M('CustomerB')->where('customerB_id = %d', $v['customerB_id'])->getField('update_time');
					$list[$k]["days"] = $outdays-floor((time()-$days)/86400);
				}
			}
			$this->ajaxReturn(array('list'=>$list,'count'=>$count), '', 1);
// 			$this->listrows = $listrows;
// 			$this->customerBlist = $list;
// 			$this->assign('count',$count);
// 			$this->field_array = getIndexFields('customerB');
// 			$this->field_list = getMainFields('customerB');
// 			$this->alert = parseAlert();
// 			$this->display();
		}
	}

	public function listDialog(){
		$m_customerB = M('CustomerB');
		$m_contactsB = M('ContactsB');
		$m_r_contactsB_customerB = M('RContactsBCustomerB');
		$underling_ids = getSubRoleId();
		$business_id = intval($_GET['bid']);
		if(!empty($business_id)){
			$customerB_id = M('business')->where('business_id = %d',$business_id)->getField('customerB_id');
			$customerB = $m_customerB->where('customerB_id = %d and is_deleted = 0',$customerB_id)->order('create_time desc')->limit(10)->select();
		}else{
			$customerB = $m_customerB->where('owner_role_id in (%s) and is_deleted = 0',implode(',',$underling_ids))->order('create_time desc')->limit(10)->select();
		}
		foreach($customerB as $k=>$v){
			//如果存在首要联系人，则查出首要联系人。否则查出联系人中第一个。
			if(!empty($v['contactsB_id'])){
				$contactsB = $m_contactsB->where('is_deleted = 0 and contactsB_id = %d',$v['contactsB_id'])->find();
				$customerB[$k]['contactsB_name'] = $contactsB['name'];
			}else{
				$contactsB_customerB = $m_r_contactsB_customerB->where('customerB_id = %d',$v['customerB_id'])->limit(1)->order('id desc')->select();
				$contactsB = $m_contactsB->where('is_deleted = 0 and contactsB_id = %d',$contactsB_customerB[0]['contactsB_id'])->find();
				$customerB[$k]['contactsB_id'] = $contactsB['contactsB_id'];
				$customerB[$k]['contactsB_name'] = $contactsB['name'];
			}
		}
		
		$this->customerBList = $customerB;
		$count = $m_customerB->where('owner_role_id in (%s) and is_deleted = 0',implode(',',$underling_ids))->count();
		$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
		$data = getIndexFields('customerB');
		$this->count_num = $count;
		$this->field_num = sizeof($data)+1;
        $this->field_array = $data;
		$this->display();
	}

    
	public function view(){
		$customerB_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if (0 == $customerB_id) {
			alert('error', L('parameter_error'), U('customerB/index'));
		} else {
			if(!check_permission($customerB_id, 'customerB','by_sharing_id')){
				$this->error(L('HAVE NOT PRIVILEGES'));
			}
            //查询客户数据
			$customerB = D('CustomerBView')->where('customerB.customerB_id = %d', $customerB_id)->find();
            //取得字段列表
			$field_list = M('Fields')->where('model = "customerB"')->order('order_id')->select();
            //查询固定信息
			$customerB['owner'] = D('RoleView')->where('role.role_id = %d', $customerB['owner_role_id'])->find();
			$customerB['create'] = D('RoleView')->where('role.role_id = %d', $customerB['creator_role_id'])->find();
			if($customerB['contactsB_id']) $customerB['contactsB_name'] = M('contactsB')->where('contactsB_id = %d', $customerB['contactsB_id'])->getField('name');
            
            if($customerB['is_deleted'] == 1){
                $customerB['deleted'] = D('RoleView')->where('role.role_id = %d', $customerB['delete_role_id'])->find();
            }
			
			//合并客户、联系人附件
			$customerB_file_ids = M('rCustomerBFile')->where('customerB_id = %d', $customerB_id)->getField('file_id', true);
			$customerB_file_ids = $customerB_file_ids ? $customerB_file_ids : array();
			$contactsB_file_ids = M('rContactsBFile')->where('contactsB_id = %d', $customerB['contactsB_id'])->getField('file_id', true);
			$contactsB_file_ids = $contactsB_file_ids ? $contactsB_file_ids : array();
			$customerB['file'] = M('file')->where('file_id in (%s)',  implode(',', array_merge($customerB_file_ids,$contactsB_file_ids)))->select();
			$file_count = 0;
			foreach ($customerB['file'] as $key=>$value) {
				$customerB['file'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$file_count ++;
			}
			$customerB['file_count'] = $file_count;
			
			$task_ids = M('rCustomerBTask')->where('customerB_id = %d', $customerB_id)->getField('task_id', true);
			$customerB['task'] = M('task')->where('task_id in (%s) and is_deleted=0', implode(',', $task_ids))->select();
			$task_count = 0;
			foreach ($customerB['task'] as $key=>$value) {
				$customerB['task'][$key]['owner'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['owner_role_id'].'0')->select();
				$customerB['task'][$key]['about_roles'] = D('RoleView')->where('role.role_id in (%s)', '0'.$value['about_roles'].'0')->select();
				$task_count ++;
			}
			$customerB['task_count'] = $task_count;
			
			$event_ids = M('rCustomerBEvent')->where('customerB_id = %d', $customerB_id)->getField('event_id', true);
			$customerB['event'] = M('event')->where('event_id in (%s) and is_deleted=0', implode(',', $event_ids))->select();
			$event_count = 0;
			foreach ($customerB['event'] as $key=>$value) {
				$customerB['event'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['owner_role_id'])->find();
				$event_count ++;
			}
			$customerB['event_count'] = $event_count;
			
			$customerB['business'] = M('business')->where('customerB_id = %d and is_deleted=0', $customerB['customerB_id'])->select();
			$customerB['business_count'] = sizeof($customerB['business']);
			foreach($customerB['business'] as $k=>$v){
				$customerB['business'][$k]['owner'] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
				$customerB['business'][$k]['status'] = M('BusinessStatus')->where('status_id = %d', $v['status_id'])->getField('name');
				$business_id[] = $v['business_id'];
			}
			//合并客户、商机、联系人沟通日志
			$customerB_log_ids = M('rCustomerBLog')->where('customerB_id = %d', $customerB_id)->getField('log_id', true);
			$customerB_log_ids = $customerB_log_ids ? $customerB_log_ids : array();
			//商机日志
			$business_log_ids = M('rBusinessLog')->where('business_id in (%s)', implode(',', $business_id))->getField('log_id', true);
			$business_log_ids = $business_log_ids ? $business_log_ids : array();
			//联系人日志
			$contactsB_log_ids = M('rContactsBLog')->where('contactsB_id = %d', $customerB['contactsB_id'])->getField('log_id', true);
			$contactsB_log_ids = $contactsB_log_ids ? $contactsB_log_ids : array();
			$customerB['log'] = M('log')->where('log_id in (%s)', implode(',', array_merge($customerB_log_ids,$business_log_ids,$contactsB_log_ids)))->select();
			$log_count = 0;
			foreach ($customerB['log'] as $key=>$value) {
				$customerB['log'][$key]['owner'] = D('RoleView')->where('role.role_id = %d', $value['role_id'])->find();
				$log_count ++;
			}
			$customerB['log_count'] = $log_count;
			
			$customerB['receivables'] = D('ReceivablesView')->where('receivables.customerB_id = %d and receivables.is_deleted=0', $customerB['customerB_id'])->select();
			$customerB['receivables_count'] = $customerB['receivables'] ? sizeof($customerB['receivables']):0;
			foreach($customerB['receivables'] as $k=>$v){
				$customerB['receivables'][$k]['owner'] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
			}
			
			$customerB['payables'] = D('PayablesView')->where('payables.customerB_id = %d and payables.is_deleted=0', $customerB['customerB_id'])->select();
			$customerB['payables_count'] = $customerB['payables'] ? sizeof($customerB['payables']):0;
			foreach($customerB['payables'] as $k=>$v){
				$customerB['payables'][$k]['owner'] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
			}
			
			$customerB['cares'] = D('CaresView')->where('customerB_cares.customerB_id = %d', $customerB['customerB_id'])->select();
			$customerB['cares_count'] = sizeof($customerB['cares']);
			
			$customerB['contract'] = D('ContractView')->where('contract.business_id in (%s) and contract.is_deleted=0', implode(',', $business_id))->select();
			
			$customerB['contract_count'] = $customerB['contract'] ? sizeof($customerB['contract']):0;
			foreach($customerB['contract'] as $k=>$v){
				$customerB['contract'][$k]['owner'] = D('RoleView')->where('role.role_id = %d', $v['owner_role_id'])->find();
			}
			
			$customerB['product'] = D('BusinessProductView')->where('r_business_product.business_id in (%s)', implode(',', $business_id))->select();
			$customerB['product_count'] = $customerB['product'] ? sizeof($customerB['product']) : 0;
			
			$contactsB_ids = M('rContactsBCustomerB')->where('customerB_id = %d', $customerB_id)->getField('contactsB_id', true);
			$customerB['contactsB'] = M('contactsB')->where('contactsB_id in (%s) and is_deleted=0', implode(',', $contactsB_ids))->select();
			foreach($customerB['contactsB'] as $k=>$v){
				if(M('CustomerB')->where('contactsB_id = %d',$v['contactsB_id'])->select()){
					$customerB['contactsB'][$k]['is_firstContact'] = 'true';
				}else{
					$customerB['contactsB'][$k]['is_firstContact'] = 'false';
				}
			}

			$contactsB_count = M('contactsB')->where('contactsB_id in (%s) and is_deleted=0', implode(',', $contactsB_ids))->count();
			$customerB['contactsB_count'] = empty($contactsB_count)?0:$contactsB_count;
			
			//服务记录
			$service_ids = M('rServiceCustomerB')->where('customerB_id = %d', $customerB_id)->getField('service_id', true);
			$customerB['service'] = M('service')->where('service_id in (%s) and is_deleted=0', implode(',', $service_ids))->select();
			foreach($customerB['service'] as $k=>$v){
				if(M('CustomerB')->where('service_id = %d',$v['service_id'])->select()){
					$customerB['service'][$k]['is_firstContact'] = 'true';
				}else{
					$customerB['service'][$k]['is_firstContact'] = 'false';
				}
			}
			
			$service_count = M('service')->where('service_id in (%s) and is_deleted=0', implode(',', $service_ids))->count();
			$customerB['service_count'] = empty($service_count)?0:$service_count;
			
			$customerB_len = strlen($customerB['name']);
			$this ->customerB_len =$customerB_len;
			$this->customerB = $customerB;
			
            $this->field_list = $field_list;
			$this->alert = parseAlert();
			$this->display();
		}
	}
	
	/**
	 * 客户导出
	 *
	 **/
	public function excelExport($customerBList=false){
		C('OUTPUT_ENCODE', false);
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("crm");
		$objProps->setLastModifiedBy("crm");    
		$objProps->setTitle("crm CustomerB");    
		$objProps->setSubject("crm CustomerB Data");    
		$objProps->setDescription("crm CustomerB Data");    
		$objProps->setKeywords("crm CustomerB Data");    
		$objProps->setCategory("crm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
        $field_list = M('Fields')->where('model = \'customerB\'')->order('order_id')->select();
        foreach($field_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
        }
		$mark_customerB_ascii = $ascii;
		$mark_customerB_cv = $cv;
		//联系人字段
		$contactsB_fields_list = array();
		$contactsB_fields_list[0]['field'] = 'name';
		$contactsB_fields_list[0]['name'] = '联系人姓名';
		$contactsB_fields_list[1]['field'] = 'saltname';
		$contactsB_fields_list[1]['name'] = '尊称';
		$contactsB_fields_list[2]['field'] = 'post';
		$contactsB_fields_list[2]['name'] = '职位';
		$contactsB_fields_list[3]['field'] = 'telephone';
		$contactsB_fields_list[3]['name'] = '电话';
		$contactsB_fields_list[4]['field'] = 'email';
		$contactsB_fields_list[4]['name'] = '邮件';
		$contactsB_fields_list[5]['field'] = 'qq';
		$contactsB_fields_list[5]['name'] = 'qq';
		$contactsB_fields_list[6]['field'] = 'zip_code';
		$contactsB_fields_list[6]['name'] = '邮编';
		$contactsB_fields_list[7]['field'] = 'address';
		$contactsB_fields_list[7]['name'] = '联系地址';
		$contactsB_fields_list[8]['field'] = 'description';
		$contactsB_fields_list[8]['name'] = '备注';
		
		foreach($contactsB_fields_list as $field){
			$objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
		}
		$mark_contactsB_ascii = $ascii;
		$mark_contactsB_cv = $cv;
		
		if(is_array($customerBList)){
			$list = $customerBList;
		}else{
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId()));
			$where['is_deleted'] = 0;
			$list = M('CustomerB')->where($where)->select();
		}
        
		$i = 2;
		foreach ($list as $k => $v) {
            $date = M('CustomerBData')->where("customerB_id = $v[customerB_id]")->find();
            if(!empty($date)){
                $v = $v+$date;
            }
			$i++;
            $ascii = 65;
            $cv = '';
            foreach($field_list as $field){
                if($field['form_type'] == 'datetime'){
                    $objActSheet->setCellValue($cv.chr($ascii).$i, date('Y-m-d',$v[$field['field']]));
                }elseif($field['form_type'] == 'number' || $field['form_type'] == 'floatnumber' || $field['form_type'] == 'phone' || $field['form_type'] == 'mobile' || ($field['form_type'] == 'text' && is_numeric($v[$field['field']]))){
					//防止使用科学计数法，在数据前加空格
					$objActSheet->setCellValue($cv.chr($ascii).$i, ' '.$v[$field['field']]);
				}else{
                    $objActSheet->setCellValue($cv.chr($ascii).$i, $v[$field['field']]);
                }
                $ascii++;
                if($ascii == 91){
                    $ascii = 65;
                    $cv .= chr(strlen($cv)+65);
                }
            }
		
			//联系人
			$mark_ascii = $ascii;
			$mark_cv = $cv;
			$m_contactsB = M('contactsB');
			$m_rContactsBCustomerB = M('rContactsBCustomerB');
			$contactsBIdArr = $m_rContactsBCustomerB->where('customerB_id = %d', $v['customerB_id'])->getField('contactsB_id',true);
			$contactsB_list = $m_contactsB->field('name,saltname,post,telephone,email,qq,zip_code,address,description')->where(array('contactsB_id'=>array('in',$contactsBIdArr)))->select();
			if($contactsB_list){
				foreach($contactsB_list as $val){
					foreach($contactsB_fields_list as $valu){
						//防止使用科学计数法，在数据前加空格
						if($valu['field'] == 'telephone' || $valu['field'] =='qq'){
							$objActSheet->setCellValue($cv.chr($ascii).$i, ' '.$val[$valu['field']]);
						}else{
							$objActSheet->setCellValue($cv.chr($ascii).$i, $val[$valu['field']]);
						}
						
						$ascii++;
						if($ascii == 91){
							$ascii = 65;
							$cv .= chr(strlen($cv)+65);
						}
					}
					$ascii = $mark_ascii;
					$cv = $mark_cv;
					$i++;
				}
			//$ascii--;
				$i--;
			}
		}
		$objActSheet->mergeCells('A1:'.$mark_customerB_cv.chr($mark_customerB_ascii-1).'1');
		$objActSheet->mergeCells($mark_customerB_cv.chr($mark_customerB_ascii).'1'.':'.$mark_contactsB_cv.chr($mark_contactsB_ascii).'1');
		$objActSheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');
		$objActSheet->getStyle('A1')->getAlignment()->setWrapText(true);
		$objActSheet->getStyle($mark_customerB_cv.chr($mark_customerB_ascii).'1')->getFont()->getColor()->setARGB('FFFF0000');
		$objActSheet->getStyle($mark_customerB_cv.chr($mark_customerB_ascii).'1')->getAlignment()->setWrapText(true);
        $objActSheet->setCellValue('A1', L('CUSTOMERB_INFO'));
        $objActSheet->setCellValue($mark_customerB_cv.chr($mark_customerB_ascii).'1', L('CONTACTSB_INFO'));
		//设置背景色
		$objActSheet->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet->getStyle('A1')->getFill()->getStartColor()->setARGB('F5DEB3');
		$objActSheet->getStyle($mark_customerB_cv.chr($mark_customerB_ascii).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet->getStyle($mark_customerB_cv.chr($mark_customerB_ascii).'1')->getFill()->getStartColor()->setARGB('FFFFE0');
		
		$current_page = intval($_GET['current_page']);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=crm_customerB_".date('Y-m-d',mktime())."_".$current_page.".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
		session('export_status', 0);
	}
	public function getCurrentStatus(){
		$this->ajaxReturn(intval(session('export_status')), 'success', 1);
		
	}
	
	/**
	*  客户导入
	*
	**/
	public function excelImport(){
   
		$m_customerB = D('CustomerB');
		$m_customerB_data = D('CustomerBData');
		if($this->isPost()){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS_TO_UPLOAD_DIRECTORY_CANNOT_WRITE'), $_SERVER['HTTP_REFERER']);
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), $_SERVER['HTTP_REFERER']);
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD_FAILED'), $_SERVER['HTTP_REFERER']);
			}
			import("ORG.PHPExcel.PHPExcel");
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($savePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
			}
			$PHPExcel = $PHPReader->load($savePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$allRow = $currentSheet->getHighestRow();
			
			if ($allRow <= 2) {
				alert('error', L('UPLOAD_A_FILE_WITHOUT_A_VALID_DATA'), $_SERVER['HTTP_REFERER']);   
			} else {
                $field_list = M('Fields')->where('model = \'customerB\'')->order('order_id')->select();
				for($currentRow = 3;$currentRow <= $allRow;$currentRow++){
					$data = array();
					$data['owner_role_id'] = intval($_POST['owner_role_id']);
					$data['creator_role_id'] = session('role_id');
					$data['create_time'] = time();
					$data['update_time'] = time();
//                     $ascii = 65;
//                     $cv = '';
                    $i=1;
                    foreach($field_list as $field){
                        //$info = (String)$currentSheet->getCell($cv.chr($ascii).$currentRow)->getValue();
                        // if ($field['is_main'] == 1){
                            // $data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(PHPExcel_Shared_Date::ExcelToPHP($info))-8*60*60 : $info;
                        // }else{
                            // $data_date[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? intval(PHPExcel_Shared_Date::ExcelToPHP($info))-8*60*60 : $info;
                        // }
                        
                    	//把$cv.chr($ascii)换成ToNumberSystem26($i)
						$cell =$currentSheet->getCell(ToNumberSystem26($i).$currentRow);
						$info = $cell->getValue();
						if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
							$cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();

							//formatcode 为 yyyy/m 时间格式
							$formatcode=$cellstyleformat->getFormatCode();
							if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
							    $info=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($info));
							}else{
								$info=PHPExcel_Style_NumberFormat::toFormattedString($info,$formatcode);
							}
						}else{
							$info = (String)$cell->getCalculatedValue();
						}
						if ($field['is_main'] == 1){
                            $data[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? strtotime($info) : $info;
                        }else{
                            $data_date[$field['field']] = ($field['form_type'] == 'datetime' && $info != null) ? strtotime($info) : $info;
                        }
						$i++;
//                         $ascii++;
//                         if($ascii == 91){
//                             $ascii = 65;
//                             $cv .= chr(strlen($cv)+65);
//                         }
                    }
					//联系人字段
					$contactsB_fields_list = array();
					$contactsB_fields_list[0]['field'] = 'name';
					$contactsB_fields_list[1]['field'] = 'saltname';
					$contactsB_fields_list[2]['field'] = 'post';
					$contactsB_fields_list[3]['field'] = 'telephone';
					$contactsB_fields_list[4]['field'] = 'email';
					$contactsB_fields_list[5]['field'] = 'qq';
					$contactsB_fields_list[6]['field'] = 'zip_code';
					$contactsB_fields_list[7]['field'] = 'address';
					$contactsB_fields_list[8]['field'] = 'description';
					
					foreach($contactsB_fields_list as $field){
						//把$cv.chr($ascii)换成ToNumberSystem26($i)
						$info = (String)$currentSheet->getCell(ToNumberSystem26($i).$currentRow)->getValue();
						$contactsB_data[$field['field']] = $info;
						$i++;
// 						$ascii++;
//                         if($ascii == 91){
//                             $ascii = 65;
//                             $cv .= chr(strlen($cv)+65);
//                         }
					}
                    if ($m_customerB->create($data) && $m_customerB_data->create($data_date)) {
                        $customerB_id = $m_customerB->add();
                        $m_customerB_data->customerB_id = $customerB_id;
                        $m_customerB_data->add();
						//添加联系人
						$m_contactsB = M('contactsB');
						$contactsB_data['creator_role_id'] = intval($_POST['owner_role_id']);
						$contactsB_data['create_time'] = time();
						$contactsB_id = $m_contactsB->add($contactsB_data);
						//添加客户联系人（客户联系人关系表）
						$m_rContactsBCustomerB = M('rContactsBCustomerB');
						$m_rContactsBCustomerB->add(array('contactsB_id'=>$contactsB_id, 'customerB_id'=>$customerB_id));
						//设置首要联系人
						$m_customerB->where('customerB_id = %d', $customerB_id)->setField('contactsB_id', $contactsB_id);
						
					}else{
						if($this->_post('error_handing','intval',0) == 0){
							alert('error', L('ERROR INTRODUCED INTO THE LINE',array($currentRow,$m_customerB->getError().$m_customerB_data->getError())), $_SERVER['HTTP_REFERER']);
						}else{
							$error_message .= L('LINE ERROR',array($currentRow,$m_customerB->getError().$m_customerB_data->getError()));
							$m_customerB->clearError();
							$m_customerB_data->clearError();
						}
                    }
				}
               
				alert('success', $error_message .L('IMPORT_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->display();
		}
	}
	
	/**
	*  客户导入模板下载
	*
	**/
    public function excelImportDownload(){
		C('OUTPUT_ENCODE', false);
        import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("crm");
		$objProps->setLastModifiedBy("crm");    
		$objProps->setTitle("crm CustomerB");    
		$objProps->setSubject("crm CustomerB Data");    
		$objProps->setDescription("crm CustomerB Data");    
		$objProps->setKeywords("crm CustomerB Data");    
		$objProps->setCategory("crm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
        $ascii = 65;
        $cv = '';
        $field_list = M('Fields')->where('model = \'customerB\' ')->order('order_id')->select();
        $contactsB_fields_list = array();
		$contactsB_fields_list[0]['name'] = '姓名';
		$contactsB_fields_list[1]['name'] = '尊称';
		$contactsB_fields_list[2]['name'] = '职位';
		$contactsB_fields_list[3]['name'] = '电话';
		$contactsB_fields_list[4]['name'] = '邮件';
		$contactsB_fields_list[5]['name'] = 'QQ';
		$contactsB_fields_list[6]['name'] = '邮编';
		$contactsB_fields_list[7]['name'] = '联系地址(长文本)';
		$contactsB_fields_list[8]['name'] = '备注';
		
        foreach($field_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
			$mark_customerB_cv = $cv;
			$mark_customerB_ascii = $ascii;
        }
		foreach($contactsB_fields_list as $field){
            $objActSheet->setCellValue($cv.chr($ascii).'2', $field['name']);
            $ascii++;
            if($ascii == 91){
                $ascii = 65;
                $cv .= chr(strlen($cv)+65);
            }
			$mark_contactsB_cv = $cv;
			$mark_contactsB_ascii = $ascii;
        }
        $objActSheet->mergeCells('A1:'.$mark_customerB_cv.chr($mark_customerB_ascii-1).'1');
		$objActSheet->mergeCells($mark_customerB_cv.chr($mark_customerB_ascii).'1'.':'.$mark_contactsB_cv.chr($mark_contactsB_ascii).'1');
		$objActSheet->getRowDimension('1')->setRowHeight(50);
		$objActSheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');
		$objActSheet->getStyle('A1')->getAlignment()->setWrapText(true);
		$objActSheet->getStyle($mark_customerB_cv.chr($mark_customerB_ascii).'1')->getFont()->getColor()->setARGB('FFFF0000');
		$objActSheet->getStyle($mark_customerB_cv.chr($mark_customerB_ascii).'1')->getAlignment()->setWrapText(true);
        $content = L('ADRESS');
        $objActSheet->setCellValue('A1', $content);
		$objActSheet->setCellValue($mark_customerB_cv.chr($mark_customerB_ascii).'1', L('FIRST_CONTACTSB_INFO'));
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=crm_customerB.xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
    }
	
	public function revert(){
		$customerB_id = isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if ($customerB_id > 0) {
			$m_customerB = M('customerB');
			$customerB = $m_customerB->where('customerB_id = %d', $customerB_id)->find();
			if ($customerB['delete_role_id'] == session('role_id') || session('?admin')) {
				if (isset($customerB['is_deleted']) || $customerB['is_deleted'] == 1) {
					if ($m_customerB->where('customerB_id = %d', $customerB_id)->setField('is_deleted', 0)) {
						alert('success', L('RESTORE_SUCCESSFUL'), $_SERVER['HTTP_REFERER']);
					} else {
						alert('error', L('RESTORE_FAILURE'), $_SERVER['HTTP_REFERER']);
					}
				} else {
					alert('error', L('ALREADY_REDUCTION!'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('HAVE_NO_PERMISSION_TO_RESTORE!'), $_SERVER['HTTP_REFERER']);
			}
		} else {
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function getCustomerBList(){	
		$idArray = getSubRoleId();
		$idArray[] = session("role_id");
		
		//获取下级和自己的客户列表,搜索
		$customerBList = M('customerB')->where('owner_role_id in (%s) and is_deleted = 0', implode(',', $idArray))->select();
		$this->assign('customerBlist',$list);

		$this->ajaxReturn($customerBList, '', 1);
	}

	//客户关怀列表
	public function cares(){
		$m_cares = M('CustomerBCares');
		$below_ids = getSubRoleId(false); 
		$all_ids = getSubRoleId();
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$where = array();
		$params = array();
		
		$order = "create_time desc";
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		switch ($by) {
			case 'me' : $where['owner_role_id'] = session('role_id'); break;
			case 'sub' : $where['owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'today' :
				$where['care_time'] = array('between',array(strtotime(date('Y-m-d')) -1 ,strtotime(date('Y-m-d')) + 86400));
				break;
			case 'week' : 
				$week = (date('w') == 0)?7:date('w');
				$where['care_time'] = array('between',array(strtotime(date('Y-m-d')) - ($week-1) * 86400 -1 ,strtotime(date('Y-m-d')) + (8-$week) * 86400));
				break;
			case 'month' : 
				$beginThismonth = mktime(0,0,0,date('m'),1,date('Y'));
				$endThismonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
				$where['care_time'] = array('between',array($beginThismonth ,$endThismonth));
				break;
			case 'email' : $where['type'] = 'email'; break;
			case 'phone' : $where['type'] = 'phone';  break;
			case 'add' : $order = 'create_time desc';  break;
			case 'update' : $order = 'update_time desc';  break;
			case 'message' : $where['type'] = 'message';  break;
			case 'other' : $where['type'] = 'other';  break;
			default : $where['owner_role_id'] = array('in',implode(',', $all_ids)); break;
		}
		if ($by != 'deleted') {
			$where['is_deleted'] = array('neq',1);
		}
		if ($by != 'me' && $by != 'sub') {
			$where['owner_role_id'] = array('in',implode(',', getSubRoleId(true))); 
		}
		
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'subject|description' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('create_time' == $field || 'update_time' == $field || 'care_time' == $field) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($_REQUEST['condition']) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.$field, 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
		}
		
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$list = $m_cares->where($where)->order($order)->page($p.',2')->select();
		$count = $m_cares->where($where)->count();		
		import("@.ORG.Page");
		$Page = new Page($count,2);
		$params[] = 'a=cares';
		$params[] = 'by='.$_GET['by'];
		$this->parameter = implode('&', $params);

		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}
		
		$Page->parameter = implode('&', $params);
		$show = $Page->show();
		$this->assign('page',$show);

		foreach ($list as $k => $v) {
			$list[$k]["customerB"] = M('customerB')->where('customerB_id = %d', $v['customerB_id'])->find();
			$list[$k]["creator"] = getUserByRoleId($v['creator_role_id']);
			$list[$k]["owner"] = getUserByRoleId($v['owner_role_id']);
		}
		$this->assign('caresList',$list);
		$this->alert = parseAlert();
		$this->display();
	}
	public function caresAdd(){
		$m_customerB = M('CustomerB');
		$m_contactsB = M('ContactsB');
		$m_r_contactsB_customerB = M('RContactsBCustomerB');
		if($this->isPost()){
			$m_cares = M('CustomerBCares');
			if($m_cares->create()){
				if(!$_POST['subject']) $this->error(L('CARE_SUBJECT_CANNOT_BE_EMPTY!'));
				if(!$_POST['care_time']) $this->error(L('CARE_CARETIME_CANNOT_BE_EMPTY'));
				if($_POST['care_time']) $m_cares->care_time = strtotime($_POST['care_time']);
				$m_cares->create_time = time();
				$m_cares->update_time = time();
				$m_cares->creator_role_id = session('role_id');
				if($m_cares->add()){
					if($_POST['submit'] == L('SAVE')){
					    if($_POST['refer_url'])
						{
						    alert('success', L('ADD_SUCCESSFUL'), $_POST['refer_url']);
						}
						alert('success', L('ADD_SUCCESSFUL'), U('customerB/cares'));
					}else{
						alert('success', L('Add_successful'), U('customerB/caresadd'));
					}
				}else{
					alert('error', L('ADD_FAILURE_PLEASE_CONTACT_YOUR_ADMIN'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->error($m_cares->getError());
			}
		}else{
			$customerB_id = $_GET['customerB_id'];
			$m_customerB = M('CustomerB');
			$customerB = $m_customerB->where('customerB_id = %d',$customerB_id)->find();
			if(!empty($customerB['contactsB_id'])){
				$contactsB = $m_contactsB->where('is_deleted = 0 and contactsB_id = %d',$customerB['contactsB_id'])->find();
				$customerB['contactsB_name'] = $contactsB['name'];
			}else{
				$contactsB_customerB = $m_r_contactsB_customerB->where('customerB_id = %d',$customerB['customerB_id'])->limit(1)->order('id desc')->select();
				$contactsB = $m_contactsB->where('is_deleted = 0 and contactsB_id = %d',$contactsB_customerB[0]['contactsB_id'])->find();
				$customerB['contactsB_id'] = $contactsB['contactsB_id'];
				$customerB['contactsB_name'] = $contactsB['name'];
			}
			$this->customerB = $customerB;
			$this->refer_url=$_SERVER['HTTP_REFERER'];
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function caresEdit(){
		$care_id = $_POST['care_id'] ? intval($_POST['care_id']) : intval($_GET['id']);
		if($care_id && !check_permission($care_id, 'customerBCares')) $this->error(L('HAVE NOT PRIVILEGES'));
		
		if ($this->isPost()) {
			$m_cares = M('CustomerBCares');
			if($m_cares->create()){
				if(!$_POST['subject']) alert('error', L('CARE_SUBJECT_CANNOT_BE_EMPTY'), $_SERVER['HTTP_REFERER']);
				if($_POST['care_time']) $m_cares->care_time = strtotime($_POST['care_time']);
				$m_cares->update_time = time();
				if($m_cares->save()){
					alert('success', L('MODIFY_THE_SUCCESS'), U('customerB/cares'));
				}else{
					alert('error', L('MODIFY_THE_SUCCESS'), $_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->error($m_cares->getError());
			}
		} else {
			if($care_id>0){
				$m_care = M('CustomerBCares');
				$care = $m_care->where('care_id = %d', $care_id)->find();
				$care['owner'] = getUserByRoleId($care['owner_role_id']);
				$care['customerB'] = M('customerB')->where('customerB_id = %d', $care['customerB_id'])->find();
				$care['contactsB'] = M('contactsB')->where('contactsB_id = %d', $care['contactsB_id'])->find();
				$this->care = $care;
				$this->alert = parseAlert();
				$this->display();
			}else{
				alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function caresView(){
		$care_id = intval($_GET['id']);
		if($care_id && !check_permission($care_id, 'customerBCares')) $this->error(L('HAVE NOT PRIVILEGES'));
		
		if($care_id>0){
			$m_care = M('CustomerBCares');
			$care = $m_care->where('care_id = %d', $_GET['id'])->find();
			if (is_array($care)) {
				
				$care = $m_care->where('care_id = %d', $care_id)->find();
				$care['owner'] = getUserByRoleId($care['owner_role_id']);
				$care['customerB'] = M('customerB')->where('customerB_id = %d', $care['customerB_id'])->find();
				$care['contactsB'] = M('contactsB')->where('contactsB_id = %d', $care['contactsB_id'])->find();
				$this->care = $care;
				$this->alert = parseAlert();
				$this->display();
			} else {
				alert('error', L('RECORD_NOT_EXIST'), U('customerB/cares'));
			}	
		}else{
			alert('error',L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	public function caresDelete(){
		$m_cares = M('CustomerBCares');
		if ($this->isPost()) {
			// foreach($_POST['care_id'] as $k => $v){
				// if($m_cares->where('care_id = %d', $v['care_id'])->getField('owner_role_id') != session('role_id')){
					// alert('error', '您没有全部的权限', U('leadsB/index'));
				// }
			// }
			$care_id = is_array($_POST['care_id']) ? implode(',', $_POST['care_id']) : '';
			if ('' == $care_id) {
				alert('error', L('HAVE_NOT_CHOOSE_ANY_CONTENT'), U('customerB/cares'));
			} else {
				if($m_cares->where('care_id in (%s)', $care_id)->delete()){					
					alert('success', L('DELETED_SUCCESSFULLY'),U('customerB/cares'));
				} else {
					alert('error', L('DELETE_FAILED_CONTACT_ADMIN'), U('customerB/cares'));
				}
			}
		} elseif($_GET['id']) {
			$care = $m_cares->where('care_id = %d', $_GET['id'])->find();
			if (is_array($care)) {
				if ($care['owner_role_id'] == session('role_id') || session('?admin')) {			
					if($m_cares->where('care_id = %d', $_GET['id'])->delete()){
						alert('success', L('DELETED_SUCCESSFULLY'), U('customerB/cares'));
					}else{
						alert('error', L('DELETE_FAILED_CONTACT_ADMIN'), U('customerB/cares'));
					}
				} else {
					alert('error', L('DO_NOT_HAVE_PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
			} else {
				alert('error', L('RECORD_NOT_EXIST'), U('customerB/cares'));
			}
		} else {
			alert('error', L('PLEASE_SELECT_A_CLUE_TO_DELETE'),$_SERVER['HTTP_REFERER']);
		}
	}
	
	public function analytics(){
		$m_customerB = M('CustomerB');
		if($_GET['role']) {
			$role_id = intval($_GET['role']);
		}else{
			$role_id = 'all';
		}
		if($_GET['department'] && $_GET['department'] != 'all'){
			$department_id = intval($_GET['department']);
		}else{
			$department_id = D('RoleView')->where('role.role_id = %d', session('role_id'))->getField('department_id');
		}
		if($_GET['start_time']) $start_time = strtotime(date('Y-m-d',strtotime($_GET['start_time'])));
		$end_time = $_GET['end_time'] ?  strtotime(date('Y-m-d 23:59:59',strtotime($_GET['end_time']))) : strtotime(date('Y-m-d 23:59:59',time()));
		if($role_id == "all") {
			$roleList = getRoleByDepartmentId($department_id);
			$role_id_array = array();
			foreach($roleList as $v2){
				$role_id_array[] = $v2['role_id'];
			}
			$where_role_id = array('in', implode(',', $role_id_array));
			$where_source['creator_role_id'] = $where_role_id;
			$where_industry['owner_role_id'] = $where_role_id;
			$where_renenue['creator_role_id'] = $where_role_id;
			$where_employees['creator_role_id'] = $where_role_id;
		}else{
			$where_source['creator_role_id'] = $role_id;
			$where_industry['owner_role_id'] = $role_id;
			$where_renenue['creator_role_id'] = $role_id;
			$where_employees['creator_role_id'] = $role_id;
		}
		if($start_time){
			$where_create_time = array(array('elt',$end_time),array('egt',$start_time), 'and');
			$where_source['create_time'] = $where_create_time;
			$where_industry['create_time'] = $where_create_time;
			$where_renenue['create_time'] = $where_create_time;
			$where_employees['create_time'] = $where_create_time;
			
		}else{
			$where_source['create_time'] = array('elt',$end_time);
			$where_industry['create_time'] = array('elt',$end_time);
			$where_renenue['create_time'] = array('elt',$end_time);
			$where_employees['create_time'] = array('elt',$end_time);
		}
		
		//统计表内容
		$role_id_array = array();
		if($role_id == "all"){
			if($_GET['department'] != 'all'){
				if(session('?admin')){
					$roleList = M('role')->where('user_id <> 0')->getField('role_id',true);
				}else{
					$roleList = getRoleByDepartmentId($department_id);
				}
				foreach($roleList as $v){
					$role_id_array[] = $v;
				}
			}else{
				$role_id_array = getSubRoleId();
			}
		}else{
			$role_id_array[] = $role_id;
		}
		if($start_time){
			$create_time= array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$create_time = array('elt',$end_time);
		}
		$add_count_total = 0;
		$own_count_total = 0;
		$success_count_total = 0;
		$deal_count_total = 0;
		$busi_customerB_array = M('Business')->getField('customerB_id', true);
		$busi_customerB_id=implode(',', $busi_customerB_array);
		foreach($role_id_array as $v){
			$user = getUserByRoleId($v);
			$add_count = $m_customerB->where(array('is_deleted'=>0, 'creator_role_id'=>$v, 'create_time'=>$create_time))->count();
			$own_count = $m_customerB->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$success_count = $m_customerB->where(array('is_deleted'=>0, 'customerB_id'=>array('in', $busi_customerB_id),'owner_role_id'=>$v, 'create_time'=>$create_time))->count();
			$reportList[] = array("user"=>$user,"add_count"=>$add_count,"own_count"=>$own_count,"success_count"=>$success_count);
			$add_count_total += $add_count;
			$own_count_total += $own_count;
			$success_count_total += $success_count;
		}
		
		//来源统计图
		$source_count_array = array();
		$setting = M('Fields')->where("model = 'customerB' and field = 'source'")->getField('setting');
		$setting_str = '$sourceList='.$setting.';';
		eval($setting_str);
		$source_total_count = 0;
		foreach($sourceList['data'] as $v){
			unset($where_source['origin']);
			$where_source['origin'] = $v;
			$target_count = $m_customerB ->where($where_source)->count();
			$source_count_array[] = '['.'"'.$v.'",'.$target_count.']';
			$source_total_count += $target_count;
		}
		$source_count_array[] = '["'.L('OTHER').'",'.($add_count_total-$source_total_count).']';
		$this->source_count = implode(',', $source_count_array);

		
		
		//客户行业统计图
		$industry_count_array = array();
		$setting = M('Fields')->where("model = 'customerB' and field = 'leixing'")->getField('setting');
		$setting_str = '$industryList='.$setting.';';
		eval($setting_str);
		$where_industry['is_deleted'] = 0;
		$industry_total_count = 0;
		foreach($industryList['data'] as $v){
			unset($where_employees['industry']);
			$where_industry['industry'] = $v;
			$target_count = $m_customerB ->where($where_industry)->count();
			$industry_total_count += $target_count;
			$industry_count_array[] = '["'.$v.'",'.$target_count.']';
		}
		$industry_count_array[] = '["'.L('OTHER').'",'.($add_count_total-$industry_total_count).']';
		$this->industry_count = implode(',', $industry_count_array);
		//客户员工数统计
		$employees_count_array = array();
		$setting = M('Fields')->where("model = 'customerB' and field = 'no_of_employees'")->getField('setting');
		$setting_str = '$no_List='.$setting.';';
		eval($setting_str);
		$where_employees['is_deleted'] = 0;
		$no_total_count = 0;
		foreach($no_List['data'] as $v){
			unset($where_employees['no_of_employees']);
			$where_employees['no_of_employees'] = $v;
			$target_count = $m_customerB ->where($where_employees)->count();
			$no_total_count+=$target_count;
			$employees_count_array[] = '["'.$v.'",'.$target_count.']';
		}
		$employees_count_array[] = '["'.L('OTHER').'",'.($add_count_total-$no_total_count).']';
		$this->employees_count = implode(',', $employees_count_array);	
		//客户营业额统计
// 		$revenue_count_array = array();
// 		$setting = M('Fields')->where("model = 'customerB' and field = 'annual_revenue'")->getField('setting');
// 		$setting_str = '$revenueList='.$setting.';';
// 		eval($setting_str);
// 		$where_renenue['is_deleted'] = 0;
// 		$revenue_total_count = 0; 
// 		foreach($revenueList['data'] as $v){
// 			unset($where_renenue['annual_revenue']);
// 			$where_renenue['annual_revenue'] = $v;
// 			$target_count = $m_customerB ->where($where_renenue)->count();
// 			$revenue_count_array[] = '['.'"'.$v.'",'.$target_count.']';
// 			$revenue_total_count+=$target_count;
// 		}
// 		$revenue_count_array[] = '["'.L('OTHER').'",'.($add_count_total-$target_count).']';
// 		$this->revenue_count = implode(',', $revenue_count_array);
		
		
		
		$this->total_report = array("add_count"=>$add_count_total, "own_count"=>$own_count_total, "success_count"=>$success_count_total);
		$this->reportList = $reportList;
		if (session('?admin')){
			$idArray = M('role')->where('user_id <> 0')->getField('role_id',true);
		}else{
			$idArray = getSubRoleId();	
		}
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		
		$departments = M('roleDepartment')->select();
		$departmentList[] = M('roleDepartment')->where('department_id = %d', session('department_id'))->find();
		$departmentList = array_merge($departmentList, getSubDepartment(session('department_id'),$departments,''));
		$this->assign('departmentList', $departmentList);
		$this->alert = parseAlert();
		$this->display();
	}
	
	//检查用户是否符合领取或被分配到客户池资源资格
	//type 1：领取 2：分配
	public function check_customerB_limit($user_id, $type){	
		$m_config = M('config');
		$m_customerB_record = M('customerB_record');
		$customerB_limit_condition = $m_config->where('name = "customerB_limit_condition"')->getField('value');			
		
		$today_begin = strtotime(date('Y-m-d',time()));
		$today_end = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$this_week_begin = ($today_begin -((date('w'))-1)*86400);
		$this_week_end = ($today_end+(7-(date('w')==0?7:date('w')))*86400); 
		$this_month_begain = strtotime(date('Y-m', time()).'-01 00:00:00');
		$this_month_end = mktime(23,59,59,date('m'),date('t'),date('Y'));
		
		$condition['user_id'] = $user_id;
		$condition['type'] = $type;
		if($customerB_limit_condition == 'day'){
			$condition['start_time'] = array('between', array($today_begin, $today_end)); 
		}elseif($customerB_limit_condition == 'week'){
			$condition['start_time'] = array('between', array($this_week_begin, $this_week_end));
		}elseif($customerB_limit_condition == 'month'){
			$condition['start_time'] = array('between', array($this_month_begain, $this_month_end));
		}
		
		$customerB_record = $m_customerB_record->where($condition)->count();
		return $customerB_record;
	}
	
	public function customerBlock(){
		if(intval($_GET['customerB_id'])){
			$m_customerB = M('CustomerB');
			$customerB = $m_customerB->where('customerB_id = %d ', intval($_GET['customerB_id']))->find();
			if(!empty($customerB)){
				if(!in_array(session('role_id'), getSubRoleId(true)) && !session('?admin'))
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				if($customerB['is_locked']){
					if($m_customerB->where('customerB_id = %d ', intval($_GET['customerB_id']))->setField('is_locked',0)){
						alert('success', L('UNLOCKING_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('UNLOCKING_FAILD'), $_SERVER['HTTP_REFERER']);
					}
				}else{
					if($m_customerB->where('customerB_id = %d ', intval($_GET['customerB_id']))->setField('is_locked',1)){
						alert('success', L('LOCKING_SUCCESS'), $_SERVER['HTTP_REFERER']);
					}else{
						alert('error', L('UNLOCKING_FAILD'), $_SERVER['HTTP_REFERER']);
					}
				}
			}else{
				alert('error', L('RECORD_NOT_EXIST'), $_SERVER['HTTP_REFERER']);
			}
		}else{
			alert('error', L('PARAMETER_ERROR'), $_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * 首页客户来源统计
	 * @ level 0:自己的数据  1:自己和下属的数据
	 **/
	public function getCustomerBOriginal	(){
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$where['owner_role_id'] = array('in',getSubRoleId());
		
		$m_customerB = M('customerB');
		$original = $m_customerB->Distinct(true)->field('origin')->getField('origin',true);
		$originalArr = array_filter($original);
		
		$customerBArr = array();
		$where['is_deleted'] = array('eq',0);
		foreach($originalArr as $v){
			$where['origin'] = array('eq',$v);
			$origin_count = $m_customerB ->where($where)->count();
			$customerBArr['series'][] = array('value'=>intval($origin_count), 'name'=>$v);
			$customerBArr['legend'][] = $v;
		}
		$this->ajaxReturn($customerBArr,'success',1);
	}
	//客户分享
	public function share(){
		if($this->isPost()){
			$m_share =M('customerBShare');
			$customerB_ids = explode(',',$_POST['customerB_id']);
			$m_customerB_share = $m_share->select();
			$sharing_id = session('role_id');
			foreach($m_customerB_share as $k=>$v){
				$by_sharing_id = explode(',',$v['by_sharing_id']);
				if(in_array($sharing_id,$by_sharing_id)){
					$customerBid[] = $v['customerB_id'];
				}
			}
			foreach($customerB_ids as $ko=>$vo){
				$is_share = in_array($vo,$customerBid);
				if($is_share !=''){
					$is_shares[] = $is_share;
				}
			}
			if($is_shares !=''){
				$this->error('不能重复分享');
			}
			
			$customerBs_ids = explode(',',$_POST['customerB_id']);
			$to_role = implode(',',$_POST['to_role_id']);
			$i = 0;
			foreach($customerBs_ids as $vo){
				$data['share_role_id'] = session('role_id');
				$data['by_sharing_id'] = $to_role;
				$data['customerB_id'] = $vo;
				$data['share_time']  = time();
				$m_share -> add($data);
				$i++;
			}
			if($i > 0){
				alert('success','共享成功！',$_SERVER["HTTP_REFERER"]);
			}else{
				alert('error','共享失败！',$_SERVER["HTTP_REFERER"]);
			}
		}else{
			$d_role = D('RoleView');
			$customerB_ids = $_GET['customerB_id'];
			$departments_list = M('roleDepartment')->select();	
			foreach($departments_list as $k=>$v){
				$roleList = $d_role->where('position.department_id = %d', $v['department_id'])->select();
				$departments_list[$k]['user'] = $roleList;
			}
			$this->customerB_id = $customerB_ids;
			$this->departments_list = $departments_list;
			$this->display();
		}
	}
	public function close_share(){
		if($this->isPost()){
			$m_share = M('customerBShare');
			$customerB_ids = is_array($_POST['customerB_id']) ? implode(',', $_POST['customerB_id']) : '';
			if (empty($customerB_ids)) {
				alert('error', L('HAVE_NOT_CHOOSE_ANY_CONTENT'), $_SERVER['HTTP_REFERER']);
			} 
			else {
				$is_deleted = $m_share ->where('customerB_id in (%s)',$customerB_ids)->delete();
				if($is_deleted){
					alert('success','关闭共享成功！',$_SERVER["HTTP_REFERER"]);
				}else{
					alert('error','关闭共享失败！',$_SERVER["HTTP_REFERER"]);
				}
			}
		}elseif($_GET['customerB_id']){
			$m_share = M('customerBShare');
			$customerB_id = $_GET['customerB_id'];
			if (empty($customerB_id)) {
				alert('error','参数错误', $_SERVER['HTTP_REFERER']);
			} 
			else {
				$is_deleted = $m_share ->where('customerB_id = %d',$customerB_id)->delete();
				if($is_deleted){
					alert('success','关闭共享成功！',$_SERVER["HTTP_REFERER"]);
				}else{
					alert('error','关闭共享失败！',$_SERVER["HTTP_REFERER"]);
				}
			}
		}
	}
	
	public function batchfocus(){
		if($this->isPost()){
			$m_focus = M('customerBFocus');
			$customerB_ids = $_POST['customerB_id'];
			if('' == $customerB_ids){
				alert('error', L('NOT_CHOOSE_ANY'), $_SERVER['HTTP_REFERER']);
			}	
			$i=0;
			foreach($customerB_ids as $v){
				if($m_focus->where('customerB_id = "%s" and user_id ="%d"', $v,session('role_id'))->count() <= 0){
					$data['customerB_id'] = $v;
					$data['user_id'] = session('role_id');
					$data['focus_time'] = time();
					$m_focus ->add($data);
					$i++;
				}
			}
			if($i > 0){
				alert('success', L('FOCUS_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', '关注失败', $_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['customerB_id']){
			$m_focus = M('customerBFocus');
			$customerB_id = $_GET['customerB_id'];
			if('' == $customerB_id){
				alert('error','参数错误', $_SERVER['HTTP_REFERER']);
			}	
			$i=0;
			if($m_focus->where('customerB_id = "%s" and user_id ="%d"', $customerB_id,session('role_id'))->count() <= 0){
				$data['customerB_id'] = $customerB_id;
				$data['user_id'] = session('role_id');
				$data['focus_time'] = time();
				$m_focus ->add($data);
				$i = 1;
			}
			if($i > 0){
				alert('success', L('FOCUS_SUCCESS'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', '关注失败', $_SERVER['HTTP_REFERER']);
			}
		}
	}
	public function batchclose(){
		if($this->isPost()){
			$m_focus = M('customerBFocus');
			$customerB_ids = $_POST['customerB_id'];
			if('' == $customerB_ids){
				alert('error', L('NOT_CHOOSE_ANY'), $_SERVER['HTTP_REFERER']);
			}	
			$i=0;
			foreach($customerB_ids as $v){
				if($m_focus->where('customerB_id = "%s" and user_id ="%d"', $v,session('role_id'))->count() > 0){
					$m_focus ->where('customerB_id = "%s" and user_id ="%d"', $v,session('role_id'))->delete();
					$i++;
				}
			}
			if($i >0){
				alert('success',L('CANCEL_THE_ATTENTION'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('CANCEL_THE_ATTENTION_ERROR'), $_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['customerB_id']){
			$m_focus = M('customerBFocus');
			$customerB_id = $_GET['customerB_id'];
			if('' == $customerB_id){
				alert('error', '参数错误', $_SERVER['HTTP_REFERER']);
			}	
			$i=0;
			if($m_focus->where('customerB_id = "%d" and user_id ="%d"', $customerB_id,session('role_id'))->count() > 0){
				$m_focus ->where('customerB_id = "%d" and user_id ="%d"', $customerB_id,session('role_id'))->delete();
				$i = 1;
			}
			if($i >0){
				alert('success',L('CANCEL_THE_ATTENTION'), $_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('CANCEL_THE_ATTENTION_ERROR'), $_SERVER['HTTP_REFERER']);
			}
		}
	}
}