<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Passport extends Ctl
{

    public function signup($params)
    {
        if(!$params['mobile']){
            $this->msgbox->add('手机号不能为空',211);
        }else if(!$mobile = K::M('verify/check')->mobile($params['mobile'])){
            $this->msgbox->add('手机号不正确',212);
        }else if(!$sms_code = $params['sms_code']){
            $this->msgbox->add('短信验证码不能为空',213);
        }else if(K::M('system/session')->start()->get('code_'.$mobile) != $sms_code){
            $this->msgbox->add('短信验证码不正确',213);
        }else if(!$passwd = $params['passwd']){
            $this->msgbox->add('登录密码不正确',214);
        }else if($member = K::M('member/member')->member($mobile, 'mobile')){
            $this->msgbox->add('该手机号已经注册',215);
        }else{
            $data = array('mobile'=>$mobile, 'passwd'=>$passwd);
            $data['paypasswd'] = $passwd;
            $data['nickname'] = substr($mobile, 0,3).'****'.substr($mobile, -4);
            if($pmid = preg_match('/^(M|P|D|S)\d+/i', $params['pmid'])){
                $data['pmid'] = $pmid;
            }
            if($uid = K::M('member/account')->create($data)){
                if($this->system->auth->manager($uid)){
                    $member = $this->filter_fields('uid,nickname,face,mobile', $this->auth->member);
                    $this->msgbox->add('success');
                    $member['token'] = $this->auth->token;
                    $this->msgbox->set_data('data', $member);
                }
            }
        }
    }

    public function login($params)
	{

        if(!$params['mobile']){
            $this->msgbox->add('手机号不能为空',211);
        }else if(!$mobile = K::M('verify/check')->mobile($params['mobile'])){
            $this->msgbox->add('手机号不正确', 212);
        }else if(!$member = K::M('member/member')->member($mobile, 'mobile')){
            $this->msgbox->add('该手机号未注册过',213);
        }else if($member['closed'] == 1){
            $this->msgbox->add('账户已被删除',215);
        }else if($passwd = $params['passwd']){
            if(md5($passwd) != $member['passwd']){
                $this->msgbox->add('登录密码不正确',214);
            }else if($this->auth->manager($member['uid'])){
                $member = $this->filter_fields('uid,nickname,face,mobile',$member);
                $this->msgbox->add('success');
                $member['token'] = $this->auth->token;
                $this->msgbox->set_data('data', $member);
            }
        }else if($sms_code = $params['sms_code']){
            $session = K::M('system/session')->start();
            if($sms_code != $session->get('code_'.$params['mobile'])){
                $this->msgbox->add('短信验证码不正确',215);
            }else if($this->auth->manager($member['uid'])){
                $member = $this->filter_fields('uid,nickname,face,mobile',$member);
                $this->msgbox->add('success');
                $member['token'] = $this->auth->token;
                $this->msgbox->set_data('data', $member);
                $session->delete('code_'.$mobile);
            }
        }else{
            $this->msgbox->add('参数传递有误',216);
        }
    }

    public function wxlogin($params)
    {
        if($wxid = $params['wx_unionid']){
            $wx_field = 'wx_unionid'; 
        }else if($wxid = $params['wx_openid']){
            $wx_field = 'wx_openid'; 
        }
        if(!$wxid){
           $this->msgbox->add('微信授权失败',211);  
        }else if($member = K::M('member/member')->member($wxid, $wx_field)){
            if($this->auth->manager($member['uid'])){
                $data = $this->filter_fields('uid,nickname,face,mobile', $member);
                $this->msgbox->add('success');
                $data['token'] = $this->auth->token;
                $data['wxtype'] = 'wxlogin';
                $data['wx_openid'] = $wx_openid;
                $data['wx_unionid'] = $wx_unionid;
                $this->msgbox->set_data('data', $data);                
            }
        }else{
            $data = array('wxtype'=>'wxbind', 'uid'=>0, 'nickname'=>'', 'face'=>'', 'mobile'=>'', 'token'=>'', 'wx_openid'=>$wx_openid);
            $this->msgbox->set_data('data', $data);
        }
    }

    public function wxbind($params)
    {
        $wx_unionid = $params['wx_unionid'];
        $wx_openid = $params['wx_openid'];
        $session = K::M('system/session')->start();
        if(!$params['mobile']){
            $this->msgbox->add('手机号不能为空',211);
        }else if(!$mobile = K::M('verify/check')->mobile($params['mobile'])){
            $this->msgbox->add('手机号不正确',212);
        }else if(!$sms_code = $params['sms_code']){
            $this->msgbox->add('短信验证码不能为空',213);
        }else if(K::M('system/session')->start()->get('code_'.$mobile) != $sms_code){
            $this->msgbox->add('短信验证码不正确',214);
        }else if($member = K::M('member/member')->member($mobile, 'mobile')){
            K::M('member/member')->update($member['uid'],array('wx_openid'=>$wx_openid, 'wx_unionid'=>$wx_unionid));
            if($this->auth->manager($member['uid'])){
                $data = $this->filter_fields('uid,nickname,face,mobile', $member);
                $this->msgbox->add('success');
                $data['token'] = $this->auth->token;
                $this->msgbox->set_data('data', $data);
            }
        }else{
            $data = array('mobile'=>$mobile, 'wx_openid'=>$wx_unionid,'wx_unionid'=>$wx_openid);
            if(!$nickname = $params['wx_nickname']){
                $nickname = substr($mobile, 3).'***'.substr($mobile, -4);
            }
            $data['nickname'] = $nickname;
            $data['paypasswd'] = md5(uniqid());
            $data['passwd'] = md5(uniqid());
            if($uid = K::M('member/account')->create($data)){
                if($wx_headimgurl = $params['wx_headimgurl']){
                    if($face = file_get_contents($wx_headimgurl)){
                        K::M('member/face')->update_face($uid, '', $face);
                    }
                }
                if($this->auth->manager($uid)){
                    $data = $this->filter_fields('uid,nickname,face,mobile', $this->auth->member);
                    $this->msgbox->add('success');
                    $data['token'] = $this->auth->token;
                    $data['line'] = __LINE__;
                    $this->msgbox->set_data('data', $data);
                }
            }
        }
    }

    public function forgot($params)
    {
        $session = K::M('system/session')->start();
        if(!$params['mobile']){
            $this->msgbox->add('手机号不能为空',211);
        }else if(!$mobile = K::M('verify/check')->mobile($params['mobile'])){
            $this->msgbox->add('手机号不正确',212);
        }else if($session->get('code_'.$mobile) != $params['sms_code']){
            $this->msgbox->add('短信验证码不正确',213);
        }else if(!$new_passwd = $params['new_passwd']){
            $this->msgbox->add('登录密码不正确',214);
        }else if(!$member = K::M('member/member')->member($mobile, 'mobile')){
            $this->msgbox->add('该手机号未注册过',215);
        }else if(K::M('member/member')->up_passwd($member['uid'], $new_passwd)) {
			K::M("system/logs")->log("up_passwd",$member['uid']);
            $this->msgbox->set_data('data', array('uid'=>$member['uid']));
        }
    }

}