<?php


class NGL_SendinblueApiClient
{
    const API_BASE_URL = 'https://api.sendinblue.com/v3';
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    const CAMPAIGN_TYPE_EMAIL = 'email';
    const CAMPAIGN_TYPE_SMS = 'sms';
    const RESPONSE_CODE_OK = 200;
    const RESPONSE_CODE_CREATED = 201;
    const RESPONSE_CODE_ACCEPTED = 202;

    private $apiKey;
    private $lastResponseCode;

    /**
     * NGL_SendinblueApiClient constructor.
     */
    public function __construct( $api_key )
    {
        $this->apiKey = $api_key;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->get('/account');
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->get("/contacts/attributes");
    }

    /**
     * @param $type ,$name,$data
     * @return mixed
     */
    public function createAttribute($type, $name, $data)
    {
        return $this->post("/contacts/attributes/" . $type . "/" . $name, $data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getEmailTemplate($id)
    {
        return $this->get("/smtp/templates/" . $id);
    }

    /**
     * @param string $type
     * @param array $data
     * @return array
     */
    public function getAllCampaignsByType($type = self::CAMPAIGN_TYPE_EMAIL, $data = [])
    {
        $campaigns = [];

        if (!isset($data['offset'])) {
            $data['offset'] = 0;
        }

        do {
            if ($type === self::CAMPAIGN_TYPE_SMS) {
                $response = $this->getSmsCampaigns($data);
            } else {
                $response = $this->getEmailCampaigns($data);
            }

            if (isset($response['campaigns']) && is_array($response['campaigns'])) {
                $campaigns = array_merge($campaigns, $response['campaigns']);
                $data['offset']++;
            }
        } while (!empty($response['campaigns']));

        return $campaigns;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getEmailCampaigns($data)
    {
        return $this->get("/emailCampaigns", $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getSmsCampaigns($data)
    {
        return $this->get("/smsCampaigns", $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getEmailTemplates($data)
    {
        return $this->get("/smtp/templates", $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function sendEmail($data)
    {
        return $this->post("/smtp/email", $data);
    }

    /**
     * @param $id ,$data
     * @return mixed
     */
    public function sendTransactionalTemplate($id, $data)
    {
        return $this->post("/smtp/templates/" . $id . "/send", $data);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getUser($email)
    {
        return $this->get("/contacts/" . urlencode($email));
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        return $this->post("/contacts", $data);
    }

    /**
     * @return mixed
     */
    public function getSenders()
    {
        return $this->get("/senders");
    }

    /**
     * @param $email ,$data
     * @return mixed
     */
    public function updateUser($email, $data)
    {
        return $this->put("/contacts/" . $email, $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createList($data)
    {
        return $this->post("/contacts/lists", $data);
    }

    /**
     * @param $data
     * @return mixed
     */
	public function createCampaign( $data ) {
		return $this->post( '/emailCampaigns', $data );
	}

    /**
     * @param $data
     * @return mixed
     */
	public function deleteCampaign( $campaign_id ) {
		return $this->delete( '/emailCampaigns/' . $campaign_id );
	}

    /**
     * @param $data
     * @return mixed
     */
	public function sendCampaign( $campaign_id ) {
		return $this->post( '/emailCampaigns/' . $campaign_id . '/sendNow' );
	}

    /**
     * @param $data
     * @return mixed
     */
	public function sendCampaignTest( $campaign_id, $data ) {
		return $this->post( '/emailCampaigns/' . $campaign_id . '/sendTest', $data );
	}

    /**
     * @param $data
     * @return mixed
     */
    public function getLists($data)
    {
        return $this->get("/contacts/lists", $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getAllLists()
    {
        $lists = array("lists" => array(), "count" => 0);
        $offset = 0;
        $limit = 50;
        do {
            $list_data = $this->getLists(array('limit' => $limit, 'offset' => $offset));
            if (isset($list_data["lists"]) && is_array($list_data["lists"])) {
                $lists["lists"] = array_merge($lists["lists"], $list_data["lists"]);
                $offset += 50;
                $lists["count"] = $list_data["count"];
            }
        } while (!empty($lists['lists']) && count($lists["lists"]) < $list_data["count"]);

        return $lists;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function importContacts($data)
    {
        return $this->post('/contacts/import', $data);
    }

    /**
     * @param $endpoint
     * @param array $parameters
     * @return mixed
     */
    public function get($endpoint, $parameters = [])
    {
        if ($parameters) {
            foreach ($parameters as $key => $parameter) {
                if (is_bool($parameter)) {
                    // http_build_query converts bool to int
                    $parameters[$key] = $parameter ? 'true' : 'false';
                }
            }
            $endpoint .= '?' . http_build_query($parameters);
        }
        return $this->makeHttpRequest(self::HTTP_METHOD_GET, $endpoint);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function post($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::HTTP_METHOD_POST, $endpoint, $data);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function delete($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::HTTP_METHOD_DELETE, $endpoint, $data);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return mixed
     */
    public function put($endpoint, $data = [])
    {
        return $this->makeHttpRequest(self::HTTP_METHOD_PUT, $endpoint, $data);
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $body
     * @return mixed
     */
    private function makeHttpRequest($method, $endpoint, $body = [])
    {
        $url = self::API_BASE_URL . $endpoint;

        $args = [
            'timeout' => 10000,
            'method' => $method,
            'headers' => [
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
        ];

        if ($method != self::HTTP_METHOD_GET && $method != self::HTTP_METHOD_DELETE) {
            if (isset($body['listIds'])) {
                $body['listIds'] = $this->getListsIds($body['listIds']);
            }
            if (isset($body['unlinkListIds'])) {
                $body['unlinkListIds'] = $this->getListsIds($body['unlinkListIds']);
            }
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);
        $this->lastResponseCode = wp_remote_retrieve_response_code($response);

        if (is_wp_error($response)) {
            $data = [
                'code' => $response->get_error_code(),
                'message' => $response->get_error_message()
            ];
        } else {
            $data = json_decode(wp_remote_retrieve_body($response), true);
        }

        return $data;
    }

    private function getListsIds($listIds)
    {
        return array_unique(array_values(array_map('intval', (array)$listIds)));
    }

    /**
     * @return int
     */
    public function getLastResponseCode()
    {
        return $this->lastResponseCode;
    }
}