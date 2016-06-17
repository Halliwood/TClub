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
	
	public function reg($username, $passwd)
	{
		$sql = "INSERT INTO `user`(`name`, `passwd`, `status`) VALUES ('$username', MD5($passwd), 0);";
	    $this->odb->exec($sql);
	}
	
	public function getUserToken($identifier)
	{
		$sql = "SELECT id, name, status, token, timeout FROM user WHERE identifier='$identifier' limit 1;";
    	return $this->odb->query($sql);
	}
	
	public function updateUserToken($userid, $identifier, $token, $timeout)
	{
		$sql = "UPDATE user SET `identifier`='$identifier', `token`='$token', `timeout`=$timeout WHERE `id`=$userid;";
    	return $this->odb->query($sql);
	}
    
    public function getUserData($username)
    {
    	$sql = "SELECT id, name, status, coin FROM user WHERE name='$username' limit 1;";
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
        
        $sql = "SELECT id, name, status, coin FROM user WHERE name='$username' AND passwd='$md5' limit 1;";
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
    
    public function orderBuilder($userid, $builder, $coinDelta)
    {
      // update coin      
      $sql = "UPDATE user SET `coin`=`coin`-$coinDelta WHERE `id`='$userid';";
      $this->odb->exec($sql);
      
	  // insert preorder
	  $sql = "INSERT INTO `preorder`(`userid`, `builder`, `actionTime`) VALUES ('$userid', $builder, ".time().");";
      $this->odb->exec($sql);
    }
	
	public function cancelOrderBuilder($orderID)
    {
      // delete row   
      $sql = "DELETE FROM preorder WHERE `id`='$orderID';";
      $this->odb->exec($sql);
    }
}
?>
