<?php
/*
 *Error Code
 *  1: No input
 *  2: Out of length
 *  3: Can't use character.
 *  5: $password !== $password2
 *
*/

class Common {
    
    var $error = array();
    var $params = array(
            'site_name',
            'site_url',
            'host',
            'site_comment',
            'dbname',
            'password',
            'password2',
            'username',
            //'adapter',
        );
    
    /*
    * フォームパラメーターの受け取り
    */
    function getParams(){
        $params = array();
    
        foreach ( $this->params as $value ){
            if ( !empty($_POST[$value]) ){
                $params[$value] = $_POST[$value];
                $this->error[$value] = 0;
            }
            else {
                $params[$value] = '';
                $this->error[$value] = 1;
            }
            
        }
        $this->checkPassword($params['password'], $params['password2']);
        
        return $params;
    }
    
    public function isParam($params){
        $flg = false;
        foreach ( $params as $key ){
          if ( !empty($params[$key]) ){
            $flg = true;
          }
        }
        return $flg;
    }
    
    public function isError()
    {
        foreach ( $this->error as $key => $value ){
          if ( !empty($this->error[$key]) ){
            return true;
          }
        }
        return false;
    }
    
    public function getErrorArray()
    {
        return $this->error;
    }
    
    //
    public function checkUrlFormat($url)
    {
    /*
        if ( preg_match("/^http(s):\/\/
                                        ([][])+\/
                                        ()+$
        /x", $url) ){
            return false;
        }
        */
        return true;
    }
        
    //パスワードの入力チェック
    public function checkPassword($password, $password2 = null)
    {
        //第一引数に値が無ければエラー
        if ( empty($password) ){
            $this->error['password'] = 1;
            return false;
        }
        
        //第一引数に使用不可能な文字列が利用されていればエラー
        /*
        if ( !preg_match("//", $password) ){
            $this->error['password'] = 3;
            return false;
        }
        */
        
        //
        if ( !empty($password2) ){
            //第二引数に値が無ければエラー
            if ( $password !== $password2 ){
                $this->errors['password'] = 5;
                return false;
            }
            
             //第二引数に使用不可能な文字列が利用されていればエラー
            if ( !preg_match("//", $password2) ){
                $this->error['password'] = 3;
                return false;
            }
        }
        
        if ( $password === $password2 ){
            return true;
        }
        
   }
   
   //
   public function getDefaultParams()
   {
   
    $params = array(
            'site_name' => 'サイト名',
            'site_url'  => 'http://',
            'host'      => 'localhost',
            'site_comment' => 'サイトの説明です。',
            'dbname'    => '',
            'username'  => '',
            'password'  => '',
            //'adapter' => '',
        );
    
    return $params;
   }
   
   //
   public function clearError()
   {
        foreach ( $this->params as $value ){
            $this->error[$value] = '';
        }
   }
   //リファラーの取得
   public function getReferer()
   {
    return $_SERVER['REFERER'];
   }
   
   public function getInitializeTablesSql()
   {
     
     $comment_flg = false;
     $fp = fopen('../sql/initialize_tables.sql', 'r');
     while ( $line = fgets($fp) ){
     // MySQLスキーマのファイル内を走査しつつ、コメントは除外して抽出
       if ( $comment_flg === true ){
         
         if ( preg_match("/\*\//", $line) ){
           $comment_flg = false;
         }
         
       }
       else {
         
         if ( preg_match("/\/\*/", $line) ){
           $comment_flg = true;
         }
         elseif ( preg_match("/^\-\-/", $line) ){
           // なにもしない
         }
         else {
           $query .= $line;
         }
         
       }
     }
     fclose($fp);
     
     // コメントを除いたスキーマを1行づつのクエリーに分割して配列に格納
     $querys = explode(";", $query);
     return $querys;
   }

}
