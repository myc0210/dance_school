<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;
    use PayPal\Rest\ApiContext;
    use PayPal\Auth\OAuthTokenCredential;
    use PayPal\Api\PaymentExecution;
    use PayPal\Api\Amount;
    use PayPal\Api\Details;
    use PayPal\Api\Item;
    use PayPal\Api\ItemList;
    use PayPal\Api\Payer;
    use PayPal\Api\Payment;
    use PayPal\Api\RedirectUrls;
    use PayPal\Api\Transaction;
    use PayPal\Api\ShippingAddress;
    use Paypal\Core\PayPalConfigManager;

    class Paypal extends ActiveRecord
    {
        public static function tableName()
        {
            return '{{%paypal}}';
        }

        public function behaviors()
        {
            return [
                TimestampBehavior::className(),
            ];
        }

        public function rules()
        {
            return [
                [['client_id', 'client_secret'], 'required']
            ];
        }

        public static function testCredential($clientId, $clientCredit, $environment = 'sandbox') {
            $oAuthTokenCredential = new OAuthTokenCredential(
                $clientId,
                $clientCredit
            );
            PayPalConfigManager::getInstance()->addConfigs(array(
                'http.ConnectionTimeOut' => 30,
                'http.Retry' => 1,
                'mode' => $environment,
                'log.LogEnabled' => true,
                'log.FileName' => 'paypal.log',
                'log.LogLevel' => 'INFO'
            ));

            $config = PayPalConfigManager::getInstance()->getConfigHashmap();
            $accessToken = $oAuthTokenCredential->getAccessToken($config);

            return $accessToken;
        }

        public static function getApiContext($clientId, $clientCredit, $environment = 'sandbox')
        {
            $apiContext = new ApiContext(new OAuthTokenCredential(
                $clientId,
                $clientCredit
            ));

            $apiContext->setConfig(array(
                'http.ConnectionTimeOut' => 30,
                'http.Retry' => 1,
                'mode' => $environment,
                'log.LogEnabled' => true,
                'log.FileName' => 'paypal.log',
                'log.LogLevel' => 'INFO'
            ));

            return $apiContext;
        }

        public static function getClientAuthorizationById($id)
        {
            if($paypalRecord = static::findOne($id)) {
                return $paypalRecord;
            }

            return null;
        }

        public static function checkout($paymentObj, $clientId, $clientCredit, $environment = 'sandbox')
        {
            $apiContext = static::getApiContext($clientId, $clientCredit, $environment);

            $payer = new Payer();
            $payer->setPaymentMethod("paypal");


            $items = [];
            $currency = '';
            foreach($paymentObj['items'] as $item) {
                $pItem = new Item();
                $pItem->setName($item['name'])
                    ->setCurrency('SGD') // Need to improve in the furture
                    ->setQuantity($item['quantity'])
                    ->setPrice($item['price']);
                $currency = 'SGD'; // Need to improve in the furture
                $items[] = $pItem;
            }

//            $shippingAddress = new ShippingAddress();
//            $shippingAddress->setRecipientName($paymentObj['shippingInfo']['first_name'] . $paymentObj['shippingInfo']['last_name']);
//            $shippingAddress->setLine1($paymentObj['shippingInfo']['address']);
//            $shippingAddress->setCity($paymentObj['shippingInfo']['country']);
//            $shippingAddress->setCountryCode($paymentObj['shippingInfo']['country_code']);
//            $shippingAddress->setPostalCode($paymentObj['shippingInfo']['postcode']);

            $itemList = new ItemList();
            $itemList->setItems($items);
//            $itemList->setShippingAddress($shippingAddress);

            $details = new Details();
//            $details->setShipping(0.00)
            $details
                ->setTax(0.00)
                ->setSubtotal($paymentObj['grand_total']);

            $amount = new Amount();
            $amount->setCurrency($currency)
                ->setTotal($paymentObj['total'])
                ->setDetails($details);


            $transaction = new Transaction();
            $transaction
                ->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription("")
                ->setInvoiceNumber($paymentObj['invoice_no']);

            $app = Yii::$app;
            $redirectUrls = new RedirectUrls();
            $redirectUrls
                ->setReturnUrl($app->params['paypalReturnSuccess'])
                ->setCancelUrl($app->params['paypalReturnCancel']);

            $payment = new Payment();
            $payment
                ->setIntent("sale")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));
            $request = clone $payment;

            try {
                $payment->create($apiContext);
            } catch (Exception $ex) {
                ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
                exit(1);
            }

            $approvalUrl = $payment->getApprovalLink();
//            $queryString = parse_url($approvalUrl, PHP_URL_QUERY);
//            $pairs = explode('&', $queryString);
//            foreach($pairs as $key => $value) {
//                $pair = explode('=', $value);
//                if($pair[0] == 'token') {
//                    $token = $pair[1];
//                }
//            }
//            //Save token clientID,clientCredit to cache.
//            $cache = Yii::$app->cache;
//            $cache->set($token, "{$clientId},{$clientCredit}");
            return $approvalUrl;
        }

        public static function executePayment($token, $paymentId, $payerId) {
            $cache = Yii::$app->cache;

            if($compositedCredit = $cache->get($token)) {
                $compositedCredit = explode(',', $compositedCredit);
                $clientId = $compositedCredit[0];
                $clientSecret = $compositedCredit[1];
            } else {
                throw new \Exception('Token is invalid.');
            }

            $apiContext = static::getApiContext($clientId, $clientSecret);

            $payment = Payment::get($paymentId, $apiContext);;
            $paymentExecution = new PaymentExecution();
            $paymentExecution->setPayerId($payerId);
            $payment = $payment->execute($paymentExecution, $apiContext);

            return $payment->getState();
        }

        public static function showPaymentDetail($paymentId, $clientId, $clientSecret) {
            $apiContext = static::getApiContext($clientId, $clientSecret);
            $payment = Payment::get($paymentId, $apiContext);
            return $payment;
        }
    }