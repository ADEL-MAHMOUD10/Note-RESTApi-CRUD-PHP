<?php 
require "vendor/autoload.php";
use Ramsey\Uuid\Uuid;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once ("db.php");
$method = $_SERVER['REQUEST_METHOD'];

$notes = load_notes();

function clean_notes($title, $content){
    if ($title === "" || $content === "") {
        echo json_encode(["message" => "Title and Content cannot be empty"]);
        exit;
    }

    if (strlen($title) < 3 || strlen($content) < 3) {
        echo json_encode(["message" => "Title and Content must be at least 3 characters long"]);
        exit;
    }

    if (strlen($title) > 20 || strlen($content) > 500) {
        echo json_encode(["message" => "Title cannot exceed 20 characters and Content cannot exceed 500 characters"]);
        exit;
    }
}
switch ($method) {
    case "GET":
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            if (isset($notes[$id])) {
                echo json_encode($notes[$id]);
            } else {
                echo json_encode(["message" => "Note not found"]);
            } 
        }
        else{
            echo json_encode($notes);
        }
    case "POST":
        $uni_id = Uuid::uuid4()->toString();
        $data = json_decode(file_get_contents("php://input"), true);

        $title = trim($data["title"] ?? "");
        $content = trim($data["content"] ?? "");

        clean_notes($title, $content);
        
        $notes[$uni_id] = [
            "id" => $uni_id,
            "title" => $title,
            "content" => $content,
            "created_at" => date("Y-m-d H:i:s")
        ];

        save_notes($notes);

        echo json_encode([
            "message" => "Note created",
            "note" => $notes[$uni_id]
        ]);
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($_GET["id"])) {
            echo json_encode(["message" => "Note ID is required"]);
            break;
        }
        $id = $_GET["id"];
        if (!isset($notes[$id])) {
            echo json_encode(["message" => "Note not found"]);
            break;
        }

        $title = trim($data["title"] ??"");
        $content = trim($data["content"] ??"");

        clean_notes($title, $content);

        $notes[$id]["title"] = $title;
        $notes[$id]["content"] = $content;
        $notes[$id]["updated_at"] = date("Y-m-d H:i:s");
        
        save_notes($notes);
        echo json_encode(["message" => "Note updated"]);
        break;
    case "DELETE":
        if (!isset($_GET["id"])) {
            echo json_encode(["message" => "Note ID is required"]);
            break;
        }
        $id = $_GET["id"];
        if (!isset($notes[$id])) {
            echo json_encode(["message" => "Note not found"]);
            break;
        }
        unset($notes[$id]);
        save_notes($notes);
        echo json_encode(["message" => "Note deleted"]);
        break;
}