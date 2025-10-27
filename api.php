<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
// Include database connection early so handlers can use $conn
require 'db.php';

// Helper handlers so we can register routes with and without the /api prefix

// Handler: list PDFs
$listPdfsHandler = function (Request $request, Response $response, $args) use ($conn) {
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
};

// Handler: search PDF by notice number or document name
$searchPdfHandler = function (Request $request, Response $response, $args) use ($conn) {
    $query = $request->getQueryParams()['query'] ?? '';

    if (empty($query)) {
        $response->getBody()->write(json_encode(['error' => 'Query parameter is required']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $pdf = null;

    // Search in notices table by nomor_notice (exact match first, then partial)
    $sql_notices_exact = "SELECT id, nomor_notice as name, file_pdf FROM notices WHERE nomor_notice = ? AND status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
    $stmt_notices_exact = mysqli_prepare($conn, $sql_notices_exact);
    mysqli_stmt_bind_param($stmt_notices_exact, 's', $query);
    mysqli_stmt_execute($stmt_notices_exact);
    $result_notices_exact = mysqli_stmt_get_result($stmt_notices_exact);

    if ($row = mysqli_fetch_assoc($result_notices_exact)) {
        $pdf = [
            'id' => 'notice_' . $row['id'],
            'name' => $row['name'],
            'type' => 'notice'
        ];
    } else {
        // Search in notices table by nomor_notice (partial match)
        $sql_notices = "SELECT id, nomor_notice as name, file_pdf FROM notices WHERE nomor_notice LIKE ? AND status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
        $stmt_notices = mysqli_prepare($conn, $sql_notices);
        $searchTerm = '%' . $query . '%';
        mysqli_stmt_bind_param($stmt_notices, 's', $searchTerm);
        mysqli_stmt_execute($stmt_notices);
        $result_notices = mysqli_stmt_get_result($stmt_notices);

        if ($row = mysqli_fetch_assoc($result_notices)) {
            $pdf = [
                'id' => 'notice_' . $row['id'],
                'name' => $row['name'],
                'type' => 'notice'
            ];
        } else {
            // Search in dokumen table by nama_dokumen (exact match first, then partial)
            $sql_dokumen_exact = "SELECT id, nama_dokumen as name, file_pdf FROM dokumen WHERE nama_dokumen = ? AND status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
            $stmt_dokumen_exact = mysqli_prepare($conn, $sql_dokumen_exact);
            mysqli_stmt_bind_param($stmt_dokumen_exact, 's', $query);
            mysqli_stmt_execute($stmt_dokumen_exact);
            $result_dokumen_exact = mysqli_stmt_get_result($stmt_dokumen_exact);

            if ($row = mysqli_fetch_assoc($result_dokumen_exact)) {
                $pdf = [
                    'id' => 'dokumen_' . $row['id'],
                    'name' => $row['name'],
                    'type' => 'dokumen'
                ];
            } else {
                // Search in dokumen table by nama_dokumen (partial match)
                $sql_dokumen = "SELECT id, nama_dokumen as name, file_pdf FROM dokumen WHERE nama_dokumen LIKE ? AND status = 'active' AND file_pdf IS NOT NULL AND file_pdf != ''";
                $stmt_dokumen = mysqli_prepare($conn, $sql_dokumen);
                mysqli_stmt_bind_param($stmt_dokumen, 's', $searchTerm);
                mysqli_stmt_execute($stmt_dokumen);
                $result_dokumen = mysqli_stmt_get_result($stmt_dokumen);

                if ($row = mysqli_fetch_assoc($result_dokumen)) {
                    $pdf = [
                        'id' => 'dokumen_' . $row['id'],
                        'name' => $row['name'],
                        'type' => 'dokumen'
                    ];
                }
            }
        }
    }

    if (!$pdf) {
        $response->getBody()->write(json_encode(['error' => 'PDF not found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $response->getBody()->write(json_encode($pdf));
    return $response->withHeader('Content-Type', 'application/json');
};

// Handler: serve PDF by id
$servePdfHandler = function (Request $request, Response $response, $args) use ($conn) {
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
};
// (db connection already included above)

// Middleware for API key authentication (skip for PDF serving)
$app->add(function (Request $request, $handler) {
    $path = $request->getUri()->getPath();
    if (strpos($path, '/api/pdf/') === 0) {
        // Skip authentication for PDF serving to allow iframe display
        return $handler->handle($request);
    }

    $apiKey = $request->getHeaderLine('X-API-Key');
    if (empty($apiKey) || $apiKey !== 'secret123') { // Replace with actual API key validation
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
    return $handler->handle($request);
});

// Register routes under /api (router.php includes this file only for /api/* requests)
$app->get('/api/pdfs', $listPdfsHandler);
$app->get('/api/search', $searchPdfHandler);
$app->get('/api/pdf/{id}', $servePdfHandler);

$app->run();
