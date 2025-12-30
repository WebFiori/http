<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * Service demonstrating different response formats
 */
#[RestController('response', 'Multiple response format demonstration')]
class ResponseService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('format', 'string', true, 'json', 'Response format: json, xml, text')]
    #[RequestParam('data', 'string', true, 'sample', 'Data to include in response')]
    public function handleResponse(?string $format = 'json', ?string $data = 'sample'): mixed {
        $sampleData = $this->getSampleData($data, $format);

        // Dynamically set content type based on format
        $reflection = new ReflectionMethod($this, 'handleResponse');
        $attrs = $reflection->getAttributes(ResponseBody::class);

        switch ($format) {
            case 'xml':
                // Override response to XML
                $xml = $this->arrayToXml($sampleData, 'response');
                $this->send('application/xml', $xml, 200);

                return null;
            case 'text':
                // Override response to text
                $text = $this->arrayToText($sampleData);
                $this->send('text/plain', $text, 200);

                return null;
            default:
                // Return array for JSON (handled by ResponseBody)
                return $sampleData;
        }
    }

    private function arrayToCsv(array $data): string {
        if (empty($data)) {
            return '';
        }

        $csv = '';

        // Check if it's a list of objects/arrays
        if (is_array($data[0] ?? null)) {
            // Get headers
            $headers = array_keys($data[0]);
            $csv .= implode(',', $headers)."\n";

            // Add data rows
            foreach ($data as $row) {
                $csvRow = [];

                foreach ($headers as $header) {
                    $value = $row[$header] ?? '';
                    $csvRow[] = '"'.str_replace('"', '""', $value).'"';
                }
                $csv .= implode(',', $csvRow)."\n";
            }
        } else {
            // Simple key-value pairs
            $csv .= "Key,Value\n";

            foreach ($data as $key => $value) {
                $csv .= '"'.str_replace('"', '""', $key).'","'.str_replace('"', '""', $value)."\"\n";
            }
        }

        return $csv;
    }

    private function arrayToHtml(array $data): string {
        $html = "<!DOCTYPE html>\n<html>\n<head>\n";
        $html .= "<title>WebFiori HTTP Response</title>\n";
        $html .= "<style>body{font-family:Arial,sans-serif;margin:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}</style>\n";
        $html .= "</head>\n<body>\n";
        $html .= "<h1>WebFiori HTTP Response</h1>\n";
        $html .= "<p>Generated: ".date('Y-m-d H:i:s')."</p>\n";
        $html .= $this->arrayToHtmlTable($data);
        $html .= "</body>\n</html>";

        return $html;
    }

    private function arrayToHtmlTable(array $data): string {
        if (empty($data)) {
            return '<p>No data available</p>';
        }

        // Check if it's a list of objects/arrays
        if (is_array($data[0] ?? null)) {
            return $this->createHtmlTable($data);
        }

        // Simple key-value pairs
        $html = "<table>\n";

        foreach ($data as $key => $value) {
            $html .= "<tr><th>".htmlspecialchars($key)."</th>";
            $html .= "<td>".htmlspecialchars(is_array($value) ? json_encode($value) : $value)."</td></tr>\n";
        }
        $html .= "</table>\n";

        return $html;
    }

    private function arrayToText(array $data): string {
        return $this->arrayToTextRecursive($data, 0);
    }

    private function arrayToTextRecursive(array $data, int $indent = 0): string {
        $text = '';
        $spaces = str_repeat('  ', $indent);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $text .= "$spaces$key:\n";
                $text .= $this->arrayToTextRecursive($value, $indent + 1);
            } else {
                $text .= "$spaces$key: $value\n";
            }
        }

        return $text;
    }

    private function arrayToXml(array $data, string $rootElement = 'root'): string {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<$rootElement>\n";
        $xml .= $this->arrayToXmlRecursive($data, 1);
        $xml .= "</$rootElement>";

        return $xml;
    }

    private function arrayToXmlRecursive(array $data, int $indent = 0): string {
        $xml = '';
        $spaces = str_repeat('  ', $indent);

        foreach ($data as $key => $value) {
            $key = is_numeric($key) ? 'item' : $key;

            if (is_array($value)) {
                $xml .= "$spaces<$key>\n";
                $xml .= $this->arrayToXmlRecursive($value, $indent + 1);
                $xml .= "$spaces</$key>\n";
            } else {
                $xml .= "$spaces<$key>".htmlspecialchars($value)."</$key>\n";
            }
        }

        return $xml;
    }

    private function createHtmlTable(array $data): string {
        if (empty($data)) {
            return '<p>No data available</p>';
        }

        $html = "<table>\n<thead>\n<tr>";

        // Get headers from first row
        $headers = array_keys($data[0]);

        foreach ($headers as $header) {
            $html .= "<th>".htmlspecialchars($header)."</th>";
        }
        $html .= "</tr>\n</thead>\n<tbody>\n";

        // Add data rows
        foreach ($data as $row) {
            $html .= "<tr>";

            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                $html .= "<td>".htmlspecialchars($value)."</td>";
            }
            $html .= "</tr>\n";
        }

        $html .= "</tbody>\n</table>\n";

        return $html;
    }

    private function getSampleData(string $type, string $format): array {
        switch ($type) {
            case 'users':
                return [
                    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
                    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
                    ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com']
                ];
            case 'products':
                return [
                    ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'category' => 'Electronics'],
                    ['id' => 2, 'name' => 'Book', 'price' => 19.99, 'category' => 'Education'],
                    ['id' => 3, 'name' => 'Coffee Mug', 'price' => 12.50, 'category' => 'Kitchen']
                ];
            default:
                return [
                    'message' => 'Hello from WebFiori HTTP',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'format_requested' => $format,
                    'server_info' => [
                        'php_version' => PHP_VERSION,
                        'server_time' => time()
                    ]
                ];
        }
    }
}
