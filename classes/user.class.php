<?php

require ('db_connect.class.php');

class User {

  private $conn;
  public $id;
  public $name;
  public $email;
  public $password;
  public $school;

  public function __construct($name, $email, $password, $school) {
    $this->conn = Database::getInstance()->getConn();
    $this->name = $name;
    $this->email = $email;
    $this->password = $password;
    $this->school = $school;
  }


  public function register()
{
    // Check if the email already exists
    $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $this->email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        return -1; // Email already exists
    }

    $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, school) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssss", $this->name, $this->email, $hashed_password, $this->school);

    if ($stmt->execute()) {
        $this->id = $this->conn->insert_id;
        $stmt->close();
        return true; // Registration successful
    }

    return false; // Registration failed
}

  public static function login($email, $password) {
    $conn = Database::getInstance()->getConn();
    
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct, create and return a new donor object
            $user = new User($row['name'], $row['email'], $row['password'], $row['school']);
            $user->id = $row['id'];
            return $user;
        }
        else{
          return 0;
        }
    }
    
    return false;
  }
  public function updateProfile($id, $name, $password, $school)
    {
       if(!empty($password)){
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, password = ?, school = ? WHERE id = ?");
            $stmt->bind_param("sssi",$name, $hashed_password, $school, $id);
        }   
        
        else{
            $stmt = $this->conn->prepare("UPDATE users SET name = ? school = ? WHERE id = ?");
            $stmt->bind_param("ssi",$name, $school, $id);
        }
        
        if($stmt->execute()){
            // Update the user object properties if the update was successful
            $this->name = $name;
            $this->school = $school;
            $this->id = $id;
            if (!empty($password)) {
                $this->password = $hashed_password;
                
            }
            
            return new User($this->name, $this->email, $this->password, $this->school);
            
        }
        return false;
    }

}
