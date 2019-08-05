<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * User model
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     * @param array $data  Initial property values
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();
        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = 'INSERT INTO users (username, email, password_hash)
                    VALUES (:username, :email, :password_hash)';
            $db = static::getDB();
            $statement = $db->prepare($sql);
            $statement->bindValue(':username', $this->name, PDO::PARAM_STR);
            $statement->bindValue(':email', $this->email, PDO::PARAM_STR);
            $statement->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            if ($statement->execute()) {
				$user = static::findByEmail($this->email);
				return $this->copyDefaultCategories($user->id);
			}
        }
        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     * @return void
     */
    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->errors[] = 'Wymagana nazwa użytkownika.';
        }
        // email address
		if ($this->email == '') {
            $this->errors[] = 'Wymagany adres email.';
        } else if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Wpisz poprawny adres email.';
        } else if (static::emailExists($this->email)) {
            $this->errors[] = 'Adres email zajęty.';
        }
        // Password
        if (strlen($this->password) < 6) {
            $this->errors[] = 'Hasło musi zawierać conajmniej 6 znaków.';
        } else if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło musi zawierać conajmniej jedną literę.';
        } else if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło musi zawierać conajmniej jedną cyfrę.';
        } else if (strlen($this->password) > 50) {
            $this->errors[] = 'Hasło może zawierać maksymalnie 50 znaków.';
        }
    }

    /**
     * See if a user record already exists with the specified email
     * @param string $email email address to search for
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email) {
        return static::findByEmail($email) !== false;
    }
	
	/**
     * Find a user model by email address
     * @param string $email email address to search for
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email) {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $statement = $db->prepare($sql);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
		$statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $statement->execute();
        return $statement->fetch();
    }
	
	/**
     * Authenticate a user by email and password
     * @param string $email email address
     * @param string $password password
     * @return mixed The user object or false if authentication false
     */
    public static function authenticate($email, $password) {
		$user = static::findByEmail($email);
		if ($user) {
			if(password_verify($password, $user->password_hash)) {
				return $user;
			}
		}
		return false;
	}
	
	/**
     * Find a user model by ID
     * @param string $id The user ID
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id) {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $db = static::getDB();
        $statement = $db->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
		$statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $statement->execute();
        return $statement->fetch();
    }
	
	/** Remember the login by inserting a new unique token into the remembered_logins table
	 * for this user record
	 * @return boolean True if the login was remembered successfully, false otherwise
	 */
	public function rememberLogin() {
		$token = new Token ();
		$hashed_token = $token->getHash();
		$this->remember_token = $token->getValue();
		
		$this->expiry_timestamp = time() + 60*60*24*30; // 30 days from now
		
		$sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
					VALUES (:token_hash, :user_id, :expires_at)';
		$db = static::getDB();
		$statement = $db->prepare($sql);
		$statement->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$statement->bindValue(':user_id', $this->id, PDO::PARAM_INT);
		$statement->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);
		
		return $statement->execute();
	}
	
	/** Copy default categories to categories assua
	 * @return bolean True if categories was copied, false otherwise
	 */
	protected function copyDefaultCategories($userId) {
		$db = static::getDB();
		$copyPayments=$db->prepare("INSERT INTO payment_methods_assigned_to_users (id, user_id, name) SELECT NULL, :newUserId, name FROM payment_methods_default");
		$copyPayments->bindValue(':newUserId',$userId,PDO::PARAM_INT);
		$copyIncomes=$db->prepare("INSERT INTO incomes_category_assigned_to_users (id, user_id, name) SELECT NULL, :newUserId, name FROM incomes_category_default");
		$copyIncomes->bindValue(':newUserId',$userId,PDO::PARAM_INT);
		$copyExpenses=$db->prepare("INSERT INTO expenses_category_assigned_to_users (id, user_id, name) SELECT NULL, :newUserId, name FROM expenses_category_default");
		$copyExpenses->bindValue(':newUserId',$userId,PDO::PARAM_INT);
		return ($copyPayments->execute() && $copyIncomes->execute() && $copyExpenses->execute());
	}
}
