<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class YClientsService
{
    private $http;
    private $baseURL;
    private $apiToken;
    private $userToken;
    private $login;
    private $password;
    private $companyId;
    private $authorizationHeader;

    function __construct()
    {
        $this->baseURL = env('YCLIENTS_BASEURL');
        $this->apiToken = env('YCLIENTS_APITOKEN');
        $this->login = env('YCLIENTS_LOGIN');
        $this->password = env('YCLIENTS_PASSWORD');
        $this->companyId = env('YCLIENTS_COMPANY_ID');

        $this->authorizationHeader = "Bearer {$this->apiToken}";
        $this->http = $this->getClient($this->authorizationHeader);
    }

    private function getClient(string $authorizationHeader)
    {
        return Http::withoutVerifying()
            ->withHeaders([
                'Authorization' =>  $authorizationHeader
            ])
            ->timeout(5)
            ->retry(3, 5000);
    }

    public function authorize()
    {
        $response = $this->http->post("{$this->baseURL}/auth", [
            'login' => $this->login,
            'password' => $this->password,
        ])
            ->throw()
            ->json();

        if (isset($response['user_token'])) {
            $this->userToken = $response['user_token'];
            $this->authorizationHeader = "Bearer {$this->apiToken}, User {$this->userToken}";
            $this->http = $this->getClient($this->authorizationHeader);

            return true;
        }
        throw new RequestException($response);
    }

    public function getStaff()
    {
        return $this->http->get("{$this->baseURL}/staff/{$this->companyId}/")
            ->throw()
            ->json();
    }

    public function findStaff(int $origin_id)
    {
        return $this->http->get("{$this->baseURL}/staff/{$this->companyId}/{$origin_id}")
            ->throw()
            ->json();
    }

    public function createStaff(array $data)
    {
        return $this->http->post("{$this->baseURL}/staff/{$this->companyId}/", [
            "name" => $data['name'],
            "specialization" => $data['specialization'],
        ])
            ->throw()
            ->json();
    }

    public function getRecords(string $date)
    {
        $page = 1;
        $pages = 1;
        $perPage = 300;
        $response = $this->http->get("{$this->baseURL}/records/{$this->companyId}/", [
            'page' => $page,
            'count' => $perPage,
            'start_date' => $date,
            'end_date' => $date,
        ])
            ->throw()
            ->json();

        if ($response['count'] > $perPage) {
            $pages = intdiv($response['count'], $perPage) + 1;
            for ($page = 2; $page <= $pages; $page++) {
                $data = $this->http->get("{$this->baseURL}/records/{$this->companyId}/", [
                    'page' => $page,
                    'count' => $perPage,
                    'start_date' => $date,
                    'end_date' => $date,
                ])
                    ->throw()
                    ->json();
                array_push($response['data'], $data['data']);
            }
        }

        return $response['data'];
    }

    public function getServices(int $staff_id = null)
    {
        $queryParams = !empty($staff_id) ? ['staff_id' => $staff_id] : null;

        return $this->http->get("{$this->baseURL}/services/{$this->companyId}", $queryParams)
            ->throw()
            ->json();
    }

    public function createService(array $data)
    {
        $body = [
            "title" => $data['title'],
            "category_id" => intval($data['category_id']),
            "price_min" => floatval($data['price']),
            "price_max" => floatval($data['price']),
            "discount" => 0,
            "comment" => "Услуга создана из CRM",
            "weight" => 6,
            "active" => 1,
            "staff" => [
                [
                    "id" => $data['staff_id'],
                    "seance_length" => intval($data['seance_length'])
                ]
            ]
        ];

        return $this->http->post("{$this->baseURL}/services/{$this->companyId}/", $body)
            ->throw()
            ->json();
    }

    public function getTransactions(int $recordId, int $visitId)
    {
        return $this->http->get("{$this->baseURL}/timetable/transactions/{$this->companyId}", [
            'record_id' => $recordId,
            'visit_id' => $visitId
        ])
            ->throw()
            ->json();
    }

    public function getServiceCategory(int $staff_id = null)
    {
        $queryParams = !empty($staff_id) ? ['staff_id' => $staff_id] : null;

        return $this->http->get("{$this->baseURL}/service_categories/{$this->companyId}", $queryParams)
            ->throw()
            ->json();
    }

    public function createServiceCategory(array $data)
    {
        return $this->http->post("{$this->baseURL}/service_categories/{$this->companyId}/", [
            "title" => $data['title'],
            "staff" => [$data['staff_id']]
        ])
            ->throw()
            ->json();
    }
}
