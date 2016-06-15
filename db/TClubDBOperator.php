<?php
require_once 'DBOperator.php';
class TClubDBOperator
{
    private $odb;

    public function  __construct()
    {
    	$this->odb = new DBOperator('tclub');
    }
    public function __destruct()
    {
    	$this->odb = null;
    }
    
    public function getUserData($username)
    {
      $sql = "SELECT * FROM user WHERE name='$username';";
      return $this->odb->query($sql);
    }

    public function getUserDataByUP($username, $passwd)
    {
        if(isset($passwd) && "" != $passwd)
        {
        	$md5 = MD5($passwd);
        }
        else
        {
        	$md5 = "";
        }
        
        $sql = "SELECT * FROM user WHERE name='$username' AND passwd='$md5';";
        return $this->odb->query($sql);
    }
	
	public function getLatestPreorderInfoByUserid($userid)
    {        
        $sql = "SELECT * FROM preorder WHERE userid='$userid' AND actionTime IN (SELECT MAX(actionTime) FROM preorder);";
        return $this->odb->query($sql);
    }
	
	public function getLatestPreorderInfoByBuilder($builder)
    {        
        $sql = "SELECT * FROM preorder WHERE builder='$builder' AND actionTime IN (SELECT MAX(actionTime) FROM preorder);";
        return $this->odb->query($sql);
    }
    
    public function preorderBuilder($userid, $builder, $coinDelta)
    {
      // update coin      
      $sql = "UPDATE user SET `coin`=`coin`+$coinDelta WHERE `userid`='$userid';";
      $this->odb->exec($sql);
      
	  // insert preorder
	  $sql = "INSERT INTO `preorder`(`userid`, `builder`, `actionTime`) VALUES ('$userid', $builder, NOW());";
      $this->odb->exec($sql);
    }

    /**
     * 获取未读消息
     */
    public function getUnread($username, $roomId)
    {
      $sql = "SELECT * FROM `user` WHERE `name`='$username';";
      $dbmsg = $this->odb->query($sql);
      if($dbmsg)
      {
        $userdb = $dbmsg->fetchObject();
        $msgid = $userdb->lastMsgId;
      }
      else
      {
        $msgid = 0;
      }
      
      $sql = "SELECT * FROM `chatmsg` WHERE `id`>$msgid AND (`roomId`=$roomId OR `targetName`='$username') ORDER BY id;";
      $rtSql = $this->odb->query($sql);
      
      $rtSql->setFetchMode(PDO::FETCH_ASSOC);
      $arrmsg = $rtSql->fetchAll();
      $counts = count($arrmsg);
      if($counts > 0)
      {
        $msgid = $arrmsg[$counts - 1]['id'];      
        $sql = "UPDATE user SET `lastMsgId`=$msgid WHERE `name`='$username';";
        $this->odb->exec($sql);
        return $arrmsg;
      }
      else
      {
        return NULL;
      }
    }
}
?>
