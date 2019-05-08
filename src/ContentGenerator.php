<?php

namespace AntKwok\FPSHKQrContent;

class ContentGenerator
{
    // Payload Format Indicator
    const PAYLOAD_ID = "00";
    // Point of Initiation
    const POINT_OF_INIT_METHOD_ID = "01";
    // CRC-16-CCITT
    const CRC_ID = "63";
    // Merchant Use FPS id
    const MERCHANT_FPS_IN_USE_ID = "26";


    // Merchant Category Code
    const MERCHANT_CAT_ID = "52";
    // Merchant Country Code
    const MERCHANT_COUNTRY_ID = "58";
    // Merchant Name Code
    const MERCHANT_NAME_ID = "59";
    // Merchant City
    const MERCHANT_CITY_ID = "60";

    // Transaction currency
    const TRANSACTION_CURRENCY_ID = "53";
    // Transaction Account
    const TRANSACTION_AMOUNT_ID = "54";


    // Additional data
    const ADDITIONAL_DATA_ID = "62";

    // Sub Additional data
    const ADDITIONAL_BILL_NUMBER_ID = "01";
    const ADDITIONAL_MOBILE_NUMBER_ID = "02";
    const ADDITIONAL_STORE_LABEL_ID = "03";
    const ADDITIONAL_LOYALTY_NUMBER_ID = "04";
    const ADDITIONAL_REFERENCE_LABEL_ID = "05";
    const ADDITIONAL_CUSTOMER_LABEL_ID = "06";
    const ADDITIONAL_TERMINAL_LABEL_ID = "07";



    private $fpsId; // support fps_id only, will support email or tel later
    private $amount = 0;

    private $billNumber = null;
    private $mobileNumber = null;
    private $storeLabel = null;
    private $loyaltyNumber = null;
    private $referenceLabel = null;
    private $customerLabel = null;
    private $terminalLabel = null;


    /**
     * @param null $billNumber
     */
    public function setBillNumber($billNumber)
    {
        $this->billNumber = $billNumber;
    }

    /**
     * @param null $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @param null $storeLabel
     */
    public function setStoreLabel($storeLabel)
    {
        $this->storeLabel = $storeLabel;
    }

    /**
     * @param null $loyaltyNumber
     */
    public function setLoyaltyNumber($loyaltyNumber)
    {
        $this->loyaltyNumber = $loyaltyNumber;
    }

    /**
     * @param null $referenceLabel
     */
    public function setReferenceLabel($referenceLabel)
    {
        $this->referenceLabel = $referenceLabel;
    }

    /**
     * @param null $customerLabel
     */
    public function setCustomerLabel($customerLabel)
    {
        $this->customerLabel = $customerLabel;
    }

    /**
     * @param null $terminalLabel
     */
    public function setTerminalLabel($terminalLabel)
    {
        $this->terminalLabel = $terminalLabel;
    }

    public function __construct(String $fpsId, $amount = 0)
    {
        $this->fpsId = $fpsId;
        $this->amount = $amount;
    }

    public function addLenInUsePrefixForValue(String $value)
    {
        $len = strlen($value);
        $len = substr("00" . $len, -2);
        return $len . $value;
    }

    public function getPayload()
    {
        return self::PAYLOAD_ID . $this->addLenInUsePrefixForValue("01");
    }

    public function getPointOfInitiationMethod()
    {
        if (empty($this->amount)) {
            $method = "11"; // static payment
        } else {
            $method = "12"; // dynamic payment
        }
        return self::POINT_OF_INIT_METHOD_ID . $this->addLenInUsePrefixForValue($method);
    }

    public function getMerchantInformation()
    {
        $part1 = $this->getGlobalUniqueIdentifier();
        $part2 = $this->getMerchantAccountInformation();

        return self::MERCHANT_FPS_IN_USE_ID . $this->addLenInUsePrefixForValue($part1 . $part2);
    }

    public function getGlobalUniqueIdentifier()
    {
        return "00" . $this->addLenInUsePrefixForValue("hk.com.hkicl");
    }

    public function getMerchantAccountInformation()
    {
        return "02" . $this->addLenInUsePrefixForValue($this->fpsId);
    }

