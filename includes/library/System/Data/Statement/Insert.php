<?php

namespace System\Data\Statement
{
	class Insert extends \System\Object
	{
		protected $tbl;
		protected $vals;
		
		public function __construct($tbl)
		{
			$this->tbl=$tbl;
			$this->vals=array();
		}
		
		public function Add($col,$val)
		{
			$this->vals[$col]=$val;
		}
		
		public function Execute()
		{
			$db=\System\Data\Database::GetInstance();
			
			$sql="INSERT INTO ".$db->Delim($this->tbl,\System\Data\Database::Delim_Table)."(";
			$sql2=") VALUES (";
			$params=array();
			
			$first=true;
			foreach( $this->vals as $col => $val )
			{
				if( !$first )
				{
					$sql.=", ";
					$sql2.=", ";
				}
				$sql.=$db->Delim($col,\System\Data\Database::Delim_Column);
				$colClean=preg_replace('/\s+/','_',preg_replace('/[^a-zA-Z0-9]*/','',$col));
				$sql2.=':'.$colClean;
				$params[$colClean] = $val;
				
				$first = false;
			}
			
			$sql = $sql.$sql2.")";
			
			return $db->ExecuteNonQuery($sql,$params);
		}
	}
}