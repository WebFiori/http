<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;

#[RestController('xml-response', 'XML response service')]
class XmlResponseService extends WebService {
    
    #[GetMapping]
    #[ResponseBody(contentType: 'application/xml')]
    #[AllowAnonymous]
    public function getXmlData(): string {
        $data = [
            'message' => 'Hello from WebFiori HTTP',
            'timestamp' => date('Y-m-d H:i:s'),
            'format' => 'xml',
            'server_info' => [
                'php_version' => PHP_VERSION,
                'server_time' => time()
            ]
        ];
        
        return $this->arrayToXml($data);
    }
    
    private function arrayToXml(array $data, string $root = 'response'): string {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<$root>\n";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml .= "  <$key>\n";
                foreach ($value as $k => $v) {
                    $xml .= "    <$k>" . htmlspecialchars($v) . "</$k>\n";
                }
                $xml .= "  </$key>\n";
            } else {
                $xml .= "  <$key>" . htmlspecialchars($value) . "</$key>\n";
            }
        }
        $xml .= "</$root>";
        return $xml;
    }
}