    public function getMerchantCategoryCode()
    {
        return self::MERCHANT_CAT_ID . $this->addLenInUsePrefixForValue("0000");
    }

    public function getTransactionCurrency()
    {
        return self::TRANSACTION_CURRENCY_ID . $this->addLenInUsePrefixForValue("344");
    }

    public function getTransactionAmount()
    {
        if (empty($this->amount)) return "";
        return self::TRANSACTION_AMOUNT_ID . $this->addLenInUsePrefixForValue($this->amount);
    }

    public function getCountryCode()
    {
        return self::MERCHANT_COUNTRY_ID . $this->addLenInUsePrefixForValue("HK");
    }

    public function getMerchantName()
    {
        return self::MERCHANT_NAME_ID . $this->addLenInUsePrefixForValue("NA");
    }

    public function getMerchantCity()
    {
        return self::MERCHANT_CITY_ID . $this->addLenInUsePrefixForValue("HK");
    }

    public function getAdditionalData()
    {
        $result = "";
        $result .= $this->getAdditionalBillNumber();
        $result .= $this->getAdditionalMobileNumber();
        $result .= $this->getAdditionalStoreLabel();
        $result .= $this->getAdditionalLoyaltyNumber();
        $result .= $this->getAdditionalReferenceLabel();
        $result .= $this->getAdditionalCustomerLabel();
        $result .= $this->getAdditionalTerminalLabel();

        if (empty($result)) return "";

        return self::ADDITIONAL_DATA_ID . $this->addLenInUsePrefixForValue($result);
    }

    private function getAdditionalBillNumber()
    {
        if (empty($this->billNumber)) return "";
        return self::ADDITIONAL_BILL_NUMBER_ID . $this->addLenInUsePrefixForValue($this->billNumber);
    }

    private function getAdditionalMobileNumber()
    {
        if (empty($this->mobileNumber)) return "";
        return self::ADDITIONAL_MOBILE_NUMBER_ID . $this->addLenInUsePrefixForValue($this->mobileNumber);
    }

    private function getAdditionalStoreLabel()
    {
        if (empty($this->storeLabel)) return "";
        return self::ADDITIONAL_STORE_LABEL_ID . $this->addLenInUsePrefixForValue($this->storeLabel);
    }

    private function getAdditionalLoyaltyNumber()
    {
        if (empty($this->loyaltyNumber)) return "";
        return self::ADDITIONAL_LOYALTY_NUMBER_ID . $this->addLenInUsePrefixForValue($this->loyaltyNumber);
    }

    private function getAdditionalReferenceLabel()
    {
        if (empty($this->referenceLabel)) return "";
        return self::ADDITIONAL_REFERENCE_LABEL_ID . $this->addLenInUsePrefixForValue($this->referenceLabel);
    }

    private function getAdditionalCustomerLabel()
    {
        if (empty($this->customerLabel)) return "";
        return self::ADDITIONAL_CUSTOMER_LABEL_ID . $this->addLenInUsePrefixForValue($this->customerLabel);
    }

    private function getAdditionalTerminalLabel()
    {
        if (empty($this->terminalLabel)) return "";
        return self::ADDITIONAL_TERMINAL_LABEL_ID . $this->addLenInUsePrefixForValue($this->terminalLabel);
    }

    public function getCRC($content)
    {
        $crc = $this->hash($content . self::CRC_ID . "04");
        $crc = base_convert($crc, 10, 16);
        $crc = strtoupper($crc);
        $crc = substr("0000" . $crc, -4);
        return self::CRC_ID . $this->addLenInUsePrefixForValue($crc);
    }

    public function generateQrCodeContent()
    {
        $string = "";
        $string .= $this->getPayload();
        $string .= $this->getPointOfInitiationMethod();
        $string .= $this->getMerchantInformation();
        $string .= $this->getMerchantCategoryCode();
        $string .= $this->getTransactionCurrency();
        $string .= $this->getTransactionAmount();
        $string .= $this->getCountryCode();
        $string .= $this->getMerchantName();
        $string .= $this->getMerchantCity();
        $string .= $this->getAdditionalData();
        $string .= $this->getCRC($string);
        return $string;
    }

    /**
     * CRC-16-CCITT
     * @param $data
     * @return int
     */
    public function hash($data)
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++)
        {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }

}
