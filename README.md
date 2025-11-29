# 📝 Simple Notes REST API

A lightweight, full-featured REST API for managing notes built with PHP. Includes a modern web interface for creating, reading, updating, and deleting notes.

## Features

- ✅ **Full CRUD Operations** - Create, read, update, and delete notes
- 🔐 **Input Validation** - Strict validation for title and content
- 🌐 **CORS Support** - Cross-Origin Resource Sharing enabled
- 📱 **Responsive UI** - Beautiful Tailwind CSS interface
- 🆔 **UUID Support** - Unique identifiers using Ramsey/UUID library
- 💾 **JSON Storage** - Notes persisted in JSON format
- ⏰ **Timestamps** - Automatic creation and update timestamps

## Requirements

- PHP 7.4+
- Composer
- Modern web browser

## Installation

1. **Clone or download the project:**
   ```bash
   git clone <repository-url>
   cd Note_RestAPI
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Create notes directory (if not exists):**
   ```bash
   mkdir notes
   ```

4. **Start PHP built-in server:**
   ```bash
   php -S 127.0.0.1:5000
   ```

5. **Open in browser:**
   - Navigate to `http://127.0.0.1:5000`

## Live Demo / Production

This application is also deployed at:

- https://noteapi.infinityfreeapp.com/

Use the same API paths against the production domain. For example:

- Get all notes: `https://noteapi.infinityfreeapp.com/?route=notes`
- Create a note (POST): `https://noteapi.infinityfreeapp.com/` (JSON body)


## API Endpoints

### GET - Retrieve All Notes
```
GET http://127.0.0.1:5000?route=notes
```
**Response:**
```json
{
  "uuid-1": {
    "id": "uuid-1",
    "title": "My Note",
    "content": "Note content",
    "created_at": "2025-11-25 10:30:45"
  }
}
```

### GET - Retrieve Single Note
```
GET http://127.0.0.1:5000?id=<note-id>
```
**Response:**
```json
{
  "id": "uuid-1",
  "title": "My Note",
  "content": "Note content",
  "created_at": "2025-11-25 10:30:45"
}
```

### POST - Create Note
```
POST http://127.0.0.1:5000
Content-Type: application/json

{
  "title": "My Note",
  "content": "This is the note content"
}
```
**Response:**
```json
{
  "message": "Note created",
  "note": {
    "id": "uuid-generated",
    "title": "My Note",
    "content": "This is the note content",
    "created_at": "2025-11-25 10:30:45"
  }
}
```

### PUT - Update Note
```
PUT http://127.0.0.1:5000?id=<note-id>
Content-Type: application/json

{
  "title": "Updated Title",
  "content": "Updated content"
}
```
**Response:**
```json
{
  "message": "Note updated"
}
```

### DELETE - Delete Note
```
DELETE http://127.0.0.1:5000?id=<note-id>
```
**Response:**
```json
{
  "message": "Note deleted"
}
```

## Validation Rules

| Field | Min Length | Max Length | Required |
|-------|-----------|-----------|----------|
| Title | 3 chars | 20 chars | Yes |
| Content | 3 chars | 500 chars | Yes |

## Error Handling

### 400 - Bad Request
- Empty title or content
- Title/content shorter than 3 characters
- Title exceeds 20 characters or content exceeds 500 characters
- Invalid JSON data

### 404 - Not Found
- Note ID not found
- Missing note ID in request

### 415 - Unsupported Media Type
- Content-Type header is not `application/json`

### 500 - Internal Server Error
- Failed to save/update/delete notes

## Project Structure

```
.
├── index.php           # Main REST API handler
├── index.html          # Frontend UI
├── db.php              # Database helper functions
├── composer.json       # PHP dependencies
├── notes/
│   └── notes.json      # Notes storage file
└── vendor/             # Composer dependencies
```

## File Descriptions

- **index.php** - Core API logic with routing and validation
- **index.html** - Responsive web interface using Tailwind CSS
- **db.php** - Database functions (load_notes, save_notes)
- **notes.json** - Persistent JSON storage for notes

## Dependencies

- **ramsey/uuid** - UUID v4 generation for unique note IDs


## Usage Example

### cURL Commands

**Create a note:**
```bash
curl -X POST http://127.0.0.1:5000 \
  -H "Content-Type: application/json" \
  -d '{"title":"My First Note","content":"This is awesome!"}'
```

**Get all notes:**
```bash
curl http://127.0.0.1:5000?route=notes
```

**Update a note:**
```bash
curl -X PUT "http://127.0.0.1:5000?id=<note-id>" \
  -H "Content-Type: application/json" \
  -d '{"title":"Updated","content":"New content here"}'
```

**Delete a note:**
```bash
curl -X DELETE "http://127.0.0.1:5000?id=<note-id>"
```

## Browser Usage

1. Open `http://127.0.0.1:5000` in your browser
2. Enter a title (3-20 characters) and content (3-500 characters)
3. Click "Add Note" to create
4. Use "Edit" to modify or "Delete" to remove notes

## License

This project is open source and available under the MIT License.

## Author

Created by ADEL-MAHMOUD10

## Support

For issues or questions, please open an issue in the repository.
