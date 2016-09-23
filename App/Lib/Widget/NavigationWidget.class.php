<?php 

class NavigationWidget extends Widget 
{
	public function render($data)
	{
		$user = M('User');
		$navigation = M('Navigation');
		$permission = M('Permission');
		$value = $user->where("user_id = %d", session('user_id'))->getField('navigation');
		$menu = unserialize($value);
		
		//要在导航栏可调顺序，就要把user表中某人的默认navigation设置为空，然后再在$navigation的搜索条件中加上'listorder asc'
		//即首要顺序是user表中某人的默认navigation，次要顺序是系统设置中的系统菜单设置
		$list = $navigation->order('listorder asc')->select();
		$controls = $permission->where('position_id = %d', session('position_id'))->getField('url', true);
		foreach($list AS $value) {
			if(empty($value['module']) or in_array(strtolower($value['module']).'/index', $controls) or session('?admin')){
				$navigationList[$value['id']] = $value;
			}
		}
		foreach($menu AS $k=>$v) {
			foreach($v AS $kk=>$vv) {
				if (isset($navigationList[$vv])) {
					$menu[$k][$kk] = $navigationList[$vv];
					unset($navigationList[$vv]);
				} else {
					unset($menu[$k][$kk]);
				}
			}
		}
		
		foreach($navigationList AS $value) {
			$menu[$value['postion']][] = $value;
		}
		
		$menu['simple'] =unserialize(M('User')->where('user_id = %d', session('user_id'))->getField('simple_menu'));
		return $this->renderFile ("index", $menu);
	}
}
