<?php 

namespace App\Libraries;

class Routing {

    public static $must_login = false;
    public static $httpCode = 200;
    public static $invalidationList = [];
    public static $pagination = [];
    /**
     * Invalidation list
     * 
     * @param array $data
     * 
     * @return void
     */
    public static function invalidation($data) {
        self::$invalidationList = $data;
    }

    /**
     * Denied
     * 
     * @return array
     */
    public static function denied($data = 'You are not authorized to perform the action on this resource.') {
        $response = [
            'status' => 'error',
            'data' => $data,
            'statusCode' => 403,
            'must_login' => self::$must_login,
        ];
        return $response;
    }

    /**
     * Denied
     * 
     * @return array
     */
    public static function unauthorized($data = 'You are not authorized to access this resource.', $message = []) {
        $response = [
            'status' => 'error',
            'data' => $data,
            'statusCode' => 200,
            'must_login' => true,
        ];
        if(!empty($message)) {
            $response['message'] = $message;
        }
        return $response;
    }

    /**
     * Error
     * 
     * @param string $message
     * @return array
     */
    public static function error($message, $record = [], $statusCode = 400) {
        return [
            'status' => $statusCode == 400 ? 'error' : 'success',
            'data' => $message,
            'record' => $record,
            'statusCode' => $statusCode
        ];
    }

    /**
     * Success
     * 
     * @param array $data
     * @return array
     */
    public static function success($data = [], $message = 'success', $statusCode = 'success') {
        return [
            'status' => $statusCode,
            'data' => $data,
            'message' => $message,
            'statusCode' => 200,
            'invalidate' => self::$invalidationList,
            'pagination' => self::$pagination
        ];
    }

    /**
     * Not found
     * 
     * @param string $resource
     * 
     * @return array
     */
    public static function notFound($resource = 'Resource', $useInfo = false) {
        return [
            'status' => 'error',
            'data' => $useInfo ? $resource : "{$resource} not found",
            'statusCode' => 404
        ];
    }

    /**
     * Created
     * 
     * @param array $data
     * @return array
     */
    public static function created($data = []) {
        return [
            'status' => 'success',
            'data' => $data['data'] ?? $data,
            'record' => $data['record'] ?? [],
            'statusCode' => 201,
            'invalidate' => self::$invalidationList
        ];
    }

    /**
     * Deleted
     * 
     * @return array
     */
    public static function deleted() {
        return [
            'status' => 'success',
            'data' => 'Resource deleted successfully.',
            'statusCode' => 200,
            'invalidate' => self::$invalidationList
        ];
    }

    /**
     * Updated
     * 
     * @param array $data
     * @return array
     */
    public static function updated($data = [], $record = []) {
        return [
            'status' => 'success',
            'data' => $data,
            'record' => $record,
            'statusCode' => 200,
            'invalidate' => self::$invalidationList
        ];
    }

    /**
     * Make the request
     * 
     * @param array $payload
     * @param string $path
     * 
     * @return object
     */
    public static function curlRequest($payload, $path = 'lambda', $method = 'POST') {

        // set the path url
        $pathUrl = [
            'insight' => getenv('PAGE_SPEED_URL'),
            'lambda' => getenv('LAMBDA_URL'),
        ];
        // convert payload to params
        if ($method == 'GET') {
            if (!empty($payload) && is_array($payload)) {
                $params = http_build_query($payload, '', '&');
                $pathUrl[$path] .= '?' . $params;
            } elseif (!empty($payload) && is_string($payload)) {
                $pathUrl[$path] .= '?' . $payload;
            }
        }
        // set the period to month if not set
        if(empty($payload['period'])) {
            $payload['period'] = 'month';
        }
        // set the date to month if not set
        if(!empty($payload['date']) && in_array($payload['date'], ['today', 'month'])) {
            $payload['date'] = date('Y-m-d');
            $payload['period'] = $payload['date'] == 'today' ? 'day' : 'month';
        }
        // get the client
        $client = service('curlrequest');

        if(strpos($pathUrl[$path], '$') !== false) {
            foreach($payload as $key => $value) {
                $pathUrl[$path] = str_ireplace($key, $value, $pathUrl[$path]);
            }
        }

        // make the request
        if ($method == 'POST') {
            $response = $client->request($method, $pathUrl[$path] , [
                'json' => $payload
            ]);
        } elseif($method == 'GET') {
            $response = $client->request($method, $pathUrl[$path]);
        }
        
        // decode the response
        $response = json_decode($response->getBody(), true);

        // return the response
        return !empty($response) ? $response : [];

    }

}
?>
