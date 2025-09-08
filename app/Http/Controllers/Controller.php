<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function doRequest($url, $request = [], $method = 'POST')
    {
        try {
            $curl = $this->init();
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl, $this->prepareMethod($method), true);
            if (!empty($request)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($request) ? json_encode($request) : $request);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            }
            curl_setopt($curl, CURLOPT_URL, $url);

            $response = curl_exec($curl);
            return json_decode($response, true);
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    private function prepareMethod($method): int
    {
        match ($method) {
            "POST" => $option = CURLOPT_POST,
            "GET" => $option = CURLOPT_CUSTOMREQUEST,
            "PUT" => $option = CURLOPT_PUT
        };

        return $option ?? CURLOPT_POST;
    }

    private function init(): \CurlHandle|false
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $curl;
    }
}
