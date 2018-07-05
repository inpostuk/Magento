<?php

/**
 * (c) InPost UK Ltd <support@inpost.co.uk>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by NMedia Systems Ltd, <info@nmediasystems.com>
 */

class Inpost_Api_Client
{
    /**
     * URL to production API ednpoint.
     */
    const PRODUCTION_API_ENDPOINT = 'https://api-uk.easypack24.net/v4/';

    /**
     * URL to sandbox API endpoint.
     */
    const SANDBOX_API_ENDPOINT = 'https://stage-api-uk.easypack24.net/v4/';

    const LABEL_SIZE_A4 = 'A4';
    const LABEL_SIZE_A6 = 'A6P';
    const LABEL_FILE_FORMAT_PDF = 'pdf';
    const LABEL_FILE_FORMAT_ZPL = 'zpl';

    private $token;
    private $apiClient;
    private $apiEndPoint;
    private $merchantEmail;
    protected $labelParamsMapping = array(
        'pdf' => 'normal',
        'zpl' => 'a6p'
    );

    /**
     * Constructs the API client wrapper object.
     *
     * @param string $token Your Token
     * @param string $apiEndpoint API endpoint base URL to use. May be a production or a
     *                                       sandbox one. Defaults to production.
     * @param array $additionalClientConfig Array of additional configuration options for the underlying Zend client in the specified format
     *                                       accepted by the Zend HTTP client.
     */
    public function __construct($token, $apiEndpoint = self::PRODUCTION_API_ENDPOINT, $merchantEmail = '')
    {
        $this->token = $token;
        $this->apiEndPoint = $apiEndpoint;
        $this->merchantEmail = $merchantEmail;
        $this->apiClient = new Zend_Http_Client($apiEndpoint, array(
                'maxredirects' => 0,
                'timeout' => 300)
        );
    }

    /**
     * @param $receiverPhone
     * @param $machineId
     * @param $size
     * @param $weight
     * @param $receiverEmail
     * @param bool $jsonFormat
     * @return Inpost_Models_Parcel|Zend_Http_Response
     * @throws Inpost_Exception
     */
    public function createParcel($receiverPhone, $machineId, $size, $weight, $receiverEmail,  $jsonFormat = false)
    {
        $parcel = new Inpost_Models_Parcel();
        $path = "customers/{$this->merchantEmail}/parcels";
        $receiverPhone = $parcel->preparePhone($receiverPhone);
        if (strlen($receiverPhone) == 10) {
            $params = array(
                'receiver_phone' => $receiverPhone,
                'target_machine_id' => $machineId,
                'size' => strtoupper($size),
                'weight' => $weight,
                'receiver_email' => $receiverEmail
            );
            $response = $this->postOnEndpoint($path, $params);

            if ($jsonFormat) {
                return $response;
            }

            $body = (array)json_decode($response->getBody());

            $parcel->addData($body);

            return $parcel;
        } else {
            Throw new Inpost_Exception('Receiver number is not valid');
        }
    }

    /**
     * @param $parcelId
     * @param $filePath
     * @param $orderId
     * @param string $fileType
     */
    public function getLabel($parcelId, $filePath, $orderId, $fileType = self::LABEL_FILE_FORMAT_PDF)
    {
        $path = "/parcels/{$parcelId}/sticker";
        $params = array(
            'sticker_format' => ucfirst($fileType),
            'type' => $this->labelParamsMapping[$fileType]
        );
        $response = $this->getFromEndpoint($path, $params, true);
        file_put_contents($filePath . DIRECTORY_SEPARATOR .  $orderId . '_' .$parcelId . '.' . $fileType, (string)$response);
        return;
    }

    /**
     * @param $parcelId
     * @param $jsonFormat
     * @return mixed|string
     */
    public function getParcelData($parcelId, $jsonFormat) {
        $path = "/parcels/{$parcelId}";
        $response = $this->getFromEndpoint($path, array(), $jsonFormat);
        if ($jsonFormat) {
            return $response;
        } else {
            return $response;
        }

    }

