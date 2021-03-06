<?php declare(strict_types=1);

namespace TG\Modules\Security\Data;
class User extends \Core\Data\ActiveRecord {
	public static function FindByUsername($un) {
		return static::FindOnlyBy(['username' => $un]);
	}
	
	public static function FindByUsernameOrEmail($un, $email) {
		return static::FindAllBy(['username' => $un, 'email' => $email], 'OR');
	}
	
	public static function FindByForgotGuid($guid) {
		return static::FindOnlyBy(['forgot_password_guid' => $guid]);
	}
	
	/*public static function FindAllWithRole($role) {
		if( is_string($role) )
			$role = \Site\Data\TG\Modules\Security\Role::FindByName($role);
		else if( is_numeric($role) )
			$role = \Site\Data\TG\Modules\Security\Role::Load($role);
		
		$db = static::GetDb();
		$sql = 'SELECT '.$db->DelimTable('u').'.*' .
				' FROM '.$db->DelimTable(static::$table_name).' AS '.$db->DelimTable('u') .
					' LEFT JOIN '.$db->DelimTable(static::$relationships['Role']['table']).' AS '.$db->DelimTable('ur') .
					' LEFT JOIN '.$db->DelimTable(static::$relationships['Group']['table']).' AS '.$db->DelimTable('ug') .
					' LEFT JOIN '.$db->DelimTable(static::$relationships['Role']['table']).' AS '.$db->DelimTable('gr') .;
		return static::FindOnlyBy(['username' => $un]);
	}*/
	
	protected static $table_name = 'security_users';
	protected static $columns = [
		'id' => '+@!bigint',
		'username' => '*varchar[255]',
		'email' => '*varchar[255]',
		'password' => 'varchar[255]',
		'forgot_password_guid' => '#?varchar[36]',
		'last_login' => '?bigint'
	];
	
	protected static $relationships = [
		'Group' => [
			'table' => 'security_user_groups',
			'local_id' => 'user_id',
			'foreign_id' => 'group_id',
			'class' => 'Security\Data\Group'
		],
		'Role' => [
			'table' => 'security_user_roles',
			'local_id' => 'user_id',
			'foreign_id' => 'role_id',
			'class' => 'Security\Data\Role'
		],
	];
}
