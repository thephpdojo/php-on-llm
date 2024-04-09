<?php
namespace PHPOnLLM/Http;

class Response
{
    /**
     * Serve a file with the appropriate header for its type.
     *
     * @param string $filePath The path to the file to serve.
     * @return void
     */
    public function file(string $filePath): void
    {
        // Check if the file exists
        if (!file_exists($filePath)) {
            // You could also use a 404 status header here
            header("HTTP/1.0 404 Not Found");
            die("File not found.");
        }

        // Determine the MIME type based on the file extension
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $this->getMimeType($fileExtension);

        // Set the content type header
        header("Content-Type: $mimeType");

        // Read and serve the file
        readfile($filePath);
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data The data to be converted to JSON.
     * @return void
     */
    public function json($data): void
    {
        // Set the content type header for JSON
        header('Content-Type: application/json');

        // Convert the data to JSON and output
        echo json_encode($data);
    }

    /**
     * Get the MIME type based on the file extension.
     *
     * @param string $extension The file extension.
     * @return string The MIME type.
     */
    private function getMimeType(string $extension): string
    {
        $mimeTypes = [
            'txt' => 'text/plain',
            'html' => 'text/html',
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp', // Added support for WebP
            // add more file types and MIME types as needed
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream'; // default MIME type
    }
}
