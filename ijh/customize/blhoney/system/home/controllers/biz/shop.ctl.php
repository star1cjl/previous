<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Biz_Shop extends Ctl_Biz
{
    

    public function index()
    {
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'title,info,addr,lat,lng,logo,phone,yy_status,yy_stime,yy_ltime')){
                $this->msgbox->add('非法的数据提交', 211);
            }else{
                if($attach = $_FILES['shop_logo']){
                    if(UPLOAD_ERR_OK == $attach['error']){
                        if($a = K::M('magic/upload')->upload($attach, 'shop', $this->shop['logo'])){
                            $data['logo'] = K::M('content/html')->encode($a['photo']);
                        }
                    }
                }
   
                if($data['lat'] == '' || $data['lng'] == ''){
                    $this->msgbox->add('经纬度必须设置', 212);
                }else if(K::M('shop/shop')->update($this->shop_id, $data)){
                    $this->msgbox->add('修改商铺资料成功');
                }
            }
        }else{
            $stime = mktime(0,0,0,date("m",time()),date("d",time()),date("Y",time()));
            $etime = mktime(23,59,59,date("m",time()),date("d",time()),date("Y",time()));
            $list = array();
            for($start = $stime; $start<=$etime; $start+=3600) {
               $times[] = date('H:i',$start);
            }
            $this->pagedata['times'] = $times;
            $this->tmpl = 'biz/shop/index.html';
        }
    }

    public function passwd()
    {
        if($data = $this->checksubmit('data')){
            $session =K::M('system/session')->start();
            if(!$data['passwd']){
                $this->msgbox->add('旧密码不能为空', 211);
            }else if(!$data['new_passwd']){
                $this->msgbox->add('新密码不能为空', 212);
            }else if(!$data['new_passwd2']){
                $this->msgbox->add('确认密码不能为空', 213);
            }else if($data['new_passwd'] != $data['new_passwd2']){
                $this->msgbox->add('两次新密码输入不一致', 214);
            }else if(md5($data['passwd']) != $this->shop['passwd']){
                $this->msgbox->add('旧密码不正确', 215);
            }else if($data['passwd'] == $data['new_passwd']){
                $this->msgbox->add('新密码不能和旧密码一致', 216);
            }else{
                $new_passwd = md5($data['new_passwd']);
                if(K::M('shop/shop')->update($this->shop_id,array('passwd'=>$new_passwd))){
                    $this->msgbox->add('修改登录密码成功');
                }        
            }
        }else{
            $this->tmpl = 'biz/shop/passwd.html';
        }        
    }
    
    public function mobile()
    {
        $session = K::M('system/session')->start();
        if($data = $this->checksubmit('data')){
            if($passwd = $data['passwd']){
                $this->msgbox->add('密码不能为空', 211);
            }else if(md5($passwd) != $this->shop['passwd']){
                $this->msgbox->add('登录密码不正确', 212);
            }else if(!$mobile = $data['mobile']){
                $this->msgbox->add('手机号不能为空', 213);
            }else if(!$mobile = K::M('verify/check')->mobile($mobile)){
                $this->msgbox->add('手机号格式不正确', 214);
            }else if($mobile == $this->shop['mobile']){
                $this->msgbox->add('新手机号与旧手机号相同', 215);
            }else if(!$sms_code = $data['code']){
                $this->msgbox->add('验证码不能为空', 216);
            }else if($sms_code != $session->get('code_'.$mobile)){
                $this->msgbox->add('验证码不正确', 217);
            }else if(K::M('shop/shop')->update_mobile($this->shop_id, $mobile)){
                $session->delete('code_'.$mobile);
                $this->msgbox->add('修改手机成功');
            }
        }else{
            $this->pagedata['mobile'] = $this->shop['mobile'];
            $this->tmpl = 'biz/shop/mobile.html';
        }        
    }

    public function account()
    {
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'account_type,account_name,account_number')){
                $this->msgbox->add('非法的数据提交', 211);
            }else if($account_info = K::M('shop/account')->detail($this->shop_id)){
                K::M('shop/account')->update($this->shop_id, $data);
                $this->msgbox->add('修改提现帐号成功');
            }else{
                $data['shop_id'] = $this->shop_id;
                K::M('shop/account')->create($data);
                $this->msgbox->add('添加提现帐号成功');
            }
        }else{
            $this->pagedata['account_info'] = K::M('shop/account')->detail($this->shop_id);
            $this->pagedata['bank_list'] = K::M('data/data')->bank_list();
            $this->tmpl = 'biz/shop/account.html';
        }          
    }
    
    
    public function pei(){
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'min_amount,freight,pei_distance,pei_type,pei_amount')){
                $this->msgbox->add('非法的数据提交', 211);
            }else{
                K::M('shop/shop')->update($this->shop_id,$data);
                $this->msgbox->add('配送设置成功');
            }
        }else{
            $this->pagedata['detail'] = $this->shop;
            $this->tmpl = 'biz/shop/pei.html';
        }          
    }

    public function youhui()
    {
        if($data = $this->checksubmit('data')){
            $datas = array();
            foreach ($data['order_amount'] as $k=>$val){
                if(($a = (float)$val) && ($b = (float)$data['youhui_amount'][$k])){
                    $datas[$a] = $b;
                }
            }
            if(K::M('shop/youhui')->update_youhui($this->shop_id, $datas)){
               $this->msgbox->add('优惠保存成功');
            }  
        }else{
            $filter = array('shop_id'=>$this->shop_id);
            if($items = K::M('shop/youhui')->items($filter, null, $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($shop_id, '{page}')));
                $this->pagedata['items'] = $items;
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'biz/shop/youhui.html';
        }
    }
    
    
    public function yhcreate()
    {
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'order_amount,youhui_amount,orderby')){
                $this->msgbox->add('非法的数据提交', 211);
            }else{
                $data['shop_id'] = $this->shop_id;
                if($youhui_id = K::M('shop/youhui')->create($data)){
                    K::M('shop/shop')->change_youhui($shop_id);
                     $this->msgbox->add('添加优惠成功');
                   $this->msgbox->set_data('forward',  $this->mklink('biz/shop/youhui'));
                } 
            }
        }else{
            $this->tmpl = 'biz/shop/yhcreate.html';
        }          
    }
    
    
    public function yhedit()
    {
       if($data = $this->checksubmit('data')){
            $data['shop_id'] = $this->shop_id;
            $datas = array();
            foreach ($data['order_amount'] as $k=>$val){
                foreach($data['youhui_amount'] as $kk=>$v){
                   if($val&&$data['youhui_amount'][$k]){
                      $datas[$val] = $data['youhui_amount'][$k];
                   } 
                }
            }
            //print_r($datas);die;
            if($youhui_id = K::M('shop/youhui')->update_youhui($this->shop_id,$datas)){
               $this->msgbox->add('优惠保存成功');
            }  
        }
    }
    
    public function yhdelete()
    {
        if($youhui_id = (int)$this->GP('youhui_id')){
            if(!$detail = K::M('shop/youhui')->detail($youhui_id)){
                $this->msgbox->add('你要删除的优惠不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else{
                if(K::M('shop/youhui')->delete($youhui_id)){
                    K::M('shop/shop')->change_youhui($detail['shop_id']);
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else{
            $this->msgbox->add('未指定要删除的优惠ID', 401);
        }
    }  

    // 轮播设置
    public function pic()
    {
        $this->pagedata['pics'] = K::M('shop/pic')->items(array('shop_id'=>$this->shop_id),array('pic_id'=>'desc'),1,100,$count);
        $this->tmpl = 'biz/shop/pic/index.html';
    }   

    // 添加轮播
    public function createpic()
    {
        if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'pic')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data['shop_id'] = $this->shop_id;
            if($pic_id = K::M('shop/pic')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward',  $this->mklink('biz/shop:pic'));
            } 
        }else{
           $this->tmpl = 'biz/shop/pic/create.html';
        }   
    }

    // 编辑轮播
    public function editpic($photo_id)
    {
        if(!$photo_id = (int)$photo_id){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('shop/pic')->detail($photo_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'shop')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            if(K::M('shop/pic')->update($photo_id, $data)){
                $this->msgbox->add('修改内容成功');
            }   
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'biz/shop/pic/edit.html';
        }
    }

    // 删除轮播
    public function delpic($photo_id)
    {
        if($photo_id = (int)$photo_id){
            if(!$detail = K::M('shop/pic')->detail($photo_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 213);
            }else{
                if(K::M('shop/pic')->delete($photo_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}