    /**
     * @param $parcelId
     * @param bool $jsonFormat
     * @return bool|void|Zend_Http_Response
     */
    public function pay($parcelId, $jsonFormat = false)
    {
        $path = "/parcels/{$parcelId}/pay";
        $response = $this->postOnEndpoint($path);
        if ($jsonFormat) {
            return $response;
        }
        return;
    }


    /**
     *  Gets the list of all APMs
     *
     * @param boolean $jsonFormat , if this flag is true, response will be in json format
     *
     * @throws \Exception If the API returned a bussiness logic error
     *
     * @param bool $jsonFormat
     * @return array|bool
     * @throws Exception
     */
    public function getMachinesList($jsonFormat = false)
    {
        $path = 'machines';
        $machineList = array();
        $response = $this->getFromEndpoint($path);
        if ($jsonFormat) {
            return $response;
        }
        if (is_object($response)) {
            if (property_exists($response, '_embedded')) {
                if (property_exists($response->_embedded, 'machines')) {
                    $machinesListJson = $response->_embedded->machines;
                    foreach ($machinesListJson as $machineArray) {
                        $object = new Inpost_Models_Machine();
                        $object->addData((array)$machineArray);
                        $machineList[] = $object;
                    }
                    return $machineList;
                }
            }
        }

        return $machineList;
    }

    /**
     * @param $path
     * @param array $params
     * @param bool $jsonFormat
     * @return mixed|string
     * @throws Inpost_Exception
     */
    protected function getFromEndpoint($path, $params = array(), $jsonFormat = false)
    {
        $this->apiClient->resetParameters();
        $this->apiClient->setUri($this->apiEndPoint . $path);
        $this->apiClient->setMethod(Zend_Http_Client::GET);
        $this->apiClient->setHeaders("Authorization", "Bearer {$this->token}");
        $helper = Mage::helper('inpost_lockers');
        if ($helper->isDebug()) {
            $request = array();
            $request['request']['uri'] = $this->apiEndPoint . $path;
            $request['request']['method'] = 'GET';
            Mage::log(json_encode($request), 7, 'inpost_debug.log');
        }
        $response = $this->apiClient->request();
        if ($helper->isDebug()) {
            if (strpos($path, 'sticker') === false) {
                Mage::log($response->getBody(), 7, 'inpost_debug.log');
            }
        }
        if ($response->getStatus() == 200) {
            if ($jsonFormat) {
                return $response->getBody();
            }
            return json_decode($response->getBody());
        } else {
            Throw new Inpost_Exception(json_decode($response->getBody())->message);
        }
    }

    /**
     * @param $path
     * @param array $params
     * @return Zend_Http_Response
     * @throws Inpost_Exception
     */
    protected function postOnEndpoint($path, array $params = array())
    {
        $this->apiClient->setUri($this->apiEndPoint . $path);
        $this->apiClient->setMethod(Zend_Http_Client::POST);
        // authorization
        $this->apiClient->setHeaders("Authorization", "Bearer {$this->token}");
        $this->apiClient->setHeaders("Content-Type", "application/json");
        $this->apiClient->setRawData(json_encode($params));
        $helper = Mage::helper('inpost_lockers');
        if ($helper->isDebug()) {
            $request = array();
            $request['request']['uri'] = $this->apiEndPoint . $path;
            $request['request']['method'] = 'POST';
            $request['request']['raw_data'] = $params;
            Mage::log(json_encode($request), 7, 'inpost_debug.log');
        }
        $response = $this->apiClient->request();
        if ($helper->isDebug()) {
            Mage::log($response->getBody(), 7, 'inpost_debug.log');
        }
        if ($response->getStatus() == 201 || $response->getStatus() == 200) {
            return $response;
        } else {
            Throw new Inpost_Exception(json_decode($response->getBody())->message);
        }
    }
}