<?php

namespace MailerLiteApi\Api;

use MailerLiteApi\Common\ApiAbstract;

class Me extends ApiAbstract {

    protected $endpoint = 'me';

    public function get($fields = [])
    {
        $response = $this->restClient->get($this->endpoint, []);

        return $response['body'];
    }

}