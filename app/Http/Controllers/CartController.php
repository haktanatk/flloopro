<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $sku = $request->input('sku');
        $qty = $request->input('qty');

        if($qty < 1)
        {
            return ['success' => false, 'message' => '0 dan büyük seçilmelidir.'];
        }

        $requestData = $this->getRequest($sku, $qty);
        $url = 'http://127.0.0.1:3031/api/cart';
        $response = $this->doRequest($url, $requestData);


        if (isset($response['status']) && $response['status'] === 'success') {
            // Your logic here
            return response()->json(['status' => 'success', 'message' => 'Başarıyla eklendi'], 200);
        }

        return response()->json($response, 200);
    }

    public function getRequest($sku, $qty): string
    {
        return json_encode([
            'qty' => $qty ?? 0,
            'sku' => $sku ?? '',
        ]);
    }


    public function destroy($id)
    {
        return response()->json(['status' => 'success', 'message' => "Ürün Silindi $id",], 200);
    }

    public function index()
    {

        return response()->json(['status' => 'success', 'basket' => []], 200);
    }
}
