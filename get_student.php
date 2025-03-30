<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "image_upload_db");

// Check connection
if ($conn->connect_error) {
    exit(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Check if reg_number is provided
if (!isset($_GET['reg_number'])) {
    exit(json_encode(["error" => "No reg_number provided"]));
}

$reg_number = $conn->real_escape_string($_GET['reg_number']);

// Debugging: Log received registration number
error_log("Received reg_number: " . $reg_number);

// Prepare SQL query (Make sure 'phone' column exists in your table)
$sql = "SELECT name, branch, semester, student_type, fee_type, amount, phone FROM utr_slips WHERE reg_number = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit(json_encode(["error" => "SQL Error: " . $conn->error]));
}

$stmt->bind_param("s", $reg_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    error_log("Student Data: " . print_r($row, true)); // Log fetched data
    echo json_encode($row);
} else {
    error_log("No student found for reg_number: " . $reg_number);
    echo json_encode(["error" => "No student found"]);
}

$stmt->close();
$conn->close();
?>
