<?php 
defined('ACC')||exit('ACC Denied');		

class mysql extends db{
	private static $ins = NULL;
	private $conn = NULL;
	private $conf = array();

	protected function __construct()//实例化对象的时候需要做一个初始化的动作，在new一个对象的时候会自动执行的一个方法
	{
		$this->conf = conf::getIns();
		$this->connect($this->conf->host,$this->conf->user,$this->conf->pwd);
		$this->select_db($this->conf->db);
		$this->setChar($this->conf->char);

	}

	public function __destruct()
	{

	}

	public static function getIns()//保证一个类只有一个实例,也就是说mysql类只有一个实例。
	{
		if(!(self::$ins instanceof self)){
			self::$ins = new self();
		}
		return self::$ins;
	}

	public function connect($h,$u,$p){   //
		$this->conn = mysql_connect($h,$u,$p);
		if(!$this->conn){
			$err = new Expection('链接失败');//Expection异常类
			throw $err;         //触发异常
		}
	}

	protected function select_db($db){      //选择库方法
		$sql = 'use '. $db;
		$this->query($sql);
	}

	protected function setChar($char){      //设置编译器语言方法
		$sql = 'set names ' . $char;
		return $this -> query($sql);
	}

	public function query($sql){            //mysql语句查询
		$rs = mysql_query($sql,$this->conn);
		log::write($sql);
		return $rs;
	}

	public function autoExecute($table,$arr,$mode='insert',$where = ' where 1 limit 1'){   //sql语句的拼接方法；
		/*
			insert into tbname(username,passwd,email)value('',)
			把所有的键名用','拼接
			implode(',',array_key($arr));
			implode("','"",array_values($arr));
		*/
		if(!is_array($arr)){
			return false;
		}
		if($mode == 'update'){
			$sql = 'update '.$table.' set ';
			foreach($arr as $k => $v){
				$sql .=$k . "='" . $v ."',";
			}
			$sql = rtrim($sql,',');
			$sql .= $where;
			return $this ->query($sql);
		}	

		$sql = 'insert into ' . $table . ' (' .implode(',',array_keys($arr)). ')';  
		$sql .= ' values (\'';                             //第一句sql付给第二句sql
		$sql .=implode("','",array_values($arr));          //第二句sql付给三句sql
		$sql .= '\')';                                     //第三句sql付给第四句sql

		return $this->query($sql); 
	}
		public function getAll($sql){
			$rs = $this->query($sql);
			//循环把所有数据获取
			$list = array();
			while($row = mysql_fetch_assoc($rs)){
				$list[] = $row;
			}
			return $list;
		}

		public function getRow($sql){
			$rs = $this->query($sql);

			return mysql_fetch_assoc($rs);//关联数组key还是原来的key值
		}

		public function getOne($sql){
			$rs = $this->query($sql);
			$row = mysql_fetch_row($rs);//索引数组key值为[0],[1],[2]
			return $row[0];
		}

		//返回影响行数的函数
		public function affected_rows(){
			return mysql_affected_rows($this->conn);
		}

		//返回最新的auto_increment列的自增长的值
		public function insert_id(){
			return mysql_insert_id($this->conn);
		}

}















































 ?>