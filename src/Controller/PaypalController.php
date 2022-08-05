<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Seller;
use App\Entity\Variant;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
use PayPalHttp\IOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PaypalController extends AbstractController
{
    /**
     * Paypal Orders Checkout Function
     * @Route("/paypal-create-payement", name="paypal_create_payament")
     * @param SessionInterface $session
     *
     * @return JsonResponse
     * @throws IOException
     */
    public function paypalCreateOrder(SessionInterface $session) {

        $clientId = "AZvdHvxArQ6e9N1xtCO-Hc8X6oFGVxizZfnkJvDEYGF4zP727c1NjVD5lGbbDxi4QjGraqrFQ_cxcZNm";
        $clientSecret = "EANsINF_SmRiv25XBD0g5dIDdKmcoQCIpiaGhcazF45itNfSSW1t-Hm1NXoF5C7QxCt094i8KjaCGgn8";

        $env = new SandboxEnvironment($clientId,$clientSecret);
        $client = new PayPalHttpClient($env);
        $req = new OrdersCreateRequest();
        $req->prefer('return=representation');

        // Get Orders from cart

        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        }
        if (null === $cart) {
            throw new $this->createNotFoundException();
        }
        if($cart->getCartLines()->count() == 0){
            throw new $this->createNotFoundException();
        }

        $lines = $cart->getCartLines();
        $items = [];
        $productName="";
        $qty = 1;
        $description = "";

        $currency = "USD";

        foreach ($lines as $line) {

            $qty = $line->getQuantity();
            /** @var Variant|null $variant */
            $variant = $line->getVariant();
            $description = "Desc Test";
            if ($variant !== null ) {
                /** @var Seller|null $seller */
                if ($variant->getProduct() != null) {
                    $seller = $variant->getProduct()->getSeller();
                    $productName = $variant->getProduct()->getName();
                    $price = $variant->getPrice();
                } else {
                    throw new NotFoundHttpException();
                }
            } else {
                $seller = $line->getProduct()->getSeller();
                $productName = $line->getProduct()->getName();
                $price = $line->getProduct()->getPrice();
            }

            $item = [
                'name' => $productName,
                'description' => $description,
                'unit_amount' =>
                    [
                        'currency_code' => $currency,
                        'value' => $price,
                    ],
                'quantity' => $qty,
            ];
            $items[]= $item;
        }

        $req->body =  array(
            'intent' => 'CAPTURE',
            'application_context' =>
                array(
                    'brand_name' => 'Tajeer',
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                ),
            'purchase_units' =>
                array(
                    0 =>
                        array(
                            'amount' =>
                                array(
                                    'currency_code' => $currency,
                                    'value' => $cart->total(),
                                    'breakdown' =>
                                        array(
                                            'item_total' =>
                                                array(
                                                    'currency_code' => $currency,
                                                    'value' => $cart->total(),
                                                ),
                                        ),
                                ),
                            'items' => $items,
                        ),
                ),
        );
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($req);
            dump($response);
            return new JsonResponse($response);
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
        }catch (HttpException $ex) {
            $response = $ex->getMessage();
            $response .= $ex->statusCode ;
            return new JsonResponse($response);
        }
    }

    /**
     * Capture Paypal Order
     * @Route("/paypal-capture-order", name="paypal_capture_order")
     * @param Request $req
     *
     * @return JsonResponse
     * @throws IOException
     */
    public function captureOrder(Request $req) {
        $clientId = "AZvdHvxArQ6e9N1xtCO-Hc8X6oFGVxizZfnkJvDEYGF4zP727c1NjVD5lGbbDxi4QjGraqrFQ_cxcZNm";
        $clientSecret = "EANsINF_SmRiv25XBD0g5dIDdKmcoQCIpiaGhcazF45itNfSSW1t-Hm1NXoF5C7QxCt094i8KjaCGgn8";
        $data = $req->request->all();
        $env = new SandboxEnvironment($clientId,$clientSecret);
        $client = new PayPalHttpClient($env);
        $request = new OrdersCaptureRequest($data['paymentID']);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return new JsonResponse($response);
        }catch (HttpException $ex) {
            $message = $ex->getMessage();
            return new JsonResponse($message,400);
        }
    }
}
