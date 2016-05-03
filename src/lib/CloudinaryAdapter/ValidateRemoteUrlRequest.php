<?php

namespace CloudinaryAdapter;

class ValidateRemoteUrlRequest
{

    protected $curlHandler;

    public function __construct($url)
    {
        $this->curlHandler = curl_init($url);
        $this->setCurlOptions();
    }

    public function validate()
    {
        $result = $this->execute();

        if ($result->responseCode == 200 && is_null($result->error)) {
            return true;
        }
        return false;
    }

    protected function execute()
    {
        curl_exec($this->curlHandler);

        $result = new \stdClass();
        $result->responseCode = $this->getResponseCode();
        $result->error = $this->getErrorMessage();

        curl_close($this->curlHandler);

        return $result;
    }

    protected function getResponseCode()
    {
        return curl_getinfo($this->curlHandler, CURLINFO_HTTP_CODE);
    }

    protected function getErrorMessage()
    {
        return curl_errno($this->curlHandler) ? curl_error($this->curlHandler) : null;
    }

    protected function setCurlOptions()
    {
        curl_setopt($this->curlHandler, CURLOPT_HEADER, 1);
        curl_setopt($this->curlHandler, CURLOPT_FAILONERROR, 1);
        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, 1);
    }
}
