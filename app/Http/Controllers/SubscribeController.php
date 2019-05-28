<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Common\PayPalModel;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Symfony\Component\HttpFoundation\Response;

class SubscribeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $accessToken;

    public function __construct()
    {
        $this->middleware('auth');
        $this->accessToken = 'A21AAGO-nwRuqiOW2Sw1hvYmeZ1qSc7O6Lfu7DFrQd3YuVryPSlwvKu8D0hxBZdiWSAp-F1XBk01PxTT8KAYQhntvhDGRRapg';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('subscribe');
    }

    public function subscribe(Request $request)
    {

        $plans = $request->plan;

        if (!empty($plans)) {
            foreach ($plans as $plan) {
                $requestData = $this->getRequestData($plan);
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.sandbox.paypal.com/v1/billing/subscriptions",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($requestData),
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer $this->accessToken",
                        "cache-control: no-cache",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    if ($http_status === Response::HTTP_OK || $http_status === Response::HTTP_CREATED || $http_status === Response::HTTP_ACCEPTED || $http_status === Response::HTTP_NON_AUTHORITATIVE_INFORMATION || $http_status === Response::HTTP_NO_CONTENT) {
                        $links = json_decode($response)->links;
                        foreach ($links as $link) {
                            if ($link->rel == 'approve') {
                                $hrefs[$plan] = $link->href;
                            }
                        }
                        $status[$plan] = json_decode($response)->status;
                    }
                }
            }
            if (!empty($status)) {
                return view('subscribe', compact('status', 'plans', 'hrefs'));

            }
        }

    }

    /**
     * @return array
     */
    protected function getRequestData($plan)
    {
        $user = auth()->user();
        $requestData = array(
            'plan_id' => $plan,
            'subscriber' =>
                array(
                    'name' =>
                        array(
                            'given_name' => $user->first_name,
                            'surname' => $user->last_name,
                        ),
                    'email_address' => $user->email,
                    'shipping_address' =>
                        array(
                            'name' =>
                                array(
                                    'full_name' => $user->first_name . ' ' . $user->last_name,
                                ),
                            'address' =>
                                array(
                                    'address_line_1' => $user->address_line_1,
                                    'address_line_2' => $user->address_line_2,
                                    'admin_area_2' => $user->admin_area_2,
                                    'admin_area_1' => $user->admin_area_1,
                                    'postal_code' => $user->postal_code,
                                    'country_code' => $user->country_code,
                                ),
                        ),
                ),
            'auto_renewal' => true,
        );
        return $requestData;
    }

    public function indexSdk()
    {

    }

    public function subscribeSdk()
    {

    }

    public function planSdk()
    {
        $plan = new Plan();
        $plan->setName('T-Shirt of the Month Club Plan')
            ->setDescription('Template creation.')
            ->setType('fixed');

// Set billing plan definitions
        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName('Regular Payments')
            ->setType('TRIAL')
            ->setFrequency('Month')
            ->setFrequencyInterval('2')
            ->setCycles('12')
            ->setAmount(new Currency(array('value' => 100, 'currency' => 'USD')));

        $paymentDefinition1 = new PaymentDefinition();
        $paymentDefinition1->setName('Regular Payments 1')
            ->setType('REGULAR')
            ->setFrequency('Month')
            ->setFrequencyInterval('3')
            ->setCycles('12')
            ->setAmount(new Currency(array('value' => 101, 'currency' => 'USD')));

// Set charge models
        $chargeModel = new ChargeModel();
        $chargeModel->setType('SHIPPING')
            ->setAmount(new Currency(array('value' => 10, 'currency' => 'USD')));
        $paymentDefinition->setChargeModels(array($chargeModel));

// Set merchant preferences
        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl('http://localhost:8000/processagreement')
            ->setCancelUrl('http://localhost:8000/cancel')
            ->setAutoBillAmount('yes')
            ->setInitialFailAmountAction('CONTINUE')
            ->setMaxFailAttempts('0')
            ->setSetupFee(new Currency(array('value' => 1, 'currency' => 'USD')));

        $plan->setPaymentDefinitions(array($paymentDefinition, $paymentDefinition1));
        $plan->setMerchantPreferences($merchantPreferences);

        try {
            $clientId = config('paypal.client_id');
            $clientSecret = config('paypal.secret');
            /**
             * All default curl options are stored in the array inside the PayPalHttpConfig class. To make changes to those settings
             * for your specific environments, feel free to add them using the code shown below
             * Uncomment below line to override any default curl options.
             */
// \PayPal\Core\PayPalHttpConfig::$defaultCurlOptions[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;
            /** @var \Paypal\Rest\ApiContext $apiContext */
            $apiContext = $this->getApiContext($clientId, $clientSecret);

            $createdPlan = $plan->create($apiContext);

            try {
                $patch = new Patch();
                $value = new PayPalModel('{"state":"ACTIVE"}');
                $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);
                $patchRequest = new PatchRequest();
                $patchRequest->addPatch($patch);
                $createdPlan->update($patchRequest, $apiContext);
                $plan = Plan::get($createdPlan->getId(), $apiContext);

                // Output plan id
                echo $plan->getId();
            } catch (PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            } catch (\Exception $ex) {
                die($ex);
            }
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (\Exception $ex) {
            die($ex);
        }

    }


    function getApiContext($clientId, $clientSecret)
    {
        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
        if(!defined("PP_CONFIG_PATH")) {
            define("PP_CONFIG_PATH", __DIR__);
        }
        */
        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );
        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration
        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                //'cache.FileName' => '/PaypalCache' // for determining paypal cache directory
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );
        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');
        return $apiContext;
    }
}
