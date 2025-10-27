<?php
$message = '';
$pdf_url = '';
$pdf_name = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $query = trim($_POST['query']);
    if (!empty($query)) {
        // Try connecting to API on multiple local ports (fallback) to avoid timeouts when one server isn't running
        $api_key = 'secret123'; // Predefined secret key for API access
        $portsToTry = [8005, 8000];
        $response = false;
        $http_code = 0;
        $lastError = '';

        foreach ($portsToTry as $port) {
            $api_url = 'http://localhost:' . $port . '/api/search?query=' . urlencode($query);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout in seconds to prevent hanging
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Connection timeout
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-API-Key: ' . $api_key,
                'Accept: application/json'
            ]);

            $response = curl_exec($ch);
            if ($response === false) {
                $lastError = curl_error($ch);
                curl_close($ch);
                // try next port
                continue;
            }

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // if we got a response (any HTTP code), stop trying further ports
            break;
        }

        if ($response === false) {
            $message = 'Gagal menghubungi server API: ' . ($lastError ?: 'no response from any configured ports');
        } else {
            if ($http_code == 200) {
                $data = json_decode($response, true);
                if ($data !== null && isset($data['id'])) {
                    $pdf_url = 'http://localhost:' . $port . '/api/pdf/' . $data['id'];
                    $pdf_name = $data['name'];
                    $message = 'PDF ditemukan: ' . $pdf_name;
                } else {
                    $message = 'PDF tidak ditemukan untuk query: ' . $query;
                }
            } else {
                $error_data = json_decode($response, true);
                if ($error_data !== null && isset($error_data['error'])) {
                    $message = $error_data['error'];
                } else {
                    $message = 'Terjadi kesalahan saat mencari PDF (HTTP ' . $http_code . ')';
                }
            }
        }
    } else {
        $message = 'Masukkan nomor notice atau nama dokumen untuk mencari';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Client Example - Arsip Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><i class="fas fa-globe"></i> API Client Example - Cari PDF</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Halaman ini adalah contoh penggunaan REST API untuk mengakses file PDF tanpa login menggunakan secret key yang telah ditentukan.</p>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-search"></i> Cari PDF berdasarkan Nomor Notice atau Nama Dokumen
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="query" class="form-label">Nomor Notice atau Nama Dokumen</label>
                                            <input type="text" class="form-control" id="query" name="query" placeholder="Masukkan nomor notice atau nama dokumen..." required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end mb-3">
                                            <button type="submit" name="search" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari PDF</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php if (!empty($message)) { ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <?php echo $message; ?>
                        </div>
                        <?php } ?>

                        <?php if (!empty($pdf_url)) { ?>
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-file-pdf"></i> PDF Ditemukan: <?php echo htmlspecialchars($pdf_name); ?>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <iframe src="<?php echo $pdf_url; ?>" width="100%" height="600px" style="border: 1px solid #ddd;">
                                        Browser Anda tidak mendukung iframe. <a href="<?php echo $pdf_url; ?>" target="_blank">Klik di sini untuk melihat PDF</a>
                                    </iframe>
                                    <div class="mt-3">
                                        <a href="<?php echo $pdf_url; ?>" target="_blank" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> Buka di Tab Baru</a>
                                        <a href="<?php echo $pdf_url; ?>" download class="btn btn-success"><i class="fas fa-download"></i> Download PDF</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
