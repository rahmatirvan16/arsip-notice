# TODO List for Converting to RESTful API Server for PDFs

- [x] Update composer.json to add Slim framework for API routing
- [x] Run composer install to install new dependencies
- [x] Create api.php as main API entry point with Slim app
- [x] Implement authentication middleware (API key in header)
- [x] Create GET /api/pdfs endpoint to list available PDFs from notices and dokumen tables
- [x] Create GET /api/pdf/{id} endpoint to serve specific PDF file
- [x] Add error handling for invalid requests, missing files, unauthorized access
- [x] Create .htaccess for URL routing
- [x] Test API endpoints with tools like Postman or curl
- [x] Update existing web files to redirect or remove web UI if needed
- [x] Document API usage in a README or api_docs.php
