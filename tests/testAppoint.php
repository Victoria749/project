<?php
use PHPUnit\Framework\TestCase;
class testAppoint extends TestCase{
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
    public function testInsertAppointmentSuccess() {
        $userId = 1; 
        $serviceId = 1; 
        $doctorId = 1; 
        $date = '2023-10-01';
        $time = '10:00:00';
        $stmt = $this->conn->prepare("INSERT INTO appointment (date, time, id_user, id_service, id_doctor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $date, $time, $userId, $serviceId, $doctorId);
        $result = $stmt->execute();
        $this->assertTrue($result); 
    }

    public function testInsertAppointmentWithMissingFields() {
        $userId = 1; 
        $serviceId = null; 
        $doctorId = 1; 
        $date = '2023-10-01';
        $time = '10:00:00';
        $stmt = $this->conn->prepare("INSERT INTO appointment (date, time, id_user, id_service, id_doctor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $date, $time, $userId, $serviceId, $doctorId);
        $result = $stmt->execute();
        $this->assertFalse($result);
    }

    public function testInsertAppointmentWithInvalidUser() {
        $userId = null; 
        $serviceId = 1; 
        $doctorId = 1; 
        $date = '2023-10-01';
        $time = '10:00:00';
        $stmt = $this->conn->prepare("INSERT INTO appointment (date, time, id_user, id_service, id_doctor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $date, $time, $userId, $serviceId, $doctorId);
        $result = $stmt->execute();
        $this->assertFalse($result);
    }
}