<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
 
// Set base path so routes are available under /api when using router.php
$app->setBasePath('/api');
// Include database connection
require 'db.php';

// Middleware for API key authentication
$app->add(function (Request $request, $handler) {
    $apiKey = $request->getHeaderLine('X-API-Key');
    if (empty($apiKey) || $apiKey !== 'secret123') { // Replace with actual API key validation
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
    return $handler->handle($request);
});

// GET /api/pdfs - List all available PDFs
$app->get('/pdfs', function (Request $request, Response $response, $args) use ($conn) {
    $pdfs = [];

    // Get PDFs from notices table
    $sql_notices = "SELECT id, nomor_notice as name, file_pdf FROM notices WHERE status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
    $result_notices = mysqli_query($conn, $sql_notices);
    while ($row = mysqli_fetch_assoc($result_notices)) {
        $pdfs[] = [
            'id' => 'notice_' . $row['id'],
            'name' => $row['name'],
            'type' => 'notice'
        ];
    }

    // Get PDFs from dokumen table
    $sql_dokumen = "SELECT id, nama_dokumen as name, file_pdf FROM dokumen WHERE status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
    $result_dokumen = mysqli_query($conn, $sql_dokumen);
    while ($row = mysqli_fetch_assoc($result_dokumen)) {
        $pdfs[] = [
            'id' => 'dokumen_' . $row['id'],
            'name' => $row['name'],
            'type' => 'dokumen'
        ];
    }

    $response->getBody()->write(json_encode($pdfs));
    return $response->withHeader('Content-Type', 'application/json');
});

// GET /pdf/{id} - Serve specific PDF file
$app->get('/pdf/{id}', function (Request $request, Response $response, $args) use ($conn) {
    $id = $args['id'];
    $filePath = null;

    if (strpos($id, 'notice_') === 0) {
        $noticeId = str_replace('notice_', '', $id);
        $sql = "SELECT file_pdf FROM notices WHERE id = ? AND status = 'active'";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $noticeId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $filePath = $row['file_pdf'];
        }
    } elseif (strpos($id, 'dokumen_') === 0) {
        $dokumenId = str_replace('dokumen_', '', $id);
        $sql = "SELECT file_pdf FROM dokumen WHERE id = ? AND status = 'active'";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $dokumenId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $filePath = $row['file_pdf'];
        }
    }

    if (!$filePath || !file_exists('uploads/' . $filePath)) {
        $response->getBody()->write(json_encode(['error' => 'PDF not found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    // Check if it's a PDF file
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($fileExtension !== 'pdf') {
        $response->getBody()->write(json_encode(['error' => 'Only PDF files are allowed']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // Serve the PDF file using a stream and proper headers
    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $filePath;
    $filesize = filesize($fullPath);

    $stream = new \Slim\Psr7\Stream(fopen($fullPath, 'rb'));
    $response = $response->withBody($stream)
        ->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
        ->withHeader('Content-Length', (string)$filesize)
        ->withHeader('Cache-Control', 'public, max-age=3600');

    return $response;
});

$app->run();
