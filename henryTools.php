<?php

/**
 * henry
 *
 * Tools class to use with henry class
 *
 * @author  Rogério Albandes <rogerio.albandes@gmail.com>
 * @version 0.1
 * @package henry
 * @example example.php
 * @link    https://github.com/albandes/henry
 * @license GNU License
 *
 */

class henryTools extends henry
{

    /**
     *
     * Method to sent biometric data to device
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @param string $idBiometric Biometric Id
     * @param string $numBiometric Biometric number
     * @param string $biometricBase64 Biometric data in base64
     * @param bool $cleanBuffer  Clear output buffer true|false
     *
     * @since March 25, 2020
     *
     * @return string|true Error message or true if ok
     *
     */
    public function sendBiometricBase64($idBiometric,$numBiometric,$biometricBase64,$cleanBuffer=true)
    {

        if($cleanBuffer)
            $this->flushBuffer();

        $commandFull = "01+ED+00+T]".$idBiometric."}S}B}".$numBiometric."}512{" . $biometricBase64;

        $commandHexa = $this->generate($commandFull);
        $commandHexa  = str_replace(" ","",$commandHexa);

        $ret = $this->connect();

        if($ret !== true)
            return $ret;

        $ret = $this->writeSocket($this->hex2str($commandHexa));

        if (!$ret)
            return $ret ;

        $arrayRet = $this->listen();

        if( $arrayRet['success'] === false ){
            return $arrayRet['message'];
        } else {
            if ($arrayRet['err_or_version'] != '000') {
                return "Error: {$arrayRet['err_or_version']}";
            }

        }

        return true ;

    }

    /**
     *
     * Method to get biometric data from turnstile
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @param string $idBiometric Biometric Id
     * @param string $numBiometric Biometric number
     * @param bool $cleanBuffer  Clear output buffer true|false
     *
     * @since March 25, 2020
     *
     *  @return array [
     *                 'success'       => true|false,
     *                 'message'       => Error or success message
     *                 'data'          => Biometric data
     *                ]
     *
     */
    public function getBiometricByIdBase64($idBiometric, $numBiometric, $cleanBuffer=true)
    {

        if($cleanBuffer) $this->flushBuffer();
        $index = rand(0,9);

        $ret = $this->connect();
        if($ret !== true) {
            $arrayReturn['success'] = false;
            $arrayReturn['data']    = '';
            $arrayReturn['message'] = $ret;
            return $arrayReturn;
        }

        $commandFull = "01+RD+00+T]".$idBiometric."}S}B}".$numBiometric;

        $commandHexa = $this->generate($commandFull);

        $commandHexa  = str_replace(" ","",$commandHexa);

        $ret = $this->writeSocket($this->hex2str($commandHexa));
        if (!$ret) {
            $arrayReturn['success'] = false;
            $arrayReturn['data']    = '';
            $arrayReturn['message'] = $ret;
            return $arrayReturn;
        }

        $arrayRet = $this->listen();

        if( $arrayRet['success'] === false ){
            $arrayReturn['success'] = false;
            $arrayReturn['data']    = '';
            $arrayReturn['message'] = $arrayRet['message'];
            return $arrayReturn;
        } else {
            if ($arrayRet['err_or_version'] != '000') {
                $arrayReturn['success'] = false;
                $arrayReturn['data']    = '';
                $arrayReturn['message'] = $arrayRet['err_or_version'];
                return $arrayReturn;
            }

        }

        $data = explode(']', $arrayRet['data']);
        array_shift($data);

        $temp = explode('}',$data['0']);
        $fingerPrintBase64 =  explode('{',$temp['4']);

        $arrayReturn['success'] = true;
        $arrayReturn['message'] = 'Return fingerprint OK !!!';
        $arrayReturn['data']    = $fingerPrintBase64[1];
        return $arrayReturn;

    }


    /**
     *
     * Method to set configuration in device
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @param string $parameter Configuration parameter
     * @param string $value     Parameter value
     *
     * @since March 26, 2020
     *
     * @return string|true Error message or true if ok
     *
     */
    public function setConfig($parameter,$value)
    {
        $commandFull = "01+EC+00+".$parameter."[".$value;

        $commandHexa = $this->generate($commandFull);
        $commandHexa  = str_replace(" ","",$commandHexa);

        $ret = $this->connect();

        if($ret !== true)
            return $ret;

        $ret = $this->writeSocket($this->hex2str($commandHexa));

        if (!$ret)
            return $ret ;

        return true ;

    }


    public function freeAccess($message,$releaseTime,$cleanBuffer=true)
    {

        if($cleanBuffer)
            $this->flushBuffer();

        $commandFull = "01+REON+000+4]".$releaseTime."]".$message."]";

        $commandHexa = $this->generate($commandFull);
        $commandHexa  = str_replace(" ","",$commandHexa);

        $ret = $this->connect();

        if($ret !== true)
            return $ret;

        $ret = $this->writeSocket($this->hex2str($commandHexa));

        if (!$ret)
            return $ret ;

        return true ;

    }


    /**
     *
     * Method to delete biometric from device
     *
     * @author Rogerio Albandes <rogerio.albandeshelpdezk.cc>
     *
     * @param string $idBiometric Biometric Id
     * @param bool $cleanBuffer  Clear output buffer true|false
     *
     * @since March 25, 2020
     *
     * @return string|true Error message or true if ok
     *
     */
    public function deleteBiometric($idBiometric,$cleanBuffer=true)
    {

        if($cleanBuffer)
            $this->flushBuffer();

        $commandFull = "01+ED+00+E]".$idBiometric;

        $commandHexa = $this->generate($commandFull);
        $commandHexa  = str_replace(" ","",$commandHexa);

        $ret = $this->connect();
        if($ret !== true)
            return $ret;

        $ret = $this->writeSocket($this->hex2str($commandHexa));

        if (!$ret)
            return $ret ;

        $arrayRet = $this->listen();

        if( $arrayRet['success'] === false ){
            return $arrayRet['message'];
        } else {
            if ($arrayRet['err_or_version'] != '000') {
                return $arrayRet['err_or_version'];
            }

        }

        return true ;

    }

}