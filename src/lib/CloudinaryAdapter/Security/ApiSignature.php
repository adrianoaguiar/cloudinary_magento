<?php

namespace CloudinaryAdapter\Security;

use Cloudinary;

class ApiSignature
{

    protected $apiSignature;

    protected function __construct(Secret $secret, array $params)
    {
        $this->apiSignature = Cloudinary::api_sign_request($params, (string) $secret);
    }

    public static function fromSecretAndParams(Secret $secret, array $params = array())
    {
        return new ApiSignature($secret, $params);
    }

    public function __toString()
    {
        return $this->apiSignature;
    }
}
