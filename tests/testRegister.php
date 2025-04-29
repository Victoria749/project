<?php
use PHPUnit\Framework\TestCase;

class testRegister extends TestCase {
    private $conn;

    protected function setUp(): void {
        
        $this->conn = new mysqli("127.0.0.1:3303", "root", "root", "med_zentr");

        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    protected function tearDown(): void {
        
        $this->conn->close();
    }
    
    public function testUserRegistrationSuccess() {
        
        $last_name = 'Иванов';
        $name = 'Иван';
        $father_name = 'Иванович';
        $date_of_birth = '1990-01-01';
        $number = '89999999999';
        $number_polis = '1234567812345678';
        $email = 'ivanov@example.com';
        $password = 'password';
        $stmt = $this->conn->prepare("INSERT INTO user (last_name, name, father_name, date_of_birth, number, number_polis, email, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $last_name, $name, $father_name, $date_of_birth, $number, $number_polis, $email, $password);
        $result = $stmt->execute();
        $this->assertTrue($result);
        $stmt = $this->conn->prepare("DELETE FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }

    public function testUserRegistrationDuplicateEmail() {
        
        $last_name = 'Петров';
        $name = 'Петр';
        $father_name = 'Петрович';
        $date_of_birth = '1985-05-05';
        $number = '89999999999';
        $number_polis = '1234567812345678';
        $email = 'petrov@example.com';
        $password = 'password';
        $stmt = $this->conn->prepare("INSERT INTO user (last_name, name, father_name, date_of_birth, number, number_polis, email, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $last_name, $name, $father_name, $date_of_birth, $number, $number_polis, $email, $password);
        $stmt->execute();
        $stmt = $this->conn->prepare("INSERT INTO user (last_name, name, father_name, date_of_birth, number, number_polis, email, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $last_name, $name, $father_name, $date_of_birth, $number, $number_polis, $email, $password);
        $result = $stmt->execute();
        $this->assertFalse($result);
        $stmt = $this->conn->prepare("DELETE FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }
}