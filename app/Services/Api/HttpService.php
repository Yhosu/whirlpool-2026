<?php

namespace App\Services\Api;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

abstract class HttpService
{
    protected $baseUrl;

    protected $headers;

    protected $methods = [
        'GET' => 'get',
        'POST' => 'post',
        'DELETE' => 'delete',
        'PUT' => 'put',
    ];

    protected function processRequest(
        string $endpoint,
        string $method = 'GET',
        array $parameters = []
    ) {
        $randCode = rand(100000, 999999);
        $url = $this->baseUrl.'/'.$endpoint;
        $type = $this->methods[$method];
        \Log::info(sprintf('%sQuery %s: %s - %s | Headers: %s', $method, $randCode, $url, json_encode($parameters), json_encode($this->headers)));
        try {
            $result = Http::withHeaders($this->headers)->$type($url, $parameters);
            if ($result->successful()) {
                $result = $result->json();
                \Log::info(sprintf('%sResponse %s: %s - %s', $method, $randCode, $url, json_encode($result)));

                return $result;
            }

            return ['error' => 'API request failed with status: '.$result->status()];
        } catch (Exception $e) {
            $result = ['status' => false, 'message' => $e->getMessage() ?? 'Hubo un error al procesar su solicitud, lo solucionaremos lo mas antes posible'];
        } catch (ConnectionException $e) {
            $result = ['status' => false, 'message' => $e->getMessage() ?? 'Hubo un error al procesar su solicitud, lo solucionaremos lo mas antes posible'];
        }

        return $result;
    }

    protected function processPostWithAttach($endpoint, $parameters, $file)
    {
        $randCode = rand(100000, 999999);
        $url = $this->baseUrl.'/'.$endpoint;
        \Log::info(sprintf('%sQueryWithAttach %s: %s - %s | Headers: %s', 'POST', $randCode, $url, json_encode($parameters), json_encode($this->headers)));
        try {
            $result = Http::withHeaders([])->attach('file', file_get_contents($file), time().'.'.$file->getClientOriginalExtension())->post($url, $parameters)->json();
            \Log::info(sprintf('%sResponseWithAttach %s: %s - %s', 'POST', $randCode, $url, json_encode($result)));
        } catch (Exception $e) {
            $result = ['status' => false, 'message' => $e->getMessage() ?? 'Hubo un error al procesar su solicitud, lo solucionaremos lo mas antes posible'];
        } catch (ConnectionException $e) {
            $result = ['status' => false, 'message' => $e->getMessage() ?? 'Hubo un error al procesar su solicitud, lo solucionaremos lo mas antes posible'];
        }

        return $result;
    }
}
