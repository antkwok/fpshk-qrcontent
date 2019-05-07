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


    private $fps_id; // support fps_id only, will support email or tel later
    private $amount = 0;

    public function __construct(String $fps_id, $amount = 0)
    {
        $this->fps_id = $fps_id;
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
        $part1 = $this->getGlobalUniqueIndentifier();
        $part2 = $this->getMerchantAccountInfomation();

        return self::MERCHANT_FPS_IN_USE_ID . $this->addLenInUsePrefixForValue($part1 . $part2);
    }

    public function getGlobalUniqueIndentifier()
    {
        return "00" . $this->addLenInUsePrefixForValue("hk.com.hkicl");
    }

    public function getMerchantAccountInfomation()
    {
        return "02" . $this->addLenInUsePrefixForValue($this->fps_id);
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

    public function getCRC($content)
    {
        $crc = $this->hash($content . self::CRC_ID . "04");
        $crc = base_convert($crc, 10, 16);
        $crc = strtoupper($crc);
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
        if (!empty($this->amount)) { // dynamic payment
            $string .= $this->getTransactionAmount();
        }
        $string .= $this->getCountryCode();
        $string .= $this->getMerchantName();
        $string .= $this->getMerchantCity();
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
