# RESTful API for PDF Access

This API provides secure access to PDF files stored in the Arsip Digital system.

## Authentication

All API requests require an API key in the header:
```
X-API-Key: secret123
```

## Endpoints

### GET /api/pdfs

Returns a JSON list of available PDFs.

**Response:**
```json
[
  {
    "id": "notice_1",
    "name": "CD025-0000001",
    "type": "notice"
  },
  {
    "id": "dokumen_1",
    "name": "Lunas PKB Agustus 2025",
    "type": "dokumen"
  }
]
```

### GET /api/search

Searches for a PDF by notice number or document name.

**Parameters:**
- `query`: The search query (notice number or document name)

**Response:**
```json
{
  "id": "notice_1",
  "name": "CD025-0000001",
  "type": "notice"
}
```

**Error Response:**
```json
{
  "error": "PDF not found"
}
```

### GET /api/pdf/{id}

Serves the PDF file with the specified ID.

**Parameters:**
- `id`: The PDF ID (e.g., `notice_1` or `dokumen_1`)

**Response:** PDF file content with `application/pdf` content type.

## Error Responses

- `401 Unauthorized`: Missing or invalid API key
- `404 Not Found`: PDF not found
- `400 Bad Request`: Invalid request or non-PDF file

## Usage Examples

Using curl (or PowerShell):

```bash
# List PDFs
curl -H "X-API-Key: secret123" http://localhost:8000/api/pdfs

# Get specific PDF and save to file
curl -H "X-API-Key: secret123" http://localhost:8000/api/pdf/notice_1 -o file.pdf
```

## Running the API

Use PHP built-in server with the included `router.php` so API routes under `/api` are handled correctly:

```powershell
php -S localhost:8000 router.php
```

Then access at http://localhost:8000/api/pdfs

## Security Notes

- API key is set to 'secret123' for testing - change in production.
- Only active PDFs from the database are accessible.
- Files are served directly from the `uploads/` directory.
